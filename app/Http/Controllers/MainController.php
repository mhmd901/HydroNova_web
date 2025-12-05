<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

    /**
     * Show the AI assistant page
     */
    public function assistant()
    {
        return view('main.assistant');
    }

    /**
     * Proxy chat messages to the n8n assistant webhook
     */
    public function assistantChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');
        $sessionId = session()->getId() ?: 'hydronova-default-session';
        $webhookUrl = 'http://192.168.248.206:5678/webhook/hydronova-chat';

        try {
            $data = Http::post($webhookUrl, [
                'message'   => $message,
                'sessionId' => $sessionId,
            ])->throw()->json();

            $assistantReply = $data['output'] ?? null;

            return response()->json([
                'reply' => $assistantReply,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'reply' => 'HydroNova Assistant is temporarily unavailable. Please try again later.',
            ], 500);
        }
    }
}
