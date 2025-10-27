<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;

class MainController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        return view('main.index');
    }

    public function products()
    {
        $products = $this->firebase->getAll('products');
        return view('main.products', compact('products'));
    }

    public function plans()
    {
        $plans = $this->firebase->getAll('plans');
        return view('main.plans', compact('plans'));
    }
}
