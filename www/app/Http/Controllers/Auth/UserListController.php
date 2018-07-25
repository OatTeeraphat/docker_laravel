<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserListController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listUsers = User::where('activate', '<>', 0)
            ->with(['roles','branch'])
            ->orderBy('created_at', 'asc')
            ->latest()
            ->get();
        return view('auth/user-list', compact('listUsers'));
    }


}
