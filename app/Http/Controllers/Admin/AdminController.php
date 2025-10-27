<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class AdminController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    // Show login form
    public function login()
    {
        return view('admin.login');
    }

    // Check login
    public function checkLogin(Request $request)
    {
        $request->validate([
            'username'=>'required',
            'password'=>'required',
        ]);

        if($request->username === 'admin' && $request->password === 'admin'){
            session(['admin_logged_in'=>true]);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    // Admin dashboard
    public function dashboard()
    {
        $products = $this->firebase->getAll('products');
        $plans = $this->firebase->getAll('plans');
        return view('admin.dashboard', compact('products','plans'));
    }

    // Logout
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login');
    }
}
