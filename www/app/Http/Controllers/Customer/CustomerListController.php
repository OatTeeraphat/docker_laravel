<?php

namespace App\Http\Controllers\Customer;

use App\Model\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerListController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $customerType = array('customer' => 'บุคคล','company'=>'บริษัท');

        $listCustomer = Customer::where('activate', '=', 1 )
            ->orderBy('created_at', 'desc')->get();

        return view('customer/customer-list', compact('listCustomer','customerType'));
    }

    public function pageCreate()
    {
        $customerType = array(
            (object)[ 'type' => 'customer', 'desc' => 'ลูกค้าบุคคล', 'default' => true ],
            (object)[ 'type' => 'company', 'desc' => 'ลูกค้าบริษัท', 'default' => false ]
        );
        return view('customer/customer-create', compact('customerType'));
    }

    public function pageUpdate(Request $request)
    {
        $this->init($request);
        $customer = Customer::all()->find($request->id);
        $customerType = array(
            (object)[ 'type' => 'customer', 'desc' => 'ลูกค้าบุคคล', 'default' => true ],
            (object)[ 'type' => 'company', 'desc' => 'ลูกค้าบริษัท', 'default' => false ]
        );
        return view('customer/customer-update', compact('customerType','customer'));
    }

    protected function init($request)
    {
        if (is_null($request->id)){
            return abort(404);
        }
    }

    protected function validator(array $request,$type)
    {
        if ($type === 'c'){
            $rule = [
                'name' => 'required|max:255',
                'phone' => 'required|max:12|unique:customer,phone,null,null,activate,1',
                'customer_type' => 'required',
                'address' => 'nullable|max:1000',
                'line' => 'nullable|max:255|unique:customer,line,null,null,activate,1',
            ];
        } else {
            $rule = [
                'name' => 'required|max:255',
                'phone' => 'required|max:12|unique:customer,phone,'.$request['id'].',,activate,1' ,
                'customer_type' => 'required',
                'address' => 'nullable|max:1000',
                'line' => 'nullable|max:255|unique:customer,line,'.$request['id'].',,activate,1',
            ];
        }
        return  Validator::make($request,$rule);

    }

    protected function created($request)
    {
        //dd($request);
        $is_already_used = isset($request->already_used) ? 1 : 0;
        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'customer_type' => $request->customer_type,
            'address' => $request->address,
            'line' => $request->line,
            'activate' => 1,
            'increment' => 0,
            'already_used' => $is_already_used
        ]);

        $request->session()->flash('success', 'เพิ่มลูกค้า ในระบบเรียบร้อยแล้ว !');

    }

    protected function updated($request)
    {
        $is_already_used = isset($request->already_used) ? 1 : 0;
        $data = Customer::find((int)$request->id);
        $data->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'customer_type' => $request->customer_type,
            'address' => $request->address,
            'line' => $request->line,
            'already_used' => $is_already_used
        ]);
        $data->save();

        $request->session()->flash('success', 'แก้ไขข้อมูลลูกค้า '.$data->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('customer');
    }

    public function createWell(Request $request)
    {
        $is_already_used = isset($request['already_used']) ? 1 : 0;
        $well = Customer::create([
            'name' => $request['name'],
            's_name' => $request['s_name'],
            'company_name' => $request['company_name'],
            'phone' => $request['phone'],
            'customer_type' => $request['customer_type'],
            'address' => $request['address'],
            'line' => $request['line'],
            'activate' => 1,
            'increment' => 0,
            'already_used' => $is_already_used
        ]);

        return response()->json($well);

    }

    protected function create(Request $request)
    {
        $this->validator($request->all(),'c')->validate();

        return $this->created($request)
         ?: redirect('customer');

    }

    protected function update(Request $request)
    {
        $this->validator($request->all(),'u')->validate();

        return $this->updated($request)
            ?: redirect($this->redirectPath());
    }

    public function delete(Request $request)
    {
        $this->init($request);
        $data = Customer::findOrFail($request->id);
        $data->update([
            'activate' => 0
        ]);
        $request->session()->flash('success', 'ลบ '.$data->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('/customer');
    }

    public function search(request $request){

        $customer = Customer::where([['phone', $request->phone],['activate', '=', 1]] )->get();
        return $customer;

    }

    public function searchWell(request $request){

        $customer = Customer::where([['name','LIKE', '%'.$request->name.'%'],['activate', '=', 1]])->get();
        return $customer;

    }

}

