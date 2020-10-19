<?php

namespace App\Http\Controllers\Export;

use App\Http\Kernel;
use App\Model\Bill;
use App\Model\Export;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;



use App\Http\Controllers\Report\ReportController;
use Illuminate\Support\Facades\Storage;


class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $backup_week = Export::where('type', 1)
            ->orderBy('updated_at', 'desc')->get();

        $backup_month = Export::where('type', 2)
            ->orderBy('updated_at', 'desc')->get();

        return view('export', compact('backup_week', 'backup_month'));
    }

    public function export(Request $request){

        set_time_limit(0);

        $report = new ReportController();
        $request->branch_id = "0";
        $request->dump = true;
        $request->report_id = 1;

        $data = $report->getReport($request);

        //dd($data);
        $json = json_encode($this->pre_export_report($request, $data));

        if($json){
            return $json;
        }

        return '{"status" : "cant found data"}';


    }

    public function dropLenght(Request $request){


        $start = explode('/',$request->date_start);
        $end = explode('/',$request->date_end);

        //dd($data);

        $start_bc = Carbon::create($start[2] - 543 , $start[1], $start[0], 0);
        $end_bc = Carbon::create($end[2] - 543 , $end[1], $end[0], 23, 59, 59);


        $deletedBill = Bill::whereBetween('date_', [$start_bc, $end_bc])->delete();


        if($deletedBill){
            return '{"status" : "you delete success" }';
        }

        return '{"status" : "cant found data"}';

    }

    public function checkLength(Request $request){

        $request->dump = true;
        $request->report_id = 1;
//        $request->date_start = "05/01/2562";
//        $request->date_end = "01/07/2562";

        $start = explode('/',$request->date_start);
        $end = explode('/',$request->date_end);

        $start_bc = Carbon::create($start[2] - 543 , $start[1], $start[0], 0);
        $end_bc = Carbon::create($end[2] - 543 , $end[1], $end[0], 23, 59, 59);

        $chunkList = $this->getChunkList($start_bc , $end_bc);
        $bill = Bill::whereBetween('date_', [$start_bc, $end_bc])->count();

        $res = (object)[];
        $res->chunkList = $chunkList;
        $res->billength = $bill + 1;
        $res->_length = count($chunkList);

        return json_encode($res);

    }


    public function getChunkList($start_bc , $end_bc){


        $length = $start_bc->diffInMonths($end_bc) + 1;

        $i = 0;

        $chunk_head = array($start_bc);
        $chunk_tail = array();


        while($i < $length){


            $date_head = $chunk_head[$i];
            $date_tail = $date_head->copy()->endOfMonth();
            $date_head = $date_tail->copy()->addDay()->startOfDay();
            !($i < $length) ?: array_push($chunk_tail, $date_tail);
            !($i < ($length - 1)) ?: array_push($chunk_head, $date_head);

            $i++;

        }

        $chunk = [];

        foreach ($chunk_tail as $key => $val)
        {
            $obj = (object)[];
            $obj->date_start = $chunk_head[$key]->addYear(543)->format('d/m/Y');
            $obj->date_end = $chunk_tail[$key]->addYear(543)->format('d/m/Y');
            $obj->name = $val->format('m-Y');

            array_push($chunk, $obj);

        }

        return $chunk;

    }

    public function pre_export_report($request, $report){

        $header = $report->header;
        $data = $report->data;

        $pre_data = (object)[];
        $pre_data->data = $this->create_sheet($header, $data);
        $pre_data->sheetName = $request->name;


        return $pre_data;

    }

    public function create_sheet($header_list, $data_list){

        $sheet = [];
        $headers = [];

        foreach ($header_list as $header_key => $header){

            //push_header
            $_item = (object)[];
            $_item->text = $header->name;
            array_push($headers , $_item);

        }

        array_push($sheet, $headers );


        foreach ($data_list as $data_key => $data){

            $_data = (array) $data;
            $_data = array_values($_data);


            $row = array_map( function($item) {

                $_item = (object)[];
                $_item->text = $item;
                return $_item;

            }, $_data);

            array_push($sheet, $row );

        }

        return $sheet;

    }


    public function cron_backup(){


        $this->backup_by_period('day', 7);
        $this->backup_by_period('month', 1);

    }

    public function backup_by_period($period, $period_amount){


        switch ($period) {
            case 'day':
                $type = 1;
                break;
            case 'month':
                $type = 2;
                break;
            default:
                return true;
        }


        if ($type){

            $time_lasted_backup = Export::where('type', $type)
                ->orderBy('updated_at', 'desc')->first();

            if ($time_lasted_backup) {

                $lasted = $time_lasted_backup->updated_at;
                $now = Carbon::now();

                $diff = null;

                switch ($period) {
                    case 'day':
                        $diff = $now->diffInDays($lasted) ;
                        $period = 'week';
                        break;
                    case 'month':
                        $diff = $now->diffInMonths($lasted) ;
                        break;
                }


                if($diff >= $period_amount){

                    $params = 'backup_'. $period . '_' . substr($now->year, 2, 2) . sprintf('%02d',$now->month) . sprintf('%02d',$now->day);

                    $count = Bill::count();

                    $this->exec_crontab_backup($params);

                    $export = new Export();

                    $export->name = $params;
                    $export->type = $type;
                    $export->file_name = $params . '.sql';

                    $export->no_record = $count;

                    $export->save();

                }

            }


        }



    }

    public function exec_crontab_backup($params)
    {

        $username = "lemp";
        $password = "123456";
        $database = "lemp_db";
        $dump_name = "./storage/backup/" .$params . ".sql";
        exec("(mysqldump --host db -u$username -p$password $database  > $dump_name) 2>&1");

    }


}
