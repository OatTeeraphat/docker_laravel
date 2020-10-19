<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Model\Amulet;
use App\Model\Bill;
use App\Model\Craft;
use App\Model\Customer;
use App\Model\Gold;
use App\Model\Job;
use App\Model\Material;
use App\Model\Order;
use App\Model\Part;
use App\Model\Payment;
use App\Model\Setting;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

use App\Http\Controllers\Export\ExportController;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $job    = Job::orderBy('order', 'asc')->get();
        $amulet = Amulet::orderBy('order', 'asc')->get();
        $material = Material::where('activate','1')->orderBy('order', 'asc')->get();


        if ($request->id == null){

            $type = 'create';
            return view('home', compact('amulet','job', 'material', 'type'));

        } else {

            $type = 'update';
            $billData = Bill::where('bill_id', $request->id)
                        ->with(['order','customer','part','payment'])
                        ->get();


            if(!count($billData)){
                return  "{\"response\" : \"404\", \"message\" : \"ค้นหาบิล ". $request->id ." ไม่พบ\"}";
            }

            $image = $billData[0]->image_part;
            $images = explode( ',', $image );

            $imagePart = [];

            foreach ($images as $l){
                if ($l !== '' && strlen($l) < 25){
                    $imagePart[] = $l;
                }
            }

            $pay = Payment::where('bill_ref', $billData[0]->id)
                    ->with(['u_recive','u_void'])
                    ->get();

            //dd($pay[0]->u_recive->name);

            $branch_id = User::where('id',$billData[0]->user_id)->get()->first()->branch_id;
            $craft = Craft::where([
                ['branch_id', '=', $branch_id ],
                ['activate', '=', 1 ]])->get();

            $payment = [];
            if (isset($pay)){
                foreach ($pay as $l){
                    $date = $l['created_at'];
                    $u_void = null;
                    if(isset($l['u_void'])){$u_void = $l['u_recive']->u_name;};
                    $payment[] = (object) [
                        'id' => $l['id'],
                        'bill_ref' => $l['bill_ref'],
                        'date' => sprintf('%02d',strval($date->day)) . '/' .
                            sprintf('%02d',strval($date->month)) . '/' .
                            strval($date->year+543),

                        'method' => $l['method'],
                        'value' => number_format((float)$l['value'], 2, '.', ','),
                        'cause'=> $l['cause'],
                        'user_recive' => $l['u_recive']->u_name,
                        'user_void' => $u_void

                    ];
                }
            }

            $total_pay = 0;
            if (isset($pay)){
                foreach ($pay as $l){
                    if ($l['activate'] !== 0) {
                      $total_pay += $l['value'];
                    }

                }
            }
            //dd($imagePart);

            $cost = 0;
            foreach ($billData[0]->order as $l){
                $cost +=  $l['price'];
            }

            $materialData = '';
            $materialCost = 0;
            if (isset($billData[0]->part)){
                foreach ($billData[0]->part as $l){
                    $materialName = Material::where('id', $l['material_id'])->get();
                    setlocale(LC_MONETARY,"en_US");
                    $materialData .=  $materialName[0]->name.' '.number_format((float)$l['price'], 2, '.', ',') .' บาท ';
                    $materialCost +=  $l['price'];
                }
            }

            //dd($materialData);

            $costData = $cost + $materialCost;

            $gold_data_ = Gold::where([['activate', 1],['bill_ref',$billData[0]->id]])->get();
            //dd($gold_data_);
            $gold_data = (object)[
                'gold_1' => count($gold_data_) > 0 ? $gold_data_[0] : null ,
                'gold_2' => count($gold_data_) > 1 ? $gold_data_[1] : null
            ];

            //dd($gold_data);

            return view('home', compact('amulet','job', 'material', 'billData', 'imagePart', 'type' ,'total_pay','costData','payment','craft','pay','materialData','gold_data'));

        }

    }

    public function print(Request $request)
    {
        $job    = Job::orderBy('order', 'asc')->get();
        $amulet = Amulet::orderBy('order', 'asc')->get();

        $setting = Setting::find(1);

        $billData = Bill::where('bill_id', $request->id)
            ->with(['order','customer','part','payment','users'])
            ->get();

        $userData = User::where('id', $billData[0]->user_id)
            ->with(['branch'])
            ->get();

        $cost = 0;
        foreach ($billData[0]->order as $l){
            setlocale(LC_MONETARY,"en_US");
            $cost +=  $l['price'];
        }

        $total_pay = 0;
        if (isset($billData[0]->payment)){
            foreach ($billData[0]->payment as $l){
                if($l->activate == 1) {
                    $total_pay += $l['value'];
                }

            }
        }
        $payData = $total_pay > 0 ? number_format((float)$total_pay, 2, '.', ',') .' บาท ' : 0;

        $materialData = '';
        $materialCost = 0;
        if (isset($billData[0]->part)){
            foreach ($billData[0]->part as $l){
                $materialName = Material::where('id', $l['material_id'])->get();
                setlocale(LC_MONETARY,"en_US");
                $materialData .=  $materialName[0]->name.' '.number_format((float)$l['price'], 2, '.', ',') .' บาท ';
                $materialCost +=  $l['price'];
            }
        }

        $costData = number_format((float)($cost + $materialCost), 2, '.', ',') .' บาท ';
        $remainPayData = number_format((float)(($cost + $materialCost) - $total_pay), 2, '.', ',') .' บาท ';

        $image = $billData[0]->image_part;
        $images = explode( ',', $image );

        $imagePart = [];

        foreach ($images as $l){
            if ($l !== '' && strlen($l) < 25){
                $imagePart[] = $l;
            }
        }

        $dt = Carbon::now();
        $getDateServer = sprintf('%02d',$dt->day) .'/'. sprintf('%02d',$dt->month) .'/'. strval($dt->year + 543) .'-'. $dt->toTimeString();
        //dd($imagePart);
        return view('bill/bill-page', compact('amulet','job', 'billData', 'imagePart' ,'payData', 'userData', 'materialData', 'costData', 'remainPayData','setting','getDateServer'));
    }

    public function initBill(Request $request)
    {
        if ($request->type == 'create'){
            return $this->create($request);
        } else {
            return $this->update($request);
        }
    }

    public function uploadImg(Request $request) {

        $imgIndex = ($request->key) + 1;
        $imgName = request()->file->getClientOriginalName();
        $imgType = pathinfo($imgName, PATHINFO_EXTENSION);
        $branch_id = intval(Auth::user()->branch_id) + 100;
        $timeStamp = Carbon::now()->timestamp;
        $fileName = 'job_'.$branch_id .'_'. $timeStamp .'_'. $imgIndex . '.' . $imgType;


        request()->file->move(public_path('images/job/'), $fileName);

        return response()->json(['path' => $fileName]);

    }

    public function deleteImg(Request $request)
    {
        $del = explode(';',$request->delete);
        $data = Bill::find($del[1]);
        $img_path = $data->image_part;
        $img_list = explode(',',$img_path);
        $img_length = count($img_list) ;
        $result = '';
        foreach ($img_list as $i => $l){
            if ($l !== $del[0]){
                $result.= $l . ($i == $img_length-2 ? '' : ',');
            }
        }
        if (substr($result,-1,1) == ','){
            $path = substr($result,0,-1);
        } else {
            $path = $result;
        }
        $data->update(['image_part' => $path ]);
        $data->save;

        return response(200);
    }

    public function voidBill(Request $request)
    {
        //dd($request);
        $bill_id = $request->id;
        $bill = Bill::find((int)$bill_id);
        $bill->update([
            'activate' => 0,
            'pay'=> 1,
            'status' => 2,
            'desc' => $request->bill_cause,
        ]);
        $bill->save;

        $order = Order::where('bill_ref',$bill->id)->get();

        foreach ($order as $l){
            $l->update([
                'activate' => 0
            ]);
            $l->save;
        }

        return back();
    }

    protected function imgList($request)
    {
        if($request->type == 'update'){

            $data = Bill::find((int)$request->bill_id);
            $oldImg = $data->image_part;

            if (isset($request->image_list)){
                $list = json_decode($request->image_list,true);
                $result = '';
                $length = count($list);
                foreach ($list as $i => $l) {
                    $result .= $l['path'] . ($i < $length - 1 ? ',' : '');
                }
                return $oldImg . ',' . $result;
            } else {
                return $oldImg;
            }

        }else {
            if (isset($request->image_list)){
                $list = json_decode($request->image_list,true);
                $result = '';
                $length = count($list);
                foreach ($list as $i => $l){
                    $result .= $l['path'] . ($i < $length-1 ? ',' : '');
                }
                return $result;
            }
        }


    }

    protected function create($request)
    {
        $customerId = $this->createCustomer($request);
        $billId = $this->createBillId($request);
        $imgList = $this->imgList($request);
        $bill = $this->createBill($request,$customerId,$billId,$imgList);

        $this->createOrder($request,$bill,$customerId);
        $this->createPart($request,$bill);
        $this->createPayment($request,$bill);
        $this->checkPaymentRemain($request,$bill);

        $request->session()->flash('success', 'สร้างบิลใหม่ '.$bill->bill_id.'เรียบร้อยแล้ว !');
        return redirect('recent/bill?id='.$bill->bill_id);
    }

    protected function update($request)
    {
        //dd($request);
        $customerId = $this->createCustomer($request);
        $imgList = $this->imgList($request);
        $bill = $this->updateBill($request,$customerId,$imgList);
        $this->updateOrder($request,$bill,$customerId);
        $this->updatePart($request,$bill);
        $this->createPayment($request,$bill);
        $this->checkPaymentRemain($request,$bill);
        if ($request->gold_id_ !== null){
            $this->updateGold($request,$bill);
        } else {
            $this->createGold($request,$bill);
        }

        if ($bill->status > 0){
            $request->session()->flash('success', 'ปิดบิล '.$bill->bill_id.' เรียบร้อยแล้ว !');
        } else {
            $request->session()->flash('success', 'แก้ไขข้อมูล '.$bill->bill_id.' เรียบร้อยแล้ว !');
        }

        return redirect('/bill/update?id='.$bill->bill_id);
    }

    protected function createBill($request,$customerId,$billId,$imgList)
    {
        $date = explode('/',$request->date);
        $dateCarbon = Carbon::create(strval($date[2]-543), $date[1], $date[0]);
        $cash = str_replace(',', '', $request->cash);
        $customer = Customer::find($customerId);
        $c_increment = $customer->increment + 1;
        $customer->update([
            'increment' => $c_increment
        ]);

        $trigger_backup = "";

        $customer->save;
        $user = Auth::user();
        $bill = Bill::create([
            'date' => $request->date,
            'date_' => $dateCarbon,
            'bill_id' => $billId ,
            'status' => 0,
            'process' => 1,
            'deliver' => 0,
            'pay' => 0,
            'allow_zero' => isset($request->allow_zero) ? 1 : 0,
            'user_id' => $user->id,
            'customer_id' => $customerId,
            'branch_id' => $user->branch_id,
            'image_part'=> $imgList,
            'job_type' => $request->order_type,
            'cash' => $cash,
            'activate' => 1,
            'gold' => 1,
        ]);
        return $bill;
    }

    protected function updateBill($request,$customerId,$imgList){
        //dd($request);
        $date = explode('/',$request->date);
        $dateCarbon = Carbon::create(strval($date[2]-543), $date[1], $date[0]);
        $data = Bill::find((int)$request->bill_id);
        $allow_zero = isset($request->allow_zero) ? 1 : 0;
        $close_bill = intval($request->close_bill);
        $cash = str_replace(',', '', $request->cash);
        $total = str_replace(',', '', $request->total);
        $cash_remain = floatval($cash) - floatval($total);
        if ($data->deliver == 1 && $cash_remain == 0 && $close_bill == 1 ){
            $data->update([
                'cash' => $cash,
                'pay' => 1,
                'status' => 1,
            ]);
        } else {
            $data->update([
                'date' => $request->date,
                'date_' => $dateCarbon,
                'customer_id' => $customerId,
                'image_part'=> $imgList,
                'job_type' => $request->order_type,
                'cash' => $cash,
                'activate' => 1,
                'allow_zero' => $allow_zero
            ]);
        }
        $data->save;
        return $data;

    }

    protected function createBillId($request)
    {

        $branchId = User::find((int) $request->user_id)->branch_id;
        $branch = Branch::find($branchId);
        $branchTxt = 100+$branchId;

        $current = Carbon::now();
        $current_day = $current->day;
        $current_month = $current->month;

        $current_year = $current->year + 543;
        $last_of_day = Bill::where('branch_id', intval($branchId))
            ->orderBy('created_at','desc')->first()->created_at->day;


        if (isset($last_of_day)){
            if ($last_of_day == $current_day){
                $increment = $branch->increment + 1;
                $branch->update(['increment' => $increment ]);
                $branch->save;
                $billId = $branchTxt .'-'. substr($current_year, 2, 2) . sprintf('%02d',$current_month) . sprintf('%02d',$current_day) .'-'. sprintf('%03d',$increment) ;
                return $billId;
            } else {
                $branch->update(['increment' => 1 ]);
                $branch->save;
                $this->cron_backup($branchId);
                $billId = $branchTxt .'-'. substr($current_year, 2, 2) . sprintf('%02d',$current_month) . sprintf('%02d',$current_day) .'-'. '001' ;
                return $billId;
            }
        } else {
            $branch->update(['increment' => 1 ]);
            $branch->save;
            $this->cron_backup($branchId);
            $billId = $branchTxt .'-'. substr($current_year, 2, 2) . sprintf('%02d',$current_month) . sprintf('%02d',$current_day) .'-'. '001' ;
            return $billId;
        }
    }


    protected function createOrder($request,$bill,$customerId)
    {
        $orderList = json_decode($request->table_job ,true);
        $date = explode('/',$bill->date);
        $dateCarbon = Carbon::create(strval($date[2]-543), $date[1], $date[0]);
        $branch_id = $bill->branch_id;
        foreach ($orderList as $i => $l){
            $val = $this->valueOrder($l['value']);
            Order::create([
                'bill_ref' => $bill->id,
                'date' => $bill->date,
                'date_' => $dateCarbon,
                'branch_id' => $branch_id,
                'customer_id' => $customerId,
                'user_id' => $bill->user_id,
                'job_id' => $l['job_id'],
                'amulet_id'=> $l['amulet_id'],
                'amount' => $val->amount,
                'price' => $val->price,
                'activate' => 1,
            ]);
        }
    }

    protected function updateOrder($request,$bill,$customerId){
        $orderOld = Order::where('bill_ref', $bill->id )->get()->toArray();
        $orderList = json_decode($request->table_job ,true);
        $create = $orderList;
        $delete = $orderOld;
        foreach ( $orderList as $i => $l ) {
            foreach ($orderOld as $j => $o) {
                $date = explode('/',$request->date);
                $dateCarbon = Carbon::create(strval($date[2]-543), $date[1], $date[0]);
                $val = $this->valueOrder($l['value']);
                $rule = $o['job_id'] == $l['job_id'] && $o['amulet_id'] == $l['amulet_id'];
                if ($rule){
                    $data = Order::find($o['id']);
                    $data->update([
                        'date' => $request->date,
                        'date_' => $dateCarbon,
                        'customer_id' => $request->customer_id,
                        'amount' => (int)$val->amount,
                        'price' => (double)$val->price,
                        'activate' => 1,
                    ]);
                    $data->save;
                    unset($create[$i]);
                    unset($delete[$j]);
                }
            }
        }

        foreach ( $create as $i => $l ) {
            $date = explode('/',$bill->date);
            $dateCarbon = Carbon::create(strval($date[2]-543), $date[1], $date[0]);
            $val = $this->valueOrder($l['value']);
            $branch_id = $bill->branch_id;
            Order::create([
                'bill_ref' => $bill->id,
                'date' => $bill->date,
                'date_' => $dateCarbon,
                'branch_id' => $branch_id,
                'customer_id' => $customerId,
                'user_id' => $bill->user_id,
                'job_id' => $l['job_id'],
                'amulet_id'=> $l['amulet_id'],
                'amount' => $val->amount,
                'price' => $val->price,
                'activate' => 1,
            ]);
        }

        foreach ( $delete as $i => $o ) {
            $data = Order::find($o['id']);
            $data->delete();
        }
    }

    protected function valueOrder($value)
    {
        $value_ = explode('/',$value);
        if (isset($value_[1])){
            return (object) [
                'amount' => $value_[0],
                'price' => $value_[1],
            ];
        } else {
            return (object) [
                'amount' => 1,
                'price' => $value_[0],
            ];
        }
    }

    protected function createPart($request,$bill)
    {
        $partList = json_decode($request->table_material , true);
        foreach ($partList as $i => $l){
            Part::Create([
                'bill_ref' => $bill->id,
                'material_id' => $l['material_id'],
                'price' => $l['value'],
            ]);
        }
    }

    protected function updatePart($request,$bill){
        $partList = json_decode($request->table_material , true);
        $partOld = Part::where('bill_ref', $bill->id )->get()->toArray();
        $create = $partList;
        $delete = $partOld;
        foreach ($partList as $i => $l){
            foreach ($partOld as $j => $o){
                $rule = $o['material_id'] == $l['material_id'];
                if ($rule){
                    $data = Part::find($o['id']);
                    $data->update([
                        'price' => $l['value'],
                    ]);
                    $data->save;
                    unset($create[$i]);
                    unset($delete[$j]);
                }
            }
        }

        foreach ( $create as $i => $l ) {
            Part::Create([
                'bill_ref' => $bill->id,
                'material_id' => $l['material_id'],
                'price' => $l['value'],
            ]);
        }

        foreach ( $delete as $i => $o ) {
            $data = Part::find($o['id']);
            $data->delete();
        }
    }

    protected function lastRecord()
    {
        return (DB::raw('SUBSTR(id, 3) AS id'))->orderBy('id', 'DESC')->first();
    }

    protected function createCustomer($request)
    {
        $customerId = $request->customer_id;
        if (strlen($request->phone) > 9) {
            $p = Customer::where([['phone', $request->phone],['activate', '=', 1 ]])->first();
            if ($customerId == 0 && empty($p)) {
                $customer = Customer::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'customer_type' => $request->customer_type,
                    'address' => $request->address,
                    'line' => $request->line,
                    'activate'=> 1,
                    'increment' => 0,
                    'already_used' => 0,
                ]);
                return $customer->id;
            } else {
                return $p->id;
            }
        } else {
            return 1;
        }

    }

    protected function createPayment($request,$bill)
    {
        $payment= [
            $request->pay_cash,
            $request->pay_credit,
            $request->pay_voucher,
            $request->pay_online,
            $request->pay_coupon
        ];

        $method = [
            'cash',
            'credit',
            'voucher',
            'online',
            'coupon'
        ];
        $branchId = $bill->branch_id;
        foreach ($payment as $i => $l){
            if ($l !== null){
                $cash = str_replace(',', '', $l);
                //dd($cash);
                Payment::Create([
                    'bill_ref' => $bill->id,
                    'method' => $method[$i],
                    'value' => $cash,
                    'activate' => 1,
                    'user_recive' => $request->user_id,
                    'branch_id' => $branchId
                ]);
            }
        }


    }

    protected function createGold($request,$bill){
        //dd($request);
        $branchId = $bill->branch_id;
        if ($request->craft_id_){
            $gold_1 = str_replace(',', '', $request->gold_);
            Gold::Create([
                'bill_ref' => $bill->id,
                'value' => $gold_1,
                'craft_id' => $request->craft_id_,
                'activate' => 1,
                'branch_id' => $branchId
            ]);
            $data = Bill::find($bill->id);
            $data->update([
                'gold' => 1
            ]);
            $data->save;
        }
        if ($request->craft_id_2){
            $gold_2 = str_replace(',', '', $request->gold_2);
            Gold::Create([
                'bill_ref' => $bill->id,
                'value' => $gold_2,
                'craft_id' => $request->craft_id_2,
                'activate' => 1,
                'branch_id' => $branchId
            ]);
            $data = Bill::find($bill->id);
            $data->update([
                'gold' => 1
            ]);
            $data->save;
        }

        if ($request->gold_input_check !== null){
            $data = Bill::find($bill->id);
            $data->update([
                'gold' => 0
            ]);
            $data->save;
        }

        if ($request->gold_input_check == null){
            $data = Bill::find($bill->id);
            $data->update([
                'gold' => 1
            ]);
            $data->save;
        }

    }

    protected function updateGold($request,$bill){
        //dd($request);
        $branchId = $bill->branch_id;
        if (!isset($request->gold_id_2) && isset($request->gold_id_)){
            if (isset($request->craft_id_2) && isset($request->gold_2)){
                $gold_2 = str_replace(',', '', $request->gold_2);
                Gold::Create([
                    'bill_ref' => $bill->id,
                    'value' => $gold_2,
                    'craft_id' => $request->craft_id_2,
                    'activate' => 1,
                    'branch_id' => $branchId
                ]);
            }
        }

        if (isset($request->gold_id_)){
            $gold = Gold::find($request->gold_id_);
            if (isset($request->gold_)){
                $gold_1 = str_replace(',', '', $request->gold_);
                $gold->update([
                    'bill_ref' => $bill->id,
                    'value' => $gold_1,
                    'craft_id' => $request->craft_id_,
                    'activate' => 1,
                    'branch_id' => $branchId
                ]);
                $gold->save();
            }
            else{
                $gold->delete();
                $data = Bill::find($bill->id);
                $data->update([
                    'gold' => 0
                ]);
                $data->save;
            }

        }
        if (isset($request->gold_id_2)){
            $gold = Gold::find($request->gold_id_2);
            if (isset($request->gold_2)){
                $gold_1 = str_replace(',', '', $request->gold_2);
                $gold->update([
                    'bill_ref' => $bill->id,
                    'value' => $gold_1,
                    'craft_id' => $request->craft_id_2,
                    'activate' => 1,
                    'branch_id' => $branchId
                ]);
                $gold->save();
            }
            else{
                $gold->delete();
            }
        }
    }

    protected function updateBillDeliver(Request $request)
    {
        $data = Bill::find((int)$request->bill_id);
        $data->update([
            'deliver' => 1,
        ]);
        $data->save;
        $request->session()->flash('success', 'ยืนยันการส่งงานบิล '.$data->bill_id.' เรียบร้อยแล้ว !');
        return back();
    }

    protected function checkPaymentRemain($request,$bill)
    {
        //dd($request);
        $pay = floatval($request->cash_val);
        $total = floatval($request->cost_current);
        $rule = $pay === $total;
        $data = Bill::find((int)$bill->id);

        if ($rule) {
            $data->update([
                'pay' => 1,
            ]);
        } else {
            $data->update([
                'pay' => 0,
            ]);
        }
        $data->save;

    }

    protected function voidPayment(Request $request)
    {
        //dd($request);
        $transaction = $request->t_id;
        $bill_id = $request->id;

        $data = Payment::find((int)$transaction);

        $bill = Bill::find((int)$bill_id);
        $cash = $bill->cash;
        $void = $data->value;

        $bill->update([
            'cash' => $cash - $void
        ]);
        $bill->save;

        $data->update([
            'cause' => $request->pay_cause,
            'activate' => 0,
            'user_void' => $request->user_id
        ]);
        $data->save;

        return back();

    }

    protected function cron_backup($branch_id){

        if($branch_id == 1){
            $export = new ExportController();
            $export->cron_backup();
        }


    }

    protected function init($request)
    {
        if (is_null($request->id)){
            return abort(404);
        }
    }
}
