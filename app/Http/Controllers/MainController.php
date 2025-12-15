<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $request->merge(['message' => trim($request->input('message', ''))]);

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:500',
        ]);

        $message = $validated['message'];
        $sessionId = session()->getId() ?: $request->ip() ?: 'hydronova-default-session';
        $webhookUrl = config('services.n8n.assistant_url');

        if (empty($webhookUrl)) {
            Log::error('Assistant webhook URL is not configured');

            return response()->json([
                'reply' => 'Assistant configuration error. Please try again soon.',
            ], 500);
        }

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(1, 200)
                ->acceptJson()
                ->post($webhookUrl, [
                    'message'   => $message,
                    'sessionId' => $sessionId,
                    'session_id' => $sessionId,
                ]);

            if ($response->failed()) {
                Log::warning('Assistant webhook returned non-success status', [
                    'status' => $response->status(),
                    'body_snippet' => mb_substr($response->body(), 0, 500),
                ]);

                throw new \RuntimeException('Assistant webhook failed with status '.$response->status());
            }

            $data = $response->json();
            $assistantReply = is_array($data) ? ($data['output'] ?? $data['reply'] ?? null) : null;

            if (!is_string($assistantReply) || trim($assistantReply) === '') {
                throw new \RuntimeException('Assistant webhook returned an empty reply');
            }

            return response()->json([
                'reply' => trim($assistantReply),
            ]);
        } catch (\Throwable $exception) {
            Log::error('Assistant webhook error', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'webhook_url' => $webhookUrl,
            ]);

            return response()->json([
                'reply' => 'HydroNova Assistant is temporarily unavailable. Please try again later.',
            ], 500);
        }
    }
}
