<?php

namespace App\Http\Controllers\AdminAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LoginAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin',['except'=>['logout']]);
    }
    public function loginForm()
    {
    	return view('auth.login');
    }

    public function login(Request $request)
    {
    	$request->validate([
    			'email'=>'required|email|exists:admins,email',
    			'password'=>'required|min:8',
    		]);

    	if(Auth::guard('admin')->attempt($request->only('email','password'), $request->filled('remember'))){
    		return redirect()->route('admin.home');
    	}

    	return back()->withErrors(['password'=>'Password not match.']);
    }

    public function logout()
    {
    	Auth::guard('admin')->logout();
    	return redirect()->route('admin.login');
    }
}
