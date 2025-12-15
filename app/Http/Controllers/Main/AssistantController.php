<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Services\N8nAssistantClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AssistantController extends Controller
{
    public function __construct(private readonly N8nAssistantClient $client)
    {
    }

    public function index()
    {
        return view('main.assistant');
    }

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $sessionId = $request->session()->getId() ?: Str::uuid()->toString();

        $result = $this->client->send($validated['message'], $sessionId);

        if (!$result['ok'] || empty($result['output'])) {
            Log::error('Assistant webhook failed', [
                'sessionId' => $sessionId,
                'status_or_error' => $result['error'] ?? 'Empty response body from assistant',
                'webhook_url' => config('services.n8n.webhook_url'),
                'request_body' => $validated['message'],
            ]);

            return response()->json([
                'error' => 'We had trouble contacting the assistant. Please try again.',
            ], 502);
        }

        return response()->json([
            'output' => $result['output'],
            'threadId' => $result['threadId'],
        ]);
    }
}
