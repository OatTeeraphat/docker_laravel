<?php

namespace App\Http\Controllers\Auth;

use App\Branch;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data,$type)
    {
        if ($type === 'c'){
            $rule = [
                'name' => 'required|string|max:255|unique:users,u_name,null,null,activate,1',
                'u_name' => 'required|string|unique:users,u_name,null,null,activate,1',
                'email' => 'required|string|email|max:255|unique:users',
                'branch_id' => 'required',
                'role_id' => 'required',
                'pin' => 'required|string|max:255',
            ];
        } else {
            $rule = [
                'name' => 'required|string|max:255|unique:users,name,'.$data['id'].',,activate,1',
                'u_name' => 'required|string|unique:users,u_name,'.$data['id'].',,activate,1',
                'branch_id' => 'required',
                'role_id' => 'required',
            ];
        }
        return  Validator::make($data,$rule);
    }

    protected function init($request)
    {
        if (is_null($request->id)){
            return abort(404);
        }
    }

    /**
     * Get Update user by paragm user_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->init($request);
        $user = User::with('roles')->get()->find($request->id);
        $listBranch = Branch::where('activate', '=', 1 )
            ->orderBy('id', 'desc')->get();
        $listRole  = Role::all();
        //dd($user);

        //dd($user);
        return view('auth/user-update', compact('user','listBranch','listRole'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $this->validator($request->all(),'u')->validate();
        $users = User::with('roles')->get()->find($request->id);
        $users->detachRole($users->roles[0]->id);
        $roles = Role::find((int)$request->role_id);
        $users->roles()->attach($roles);

        $user = User::find((int)$request->id);
        $user->update([
            'name' => $request->name,
            'u_name' => $request->u_name,
            'email' => $request->email,
            'branch_id' => $request->branch_id,
        ]);
        $user->save();

        $request->session()->flash('success', 'แก้ไข '.$request->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('/user');

    }

    public function delete(Request $request)
    {
        $this->init($request);
        $data = User::findOrFail($request->id);
        $data->update([
            'activate'=> 0
        ]);
        $request->session()->flash('success', 'ลบ '.$data->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('/user');
    }

    public function setPin(Request $request)
    {
        //dd($request);
        $data = User::findOrFail($request->id);
        $data->update([
            'pin'=> $request->pin
        ]);
        $request->session()->flash('success', 'แก้ไข PIN '.$data->name.' ในระบบเรียบร้อยแล้ว !');
        return redirect('/user');
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
        ]);
        $users = User::find((int)$request->id);
        $users->update([
            'password' => Hash::make($request->password),
        ]);
        $users->save();
        $request->session()->flash('success', 'เปลี่ยนรหัสผู้ใช้งาน '.$request->name.' ในระบบเรียบร้อยแล้ว !');

        return redirect('/user');
    }


}
