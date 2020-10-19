<?php

namespace App\Http\Controllers\Report;

use App\Branch;
use App\Model\Bill;
use App\Model\Craft;
use App\Model\Gold;
use App\Model\Material;
use App\Model\Order;
use App\Model\Part;
use App\Model\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $dt = Carbon::now(); //now
        $dn = Carbon::today(); //00:00:00

        $date_start_bc = sprintf('%02d', $dt->day) . '/' . sprintf('%02d', $dt->month) . '/' . strval($dt->year);
        $date_start = sprintf('%02d', $dt->day) . '/' . sprintf('%02d', $dt->month) . '/' . strval($dt->year + 543);
        $date_now = sprintf('%02d', $dt->day) . '/' . sprintf('%02d', $dt->month) . '/' . strval($dt->year + 543);

        $role = Auth::user()->roles[0]->level;

        $branch = $this->initReport()->branch;

        if (count($branch) > 1) {
            $branch_id = '0';
            $report_desc = 'ทุกสาขา ' . ' วันที่ ' . $date_start;
            $branch_name = 'ทุกสาขา';
        } else {
            $branch_id = $branch[0]->id;
            $report_desc = $branch[0]->name . ' วันที่ ' . $date_start;
            $branch_name = $branch[0]->name;
        }

        $current = (object)[
            'start' => $date_start_bc,
            'end' => $date_start_bc,
            'report_desc' => $report_desc,
            'date_start' => $date_start,
            'date_end' => $date_start,
            'date_now' => $date_now,
            'branch' => $branch_id,
            'branch_name' => $branch_name,
        ];

        $data = $this->summaryData($dn, $dt, $branch_id);

        return view('report/report-summary', compact('branch', 'role', 'current', 'data'));

    }

    public function getSummary(Request $request)
    {

        $start = explode('/', $request->date_start);
        $end = explode('/', $request->date_end);

        $start_bc = Carbon::create($start[2] - 543, $start[1], $start[0], 0);
        $end_bc = Carbon::create($end[2] - 543, $end[1], $end[0], 23, 59, 59);

        $role = Auth::user()->roles[0]->level;
        $branch = $this->initReport()->branch;

        $branch_id = $request->branch_id;

        $current = $this->currentReport($request);

        //dd($request);

        $data = $this->summaryData($start_bc, $end_bc, $branch_id);

        return view('report/report-summary', compact('branch', 'role', 'current', 'data'));
    }

    protected function initReport()
    {
        $user = Auth::user()->roles()->get();
        $role = $user[0]->level;
        $branch_id = Auth::user()->branch_id;

        if ($role === 4) {
            $branch = Branch::all();
        } else {
            $branch = [Branch::find($branch_id)];
        }

        return (object)[
            'branch' => $branch,
        ];

    }

    protected function currentReport($request)
    {
        $start = explode('/', $request->date_start);
        $end = explode('/', $request->date_end);

        $dt = Carbon::today(); //now
        $date_now = sprintf('%02d', $dt->day) . '/' . sprintf('%02d', $dt->month) . '/' . strval($dt->year + 543);

        $start = $start[0] . '/' . $start[1] . '/' . strval($start[2] - 543);
        $end = $end[0] . '/' . $end[1] . '/' . strval($end[2] - 543);
        $branch_id = $request->branch_id;
        $branch = $branch_id == '0' ? 'ทุกสาขา' : Branch::find($branch_id)->name;

        $report_desc = $start !== $end ? $branch . ' วันที่ ' . $request->date_start . ' - ' . $request->date_end
            : $branch . ' วันที่ ' . $request->date_start;

        return (object)[
            'start' => $start,
            'end' => $end,
            'report_desc' => $report_desc,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'date_now' => $date_now,
            'branch' => $request->branch_id,
            'branch_name' => $branch,
        ];
    }


    protected function summaryData($start_bc, $end_bc, $branch_id)
    {

        if ($branch_id !== "0") {
            $query = [['activate', 1], ['branch_id', $branch_id]];
            $query_ = [['payment.activate', 1], ['bill.branch_id', $branch_id]];
            $query_gold = [['gold.activate', 1], ['bill.branch_id', $branch_id]];
        } else {
            $query = [['activate', 1]];
            $query_ = [['payment.activate', 1]];
            $query_gold = [['gold.activate', 1]];
        }

        $order = Order::where($query)->whereBetween('date_', [$start_bc, $end_bc])->get();
        $order = $this->summaryPrice($order, 'price');

        $bill = Bill::where($query)->whereBetween('date_', [$start_bc, $end_bc])->get();

        $materail = $this->summaryMaterial($bill);
        $materail = $this->summaryPrice($materail, 'price');

        $cash = $this->summaryPay($query_, 'cash',$start_bc, $end_bc);
        $cash = $this->summaryPrice($cash, 'value');
        $credit_cash = $this->summaryCash($query, 'cash',$start_bc, $end_bc);
        $credit_cash = $this->summaryPrice($credit_cash, 'value');

        /*$cash = $this->summaryPay($query, 'cash',$start_bc, $end_bc);
        $cash = $this->summaryPrice($cash, 'value');*/

        $credit = $this->summaryPay($query_, 'credit',$start_bc, $end_bc);
        $credit = $this->summaryPrice($credit, 'value');
        $credit_credit = $this->summaryCash($query, 'credit',$start_bc, $end_bc);
        $credit_credit = $this->summaryPrice($credit_credit, 'value');

        $voucher = $this->summaryPay($query_, 'voucher',$start_bc, $end_bc);
        $voucher = $this->summaryPrice($voucher, 'value');
        $credit_voucher = $this->summaryCash($query, 'voucher',$start_bc, $end_bc);
        $credit_voucher = $this->summaryPrice($credit_voucher, 'value');

        $coupon = $this->summaryPay($query_, 'coupon',$start_bc, $end_bc);
        $coupon = $this->summaryPrice($coupon, 'value');
        $credit_coupon = $this->summaryCash($query, 'coupon',$start_bc, $end_bc);
        $credit_coupon = $this->summaryPrice($credit_coupon, 'value');

        $online = $this->summaryPay($query_, 'online',$start_bc, $end_bc);
        $online = $this->summaryPrice($online, 'value');
        $credit_online = $this->summaryCash($query, 'online',$start_bc, $end_bc);
        $credit_online = $this->summaryPrice($credit_online, 'value');

        $payment_re = $this->summaryRemain($query,$start_bc,$end_bc);

        $craftMan = $this->summaryCraftMan($query_gold,$start_bc, $end_bc);

        $dt = Carbon::now();
        $getDateServer = sprintf('%02d',$dt->day) .'/'. sprintf('%02d',$dt->month) .'/'. strval($dt->year + 543) .'-'. $dt->toTimeString();

        $sumService = $order + $materail;
        $sumPayService = $cash + $credit + $voucher + $coupon + $online;
        $sumCredit = $credit_cash + $credit_credit + $credit_voucher + $credit_coupon + $credit_online;

        return (object)[
            'order' => $order,
            'material' => $materail,
            'sumService' => $sumService,
            'cash' => $cash,
            'credit' => $credit,
            'voucher' => $voucher,
            'coupon' => floatval($coupon + $online),
            'payment_remain' => $payment_re,
            'sumPayService' => $sumPayService,
            'craft' => $craftMan->craft,
            'craft_sum' => $craftMan->sum,
            'credit_cash' => $credit_cash,
            'credit_credit' => $credit_credit,
            'credit_voucher' => $credit_voucher,
            'credit_coupon' => floatval($credit_coupon + $credit_online),
            'sumCredit' => $sumCredit,
            'getDateServer' => $getDateServer
        ];

    }

    protected function summaryPrice($data, $position)
    {
        $price = 0;
        foreach ($data as $l) {
            $price += $l[$position];
        }
        return $price;

    }

    protected function summaryMaterial($bill)
    {
        $material = [];
        foreach ($bill as $l) {
            $m = Part::where('bill_ref', $l['id'])->get();
            foreach ($m as $c) {
                $material[] = $c;
            }
        }
        return $material;
    }

    protected function summaryPay($query_, $method, $start_bc, $end_bc)
    {
        $data = [];
            $d = Bill::leftjoin('payment',function ($join){
                $join->on('bill.id', '=', 'payment.bill_ref');
            })
                ->select('bill.id', 'bill.date_', 'payment.*')
                ->whereBetween('bill.date_', [$start_bc, $end_bc])
                ->where($query_)
                ->where('method', $method)
                ->get();
                /*->select('bill.id', 'payment.created_at', 'payment.value','payment.activate')
                ->where('method', $method)
                ->where('payment.activate', 1)
                ->whereBetween('bill.created_at', [$start_bc, $end_bc])
                ->get();*/
            //dd($d);
            foreach ($d as $c) {
                $data[] = $c;
            }
        //dd($data);
        return $data;
    }

    protected function summaryCash($query, $method, $start_bc, $end_bc)
    {
        $data = [];
        $d = Payment::where($query)
            ->where('method', $method)
            ->whereBetween('created_at', [$start_bc, $end_bc])->get();
        foreach ($d as $c) {
            $data[] = $c;
        }
        return $data;
    }

    protected function summaryRemain($query,$start_bc,$end_bc)
    {
        $bill = Bill::where($query)->whereBetween('date_', [$start_bc, $end_bc])->get();
        $data = 0;
        foreach ($bill as $i => $l) {

            $d = Order::where('bill_ref', $l['id'], ['activate', 1])->get();
            $sumPrice = 0;
            foreach ($d as $c) {
                $sumPrice += $c->price;
            }

            $m = Part::where('bill_ref', $l['id'])->get();
            $sumMaterial = 0;
            foreach ($m as $c) {
                $sumMaterial += $c->price;
            }

            $cash = $l['cash'];

            $payRemain = ($sumPrice + $sumMaterial) - $cash;
            $data += $payRemain;

        }
        return $data;
    }

    protected function summaryCraftMan($query_gold,$start_bc, $end_bc)
    {

        $gold = Bill::leftjoin('gold',function ($join){
            $join->on('bill.id', '=', 'gold.bill_ref');
        })
            ->select('bill.id', 'bill.date_', 'gold.*')
            ->whereBetween('bill.date_', [$start_bc, $end_bc])
            ->where($query_gold)
            ->get();

        $data = [];
        foreach ($gold as $i => $l) {
            $c = Craft::where('id', $l['craft_id'])->get();
            $name = '';
            foreach ($c as $j) {
                $name = $j->name;
            }

            $data[] = (object)[
                $name => $l['value']
            ];

        }

        $merged = [];
        foreach ($data as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }

        $result = [];
        $result_sum = 0;
        foreach ($merged as $k => $r){
            $result[] = (object)[
                'name' => $k,
                'gold' => $r
            ];
            $result_sum += $r;
        }

        return (object)[
            'craft' => $result,
            'sum' => $result_sum
        ];

    }

}

