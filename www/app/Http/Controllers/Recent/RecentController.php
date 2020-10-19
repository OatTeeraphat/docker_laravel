<?php

namespace App\Http\Controllers\Recent;

use App\Branch;
use App\Model\Bill;
use ArrayObject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RecentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        //dd($request);
        $bills = $this->getBill($request);
        $current = $this->initBill($request);
        $branch = Branch::all();
        return view('bill/bill-query',compact('branch', 'bills', 'current'));
    }

    public function getBillBydate(Request $request){
        //dd($request);
        $bills = $this->getBill($request);
        $current = $this->initBill($request);
        $branch = Branch::all();
        return view('bill/bill-query',compact('branch', 'bills', 'current'));
    }

    public function initBill($request){

        $user = Auth::user()->roles()->get();
        $role = $user[0]->level;
        $branch_id = Auth::user()->branch_id;
        $branch_req = $request->branch_id;
        $today = Carbon::today();

        if($request->date_start){
            $start = explode('/',$request->date_start);
            $end = explode('/',$request->date_end);

            $start_bc = Carbon::create($start[2] - 543 , $start[1], $start[0], 0);
            $end_bc = Carbon::create($end[2] - 543 , $end[1], $end[0], 23, 59, 59);

            $diff = $start_bc->diffInDays($end_bc);

            if ($diff > 3){
                $start_bc = $end_bc->copy()->subDays(4);
            }
        } else{

            $start = [$today->day, $today->month, intval(intval($today->year) + 543)];
            $end = $start;

            $start_bc = Carbon::create($today->year , $today->month, $today->day, 0);
            $end_bc = Carbon::create($today->year , $today->month, $today->day , 23, 59, 59);
        }

        if($role >= 4) {
            if(isset($branch_req) && $branch_req > 0){
                $branch_request = $branch_req;
            } else {
                $branch_request = 0;
            }
        }

        else {
            $branch_request = $branch_id;
        }

        $start = $start[0].'/'.$start[1].'/'.strval(intval($start[2])-543);
        $end = $end[0].'/'.$end[1].'/'.strval(intval($end[2])-543);

        return (object)[
            'role' => $role,
            'branch_id' => $branch_id,
            'branch_request' => $branch_request,
            'today' => $today,
            'start_bc' => $start_bc,
            'end_bc' => $end_bc,
            'date_start' => $start,
            'date_end' => $end
        ];

    }

    public function getBill($request)
    {
        $init = $this->initBill($request);
        $branch = $init->branch_id;
        $branch_req = $request->branch_id;

        if($init->role >= 4) {
            if(isset($branch_req) && $branch_req > 0){
                $query = [['branch_id', $branch_req]];
            } else {
                $query = [];
            }
        }
        else {
            $query = [['branch_id', $branch]];
        }

        $billList = $this->queryBill($query, $init->start_bc, $init->end_bc);

        $output = [];

        foreach ($billList as $bill){
            $output[] = (object)[
                'bill_id' => $bill->bill_id,
                'date' => $bill->date,
                'customer_type' => $bill->customer[0]->name . ' (' .$bill->customer[0]->customer_type . ')',
                'job_type' => $bill->job_type,
                'sum_cost' => number_format((float)$this->sum($bill->order()->get(),'price') + (float)$this->sum($bill->part()->get(),'price'), 2),
                'gold' => $this->sum($bill->gold()->get(),'value'),
                'deliver' => $bill->deliver,
                'pay' => $bill->pay,
                'status' => $bill->status,
                'img' => $this->getImageThumbnail($bill->image_part)
            ];

        }
        return  $this->sortByDate($this->groupByDate($output));
    }

    public function groupByDate($data){
        $result = array();
        foreach ((array)$data as $element) {
            $result[$element->date][] = $element;
        }
        return $result;
    }

    public function sortByDate($data){
        $result = array();
        $ArrayObject = new ArrayObject($data);
        $ArrayObject->ksort();
        foreach ($ArrayObject as $key => $val) {
            $result[$key] = $val;
        }
        return array_reverse($result);
    }

    public function queryBill($query,$start_bc, $end_bc)
    {
        $bill = Bill::where($query)
            ->whereBetween('date_', [$start_bc, $end_bc])
            ->orderBy('id', 'desc')
            ->orderBy('branch_id', 'desc')
            ->with(['customer','order','part','gold'])
            ->get();

        return $bill;
    }

    public function sum($arrays,$key){
        $sum = 0;
        if($arrays){
            foreach ((array)$arrays as $it){
                foreach ((array)$it as $i){
                    $sum += $i[$key];
                }
            }
        }
        return $sum;
    }

    public function getImageThumbnail($image){
        $im = null;
        if (isset($image) && $image !=''){

            $image = explode(',' , $image);

            if($image[0] != ""){
                $im = $image[0];
            } elseif($image[1] != ""){
                $im = $image[1];
            } else {
                $im = null;
            }

        }

        return $im;
    }

}
