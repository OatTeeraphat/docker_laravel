<?php

namespace App\Http\Controllers\Setting;

use App\Model\Amulet;
use App\Model\Job;
use App\Model\Material;
use App\Model\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $job    = Job::orderBy('order', 'asc')->get();
        $amulet = Amulet::orderBy('order', 'asc')->get();
        $material = Material::where('activate', 1)
                    ->orderBy('order', 'asc')->get();
        $setting = Setting::find(1);
        return view('setting/setting-main', compact('amulet','job','material','setting'));
    }

    public function order(Request $request)
    {
        $data = json_decode($request->data, true);

        array_filter($data, function($obj){

            if ($obj['amulet_id'] !== null){

                $amulet_ = Amulet::find((int)$obj['amulet_id']);
                $amulet_->update([
                    "order" => $obj['key']
                ]);
                $amulet_->save();

            } elseif ($obj['job_id'] !== null){

                $job = Job::find((int)$obj['job_id']);
                $job->update([
                    "order" => $obj['key']
                ]);
                $job->save();
            }

        });

        $request->session()->flash('success', 'แก้ไขหัวตารางเรียบร้อย !');

        return redirect()->back();

    }

    public function addJob(Request $request)
    {

        $job = Job::orderBy('id', 'desc')->limit(1)->get();
        //dd($job);
        Job::create([
            'name' => $request->name,
            'type' => 'well',
            'order' => ($job[0]->id)+1,
        ]);
        return redirect()->back();

    }

    public function updateJob(Request $request)
    {

        $data = Job::findOrFail($request->id);
        $data->update([
            'name' => $request->name,
        ]);
        $request->session()->flash('success', 'แก้ไข '.$data->name.' ในตารางรายการงานซ่อม เรียบร้อย');

        return back();

    }

    public function addAmulet(Request $request)
    {

        $job = Amulet::orderBy('id', 'desc')->limit(1)->get();
        //dd($job);
        Amulet::create([
            'name' => $request->name,
            'order' => ($job[0]->id)+1
        ]);
        return redirect()->back();

    }

    public function updateAmulet(Request $request)
    {

        $data = Amulet::findOrFail($request->id);
        $data->update([
            'name' => $request->name,
        ]);
        $request->session()->flash('success', 'แก้ไข '.$data->name.' ในตารางรายการงานซ่อม เรียบร้อย');

        return redirect()->back();

    }

    public function updateSetting(Request $request)
    {
        $data = Setting::findOrFail(1);
        $data->update([
            'head_r_1' => $request->head_r_1 ,
            'head_r_2' => $request->head_r_2 ,
            'btm_l_1' => $request->btm_l_1 ,
            'btm_l_2' => $request->btm_l_2 ,
            'btm_r_1' => $request->btm_r_1 ,
            'btm_r_2' => $request->btm_r_2
        ]);
        $request->session()->flash('success', 'แก้ไขข้อมูลบิลเรียบร้อย');

        return back();
    }

    protected function init($request)
    {
        if (is_null($request->id)){
            return abort(404);
        }
    }


}
