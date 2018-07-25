<?php

namespace App\Http\Controllers\Auth;

use App\Branch;
use App\User;
use App\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
                'password' => 'required|string|min:6|confirmed',
                'branch_id' => 'required',
                'role_id' => 'required',
                'pin' => 'required|string|max:255',
            ];
        } else {
            $rule = [
                'name' => 'required|string|max:255|unique:users,name,'.$data['id'].',,activate,1',
                'u_name' => 'required|string|unique:users,u_name,'.$data['id'].',,activate,1',
                'password' => 'required|string|min:6|confirmed',
                'branch_id' => 'required',
                'role_id' => 'required',
                'pin' => 'required|number|min:4|max:4',
            ];
        }
        return  Validator::make($data,$rule);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $this->validator($data,'c')->validate();
        return User::create([
            'name' => $data['name'],
            'u_name' => $data['u_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'branch_id' => $data['branch_id'],
            'status' => 0,
            'pin' => $data['pin'],
            'activate'=> 1
        ]);

    }

    protected function registered($data){

        $id = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->first()->id;

        $users = User::find($id);
        $roles = Role::find((int)$data['role_id']);

        return $users->attachRole($roles);

    }

    public function register(Request $request)
    {

        $this->validator($request->all(),'c')->validate();

        event(new Registered($user = $this->create($request->all())));

        //$this->guard()->login($user);

        $request->session()->flash('success', 'แก้ไขข้อมูลของ '.$request->name.' ในระบบเรียบร้อยแล้ว !');

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    public function index()
    {
        $listBranch = Branch::where('activate', '=', 1 )
            ->orderBy('id', 'desc')->get();
        $listRole = Role::all();
        return view('auth/register', compact('listBranch'),compact('listRole') );
    }
}
