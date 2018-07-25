<?php

namespace App\Http\Controllers\Report;

use App\Branch;
use App\Model\Amulet;
use App\Model\Bill;
use App\Model\Craft;
use App\Model\Customer;
use App\Model\Job;
use App\Model\Order;
use App\Model\Payment;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $dt = Carbon::now(); //now
        $dn = Carbon::today(); //00:00:00

        $getInit = $this->initReport();
        $report = $getInit->report;
        $branch = $getInit->branch;

        $date_start = sprintf('%02d',$dt->day) .'/'.sprintf('%02d',$dt->month) .'/'.strval($dt->year + 543);

        if (count($branch) > 1){
            $branch_id = 0;
            $report_desc = 'ทุกสาขา '.' วันที่ '. $date_start ;
            $report_branch = 'ทุกสาขา';
        } else {
            $branch_id = $branch[0]->id;
            $report_desc = $branch[0]->name.' วันที่ '. $date_start ;
            $report_branch = $branch[0]->name;
        }

        $getData = $this->orderReport($dn,$dt,$branch_id);

        $data = $getData->data;
        $report_header = $getData->header;
        $unit = $getData->unit;
        $current = null;
        $report_date = $date_start;

        return view('report/report', compact('report','data', 'report_header','branch','current','report_desc','report_date','report_branch','unit'));

    }

    public function getReport(Request $request)
    {
        //dd($request->all());
        $start = explode('/',$request->date_start);
        $end = explode('/',$request->date_end);

        $start_bc = Carbon::create($start[2] - 543 , $start[1], $start[0], 0);
        $end_bc = Carbon::create($end[2] - 543 , $end[1], $end[0], 23, 59, 59);

        $branch_id = $request->branch_id;
        $report_id = $request->report_id;

        $getInit = $this->initReport();
        $getData = $this->selectReport($report_id,$start_bc,$end_bc,$branch_id);

        $data = $getData->data;
        $report_header = $getData->header;

        $report = $getInit->report;
        $branch = $getInit->branch;
        $current = $this->currentReport($request);
        $unit = $getData->unit;
        $report_desc = null;
        $report_date = null;
        $report_branch = null;

        return view('report/report', compact('report','data','report_header','branch','current','report_desc','report_date','report_branch','unit'));

    }

    protected function initReport()
    {
        $user = Auth::user()->roles()->get();
        $role = $user[0]->level;
        $branch_id = Auth::user()->branch_id;

        if ($role === 4){
            $branch = Branch::all();
        } else {
            $branch = array(Branch::find($branch_id));
        }

        $report = array(
            (object)[ 'id' => 1, 'name' => 'ยอดรวมงานซ่อม' ],
            (object)[ 'id' => 2, 'name' => 'ยอดรวมบิล' ],
            (object)[ 'id' => 3, 'name' => 'ยอดรวมชำระ' ],
            (object)[ 'id' => 4, 'name' => 'ยอดรวมน้ำหนักทอง']
        );

        return (object)[
            'report' => $report,
            'branch' => $branch,
        ];

    }

    protected function selectReport($report_id,$start_bc,$end_bc,$branch_id)
    {
        switch ($report_id) {
            case 1:
                return $this->orderReport($start_bc,$end_bc,$branch_id);
                break;
            case 2:
                return $this->billReport($start_bc,$end_bc,$branch_id);
                break;
            case 3:
                return $this->paymentReport($start_bc,$end_bc,$branch_id);
                break;
            case 4:
                return $this->goldReport($start_bc,$end_bc,$branch_id);
                break;
        }

    }

    protected function currentReport($request)
    {
        $start = explode('/',$request->date_start);
        $end = explode('/',$request->date_end);

        $start = $start[0].'/'.$start[1].'/'.strval($start[2]-543);
        $end = $end[0].'/'.$end[1].'/'.strval($end[2]-543);
        $branch_id = $request->branch_id;
        $branch = $branch_id == 0 ? 'ทุกสาขา' : Branch::find($branch_id)->name;

        $report = $this->initReport()->report;
        $report_desc = $start !== $end ? $branch.' วันที่ '. $request->date_start .' - ' . $request->date_end
                                       : $branch.' วันที่ '. $request->date_start ;

        return (object)[
            'report' => $report[intval($request->report_id)-1]->name,
            'report_desc' => $report_desc,
            'report_id' => $request->report_id,
            'start' => $start,
            'end' => $end,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'branch' => $request->branch_id,
            'branch_name' => $branch,
        ];
    }


    protected function orderReport($start_bc,$end_bc,$branch_id)
    {
        $branch_id =intval($branch_id);

        if ($branch_id !== 0){
            $query = [['activate',1],['branch_id', $branch_id ]];
        } else {
            $query = [['activate',1]];
        }

        $order = Order::where($query)->whereBetween('date_', [$start_bc, $end_bc])->get();

        $data = [];
        foreach ($order as $o){
            $data[] = $this->dataModelOrder($o);
        }
        $header = $this->initOrderHeader();
        $unit   = 'บาท';

        return (object)[
            'header' => $header,
            'data' => $data,
            'unit' => $unit
        ];
    }

    protected function dataModelOrder($data)
    {
        $bill = Bill::find($data->bill_ref);
        $customer = Customer::find($data->customer_id);
        $job =  Job::find($data->job_id);
        $amulet = Amulet::find($data->amulet_id);

        $model = (object)[
            'date' => $data->date,
            'bill_id' => $bill->bill_id,
            'c_name' => $customer->name,
            'c_type' => $customer->customer_type,
            'job' => $job->name,
            'amulet' => $amulet->name,
            'amount' => $data->amount,
            'price' => $data->price,
        ];
        return $model;
    }

    protected function initOrderHeader()
    {
        $c_type = array(
            (object)[ 'id' => '1', 'name' => 'สด1'],
            (object)[ 'id' => '2', 'name' => 'สด2'],
            (object)[ 'id' => '3', 'name' => 'สด3']
        );
        $job = Job::all();
        $amulet = Amulet::all();
        return array(
            (object)[ 'field' => 'date', 'name' => 'วันที่' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'bill_id', 'name' => 'เลขที่บิล' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'c_name', 'name' => 'ชื่อลูกค้า' , 'search' => true , 'select' => null],
            (object)[ 'field' => 'c_type', 'name' => 'ลูกค้า' , 'search' => true , 'select' => $c_type],
            (object)[ 'field' => 'job', 'name' => 'งานซ่อม' , 'search' => true , 'select' => $job ],
            (object)[ 'field' => 'amulet', 'name' => 'ชิ้นงาน' , 'search' => true , 'select' => $amulet ],
            (object)[ 'field' => 'amount', 'name' => 'จำนวน' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'price', 'name' => 'ยอดรวม' , 'search' => false , 'select' => null],
        );
    }


    protected function billReport($start_bc,$end_bc,$branch_id)
    {
        $branch_id = intval($branch_id);
        if ($branch_id !== 0){
            $bill = Bill::where('branch_id', $branch_id)->whereBetween('date_', [$start_bc, $end_bc])->get();
        } else {
            $bill = Bill::whereBetween('date_', [$start_bc, $end_bc])->get();
        }

        $data = [];
        foreach ($bill as $o){
            $data[] = $this->dataModelBill($o);
        }
        $header = $this->initBillHeader();
        $unit   = 'บาท';

        return (object)[
            'header' => $header,
            'data' => $data,
            'unit' => $unit
        ];
    }

    protected function dataModelBill($data)
    {

        $customer = Customer::find($data->customer_id);
        $order = Order::where('bill_ref',$data->id)->get();
        $j_type = intval($data->job_type);
        $u_name = User::find($data->user_id)->u_name;
        $job_type = array('','งานซ่อม','แกะสลัก','อื่นๆ');

        $cost = 0;
            foreach($order as $l){
                $cost += $l['price'];
            }

        $status = '';
        switch ($data) {
            case $data->status == 1:
                $status = 'ปิดบิลแล้ว';
                break;
            case $data->status == 2:
                $status = 'ยกเลิกบิล ' . $data->desc;
                break;
            case $data->deliver == 1:
                $status = 'ส่งงานแล้ว';
                break;
            case $data->process == 1:
                $status = 'ดำเนินการ';
                break;
        }

        if ($data->status == 2){
            $cash_status = 'ยกเลิก';
        } elseif ($data->pay == 1){
            $cash_status = 'ครบแล้ว';
        } else {
            $cash_status = 'ค้างชำระ';
        }

        $model = (object)[
            'date' => $data->date,
            'bill_id' => $data->bill_id,
            'c_name' => $customer->name,
            'c_type' => $customer->customer_type,
            'c_phone' => $customer->phone,
            'j_type' => $job_type[$j_type],
            'u_name' => $u_name,
            'b_status' => $status,
            'b_cash' => $cash_status,
            'b_cost' => $cost,

        ];
        return $model;
    }

    protected function initBillHeader()
    {
        $c_type = array(
            (object)[ 'id' => '1', 'name' => 'สด1'],
            (object)[ 'id' => '2', 'name' => 'สด2'],
            (object)[ 'id' => '3', 'name' => 'สด3']
        );
        $b_status = array(
            (object)[ 'id' => '1', 'name' => 'ดำเนินการ'],
            (object)[ 'id' => '2', 'name' => 'ส่งงานแล้ว'],
            (object)[ 'id' => '3', 'name' => 'ปิดบิลแล้ว'],
            (object)[ 'id' => '4', 'name' => 'ยกเลิกบิล']
        );
        $j_type = array(
        (object)[ 'id' => '1', 'name' => 'งานซ่อม'],
        (object)[ 'id' => '2', 'name' => 'แกะสลัก'],
        (object)[ 'id' => '3', 'name' => 'อื่นๆ']
    );
        $c_status = array(
            (object)[ 'id' => '1', 'name' => 'ครบแล้ว'],
            (object)[ 'id' => '2', 'name' => 'ยกเลิก'],
            (object)[ 'id' => '3', 'name' => 'ค้างชำระ']
        );

        return array(
            (object)[ 'field' => 'date', 'name' => 'วันที่' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'bill_id', 'name' => 'เลขที่บิล' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'c_name', 'name' => 'ชื่อลูกค้า' , 'search' => true , 'select' => null],
            (object)[ 'field' => 'c_type', 'name' => 'ลูกค้า' , 'search' => true , 'select' => $c_type],
            (object)[ 'field' => 'c_phone', 'name' => 'หมายเลขโทรศัพท์' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'u_name', 'name' => 'พนักงาน' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'j_type', 'name' => 'งาน' , 'search' => true , 'select' => $j_type],
            (object)[ 'field' => 'b_status', 'name' => 'สถานะ' , 'search' => true , 'select' => $b_status],
            (object)[ 'field' => 'b_cash', 'name' => 'การชำระ' , 'search' => true , 'select' => $c_status],
            (object)[ 'field' => 'b_cost', 'name' => 'ยอดรวม' , 'search' => false , 'select' => null],
        );
    }


    protected function paymentReport($start_bc,$end_bc,$branch_id)
    {
        $branch_id =intval($branch_id);

        if ($branch_id !== 0){
            $query = [['branch_id', $branch_id ]];
            $payment = Payment::where($query)->whereBetween('created_at', [$start_bc, $end_bc])->get();
        } else {
            $payment = Payment::whereBetween('created_at', [$start_bc, $end_bc])->get();
        }

        $data = [];
        foreach ($payment as $o){
            $data[] = $this->dataModelPayment($o);
        }
        $header = $this->initPaymentHeader();
        $unit   = 'บาท';

        return (object)[
            'header' => $header,
            'data' => $data,
            'unit' => $unit
        ];
    }

    protected function dataModelPayment($data)
    {
        $bill = Bill::find($data->bill_ref);
        $customer = Customer::find($bill->customer_id);
        $receive = User::find($data->user_recive)->u_name;

        $type = '';
        switch ($data->method) {
            case 'cash':
                $type = 'เงินสด';
                break;
            case 'credit':
                $type = 'บัตรเครดิต';
                break;
            case 'online':
                $type = 'ออนไลน์';
                break;
            case 'coupon':
                $type = 'คูปอง';
                break;
        }

        $status = '';
        switch ($data->activate) {
            case 1:
                $status = 'รับเงิน';
                break;
            case 0:
                $status = 'ยกเลิก ' . $data->cause . 'โดย' . User::find($data->user_void)->u_name;
                break;
        }

        $date = $data->created_at;
        $date = sprintf('%02d',$date->day).'/'.sprintf('%02d',$date->month).'/'.strval($date->year + 543);

        $model = (object)[
            'date' => $date,
            'bill_id' => $bill->bill_id,
            'c_name' => $customer->name,
            'c_type' => $customer->customer_type,
            'p_method' => $type,
            'p_receive' => $receive,
            'p_status' => $status,
            'amount' => $data->value,
        ];
        return $model;
    }

    protected function initPaymentHeader()
    {
        $c_type = array(
            (object)[ 'id' => '1', 'name' => 'สด1'],
            (object)[ 'id' => '2', 'name' => 'สด2'],
            (object)[ 'id' => '3', 'name' => 'สด3']
        );
        $p_method = array(
            (object)[ 'id' => '1', 'name' => 'เงินสด'],
            (object)[ 'id' => '2', 'name' => 'บัตรเครดิต'],
            (object)[ 'id' => '4', 'name' => 'คูปอง'],
            (object)[ 'id' => '3', 'name' => 'ออนไลน์']
        );
        $p_status = array(
            (object)[ 'id' => '1', 'name' => 'รับเงิน'],
            (object)[ 'id' => '2', 'name' => 'ยกเลิก'],
        );
        return array(
            (object)[ 'field' => 'date', 'name' => 'วันที่' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'bill_id', 'name' => 'เลขที่บิล' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'c_name', 'name' => 'ชื่อลูกค้า' , 'search' => true , 'select' => null],
            (object)[ 'field' => 'c_type', 'name' => 'ลูกค้า' , 'search' => true , 'select' => $c_type],
            (object)[ 'field' => 'p_method', 'name' => 'รูปแบบ' , 'search' => true , 'select' => $p_method ],
            (object)[ 'field' => 'p_receive', 'name' => 'ผู้รับ' , 'search' => false , 'select' => null ],
            (object)[ 'field' => 'p_status', 'name' => 'สถานะ' , 'search' => true , 'select' => $p_status ],
            (object)[ 'field' => 'amount', 'name' => 'ยอดรับ' , 'search' => false , 'select' => null],
        );
    }


    protected function goldReport($start_bc,$end_bc,$branch_id)
    {
        $branch_id =intval($branch_id);

        if ($branch_id !== 0){
            $query = [['activate',1],['status', 1],['branch_id', $branch_id ]];
        } else {
            $query = [['activate',1],['status', 1]];
        }

        $bill = Bill::where($query)->whereBetween('date_', [$start_bc, $end_bc])->get();

        $data = [];
        foreach ($bill as $o){
            $data[] = $this->dataModelGold($o);
        }
        $header = $this->initGoldHeader();
        $unit   = 'กรัม';

        return (object)[
            'header' => $header,
            'data' => $data,
            'unit' => $unit
        ];
    }

    protected function dataModelGold($data)
    {

        $craft_name =  Craft::find($data->craft_id)->name;
        $branch = Branch::find($data->branch_id)->name;
        $model = (object)[
            'date' => $data->date,
            'bill_id' => $data->bill_id,
            'branch' => $branch,
            'craft_name' => $craft_name,
            'amount' => number_format((float)$data->gold, 2, '.', ','),
        ];
        return $model;
    }

    protected function initGoldHeader()
    {
        $branch = Branch::all();
        return array(
            (object)[ 'field' => 'date', 'name' => 'วันที่' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'bill_id', 'name' => 'เลขที่บิล' , 'search' => false , 'select' => null],
            (object)[ 'field' => 'branch', 'name' => 'สาขา' , 'search' => true , 'select' => $branch],
            (object)[ 'field' => 'craft_name', 'name' => 'ชื่อช่าง' , 'search' => true , 'select' => null ],
            (object)[ 'field' => 'amount', 'name' => 'จำนวน' , 'search' => false , 'select' => null],
        );
    }


}
