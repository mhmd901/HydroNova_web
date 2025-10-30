<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class MainController extends Controller
{
    protected $firebase;

    /**
     * Inject the Firebase service
     */
    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Show the HydroNova homepage
     */
    public function index()
    {
        return view('main.index');
    }

    /**
     * Display all products from Firebase
     */
    public function products()
    {
        $products = $this->firebase->getAll('products');
        return view('main.products', compact('products'));
    }

    /**
     * Display all plans from Firebase
     */
    public function plans()
    {
        $plans = $this->firebase->getAll('plans');
        return view('main.plans', compact('plans'));
    }

    /**
     * Show the contact form page
     */
    public function contact()
    {
        return view('main.contact');
    }

    /**
     * Handle contact form submissions and save to Firebase
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:5|max:1000',
        ]);

        // Save message to Firebase
        $this->firebase->getRef('messages')->push([
            'name'       => $request->name,
            'email'      => $request->email,
            'subject'    => $request->subject,
            'message'    => $request->message,
            'timestamp'  => now()->toDateTimeString(),
        ]);

        return redirect()->route('main.contact')
                         ->with('success', 'Your message has been sent successfully!');
    }

    /**
     * (Optional) About page
     */
    public function about()
    {
        return view('main.about');
    }
}
