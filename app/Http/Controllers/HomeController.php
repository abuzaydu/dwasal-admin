<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $page = 'Welcome';
        return view('welcome-page', compact('page'));
    }

    public function revDashboard(Request $request)
    {
        $page = 'Dashboard';

        return view('shop.index', compact('page'));
    }
}
