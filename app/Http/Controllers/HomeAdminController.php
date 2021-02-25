<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gate;

class HomeAdminController extends Controller
{
    public function index()
    {
    	return view('home');
    }

    public function add()
    {
    	Gate::authorize('level','super');
    	return 'Page for level super';
    }

    public function profile()
    {
    	Gate::authorize('level',['super','admin']);
    	return 'Page for level super & admin';
    }
}
