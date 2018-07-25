<?php

namespace App\Http\Controllers\Branch;

use App\Branch;
use App\Model\Craft;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $list = Branch::where('activate', '=', 1 )
                        ->orderBy('id', 'desc')->get();

        return view('branch/branch-list', compact('list'));
    }

    public function craftPage(Request $request)
    {
        $this->init($request);
        $branch = Branch::where([
            ['id', '=', $request->id ],
            ['activate', '=', 1 ]])->get();
        $craft = Craft::where([
            ['branch_id', '=', $request->id ],
            ['activate', '=', 1 ]])->get();
        return view('branch/branch-craft', compact('craft','branch'));
    }

    protected function init($request)
    {
        if (is_null($request->id)){
            return abort(404);
        }
    }

    public function craftCreate(Request $request)
    {
        //dd($request);
        Craft::create([
            'name' => $request->name,
            'branch_id' => $request->id,
            'activate' => 1,
        ]);

        $branch = Branch::where([
            ['id', '=', $request->id ],
            ['activate', '=', 1 ]])->get();

        $request->session()->flash('success', 'เพิ่มช่างทอง '.$request->name.' ในสาขา'.$branch[0]->name.' เรียบร้อยแล้ว !');

        return back();
    }

    public function craftDelete(Request $request)
    {
        $this->init($request);
        $data = Craft::findOrFail($request->id);
        $data->update([
            'activate' => 0
        ]);
        $request->session()->flash('success', 'ลบ '.$data->name.' ในระบบเรียบร้อยแล้ว !');

        return back();
    }

    public function pageCreate()
    {
        return view('branch/branch-create');
    }

    public function pageUpdate(Request $request)
    {
        $this->init($request);
        $item = Branch::all()->find($request->id);
        return view('branch/branch-update', compact('item'));
    }

    protected function validator(array $request)
    {
        return Validator::make($request, [
            'name' => 'required|max:255',
            'phone' => 'required|max:13',
            'address' => 'nullable|max:1000',
            'date_open' => 'required|max:50',
            'time_open' => 'required|max:50',
        ]);

    }

    protected function created($request)
    {
        Branch::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_open' => $request->date_open,
            'time_open' => $request->time_open,
            'increment'=> 0,
            'activate' => 1
        ]);

        $request->session()->flash('success', 'เพิ่มสาขา '.$request->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('branch');
    }

    protected function create(Request $request)
    {
        //dd($request->all());
        $this->validator($request->all())->validate();

        return $this->created($request)
            ?: redirect($this->redirectPath());
    }

    protected function updated($request)
    {
        $data = Branch::find((int)$request->id);
        $data->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'date_open' => $request->date_open,
            'time_open' => $request->time_open
        ]);
        $data->save();

        $request->session()->flash('success', 'แก้ไขข้อมูลสาขา '.$data->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('branch');
    }

    protected function update(Request $request)
    {

        $this->validator($request->all())->validate();

        return $this->updated($request)
            ?: redirect($this->redirectPath());
    }

    public function delete(Request $request)
    {
        $this->init($request);
        $data = Branch::findOrFail($request->id);
        $data->update([
            'activate' => 0
        ]);
        $request->session()->flash('success', 'ลบ '.$data->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('/branch');
    }

}
