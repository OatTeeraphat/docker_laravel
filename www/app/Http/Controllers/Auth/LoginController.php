<?php
namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/';
    /**
     * Create a new controller instance.
     */

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }


    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticated( Request $request ) {

        $user = User::where( 'name', $request->email )->first();
        $status = $user->status;
        !isset($request->pin) ?: Cookie::queue(Cookie::make('PIN', $request->pin , 60 * 24 * 365 ));

        if ( $status === 0 ){
            $user->status = 1;
            $user->save();
        } else {
            Auth::logoutOtherDevices($request->password);
        }

        $request->session()->flash('check-user', 'เข้าสู่ระบบแล้ว' . $user->u_name);
        return redirect('/bill');

    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request){

        $id = Auth::user()->id;
        $user = User::find($id);
        $user->status = 0;
        $user->save();

        auth()->logout();
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');

    }

    public function logoutUsingID(Request $request){

        $id = Auth::user()->id;
        $user = User::find($id);
        $user->status = 0;
        $user->save();

        auth()->logout();
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');

    }

    public function checkHasPin(Request $request)
    {
        $data = explode('!',$request->data);
        $user = User::where( 'name', $data[0] )->first();
        $pin = $user->pin;

        if ($pin !== intval($data[1])){
            return 'false';
        } else {
            return 'true';
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            'name' => $request->get($this->username()),
            'password' => $request->password,
            'activate' => 1,
        ];

    }

    /**
    * Method override to send correct errors messages
    * Get the failed login response instance. (** email is passed variable form name)
    *
    * @param \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    protected function sendFailedLoginResponse(Request $request)
    {

        if ( ! User::where('name', $request->email)
                ->first() ) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => Lang::get('ไม่พบผู้ใช้งานนี้ในระบบ'),
                ]);
        }

        if ( ! User::where([['name', $request->email],['activate', '=', 1]])
                ->first() ) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => Lang::get('ผู้ใช้งานถูกระงับ โปรดติดต่อผู้ดูแลระบบ'),
                ]);
        }

        if ( ! User::where([['name', $request->email],['password', bcrypt($request->password)]])
                ->first() ) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    'password' => Lang::get('รหัสผ่านของผู้ใช้งานนี้ ไม่ถูกต้อง'),
                ]);
        }


    }


}