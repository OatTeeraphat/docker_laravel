<?php

namespace App\Http\Controllers\Material;

use App\Model\Material;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $material = Material::orderBy('order', 'asc')->get();
        return view('material/material-list', compact('material'));
    }

    public function order(Request $request)
    {
        $data = json_decode($request->data, true);

        array_filter($data, function($obj){

            if ($obj['job_id'] !== null){

                $job = Material::find((int)$obj['job_id']);
                $job->update([
                    "order" => $obj['key']
                ]);
                $job->save();
            }

        });

        $request->session()->flash('success', 'แก้ไขหัวตารางเรียบร้อย !');

        return redirect()->back();

    }

    public function deleteMaterial(Request $request)
    {
        $this->init($request);
        $data = Material::findOrFail($request->id);
        $data->update([
            'activate' => 0
        ]);
        $request->session()->flash('success', 'ลบ '.$data->name.' จากตารางรายการส่วนประกอบ !');

        return redirect()->back();

    }

    public function updateMaterial(Request $request)
    {
        $this->init($request);
        $data = Material::findOrFail($request->id);
        $data->update([
            'name' => $request->name,
        ]);
        $request->session()->flash('success', 'แก้ไข '.$data->name.' ในตารางรายการส่วนประกอบ เรียบร้อย');

        return redirect()->back();

    }

    public function addMaterial(Request $request)
    {

        $job = Material::orderBy('id', 'desc')->limit(1)->get();
        //dd($job);
        Material::create([
            'name' => $request->name,
            'activate' => 1,
            'order' => ($job[0]->id)+1,
        ]);
        return back();

    }

    protected function init($request)
    {
        if (is_null($request->id)){
            return abort(404);
        }
    }

}
