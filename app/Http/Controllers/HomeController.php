<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laratrust\LaratrustFacade as Laratrust;
use Illuminate\Support\Facades\Auth;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Laratrust::hasRole('admin')) return $this->adminDashboard();
        if (Laratrust::hasRole('member')) return $this->memberDashboard();
        return view('home');
    }

    protected function memberDashboard()
    {
        $borrowLogs = Auth::user()->borrowLogs()->borrowed()->get();          //method borrowed diambli dari model user
        return view('dashboard.member', compact('borrowLogs')); //kirim ke view member
    }

    protected function adminDashboard()
    {
        return view('dashboard.admin');
    }
}
