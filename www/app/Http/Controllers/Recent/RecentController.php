<?php

namespace App\Http\Controllers\Recent;

use App\Branch;
use App\Model\Bill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $branch = Branch::all();
        return view('bill/bill-list',compact('branch'));
    }

    public function getBill(Request $request)
    {
        $slice = explode('!',$request->ref);
        $branch_req = intval($slice[2]);

        if ($branch_req !== 0){
            $bill = Bill::orderBy('id', 'desc')
                ->with(['customer'])
                ->get()
                ->where('branch_id', '=', $branch_req)
                ->groupBy(function($date) {
                    return $date->date;
                });
        } else {
            $bill = Bill::orderBy('id', 'desc')
                ->with(['customer'])
                ->get()
                ->groupBy(function($date) {
                    return $date->date;
                });
        }

        $result_ = $bill->filter(function ($item) {
            return $item;
        })->values()->all();

        $offset = ($slice[0])*($slice[1]);

        $output = array_slice($result_,$offset,$slice[1]);

        return  $output;
    }


}
