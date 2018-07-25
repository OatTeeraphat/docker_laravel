<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;

class CheckPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::find($request->user()->id);
        $pin = $user->pin;
        $old = ['email' => $user->name,'password' => '111111'];

        if($pin !== intval($request->cookie('PIN'))){

        Auth::logout($request);
        $request->session()->invalidate();

        return Redirect::to('/')
            ->withInput($old)
            ->withCookie(Cookie::forget('PIN'))
            ->with('pin', ['โปรดเข้าสู่ระบบใหม่ ด้วยรหัส PIN 4 ']);
        }

        return $next($request);

    }
}
