<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class N8nAssistantClient
{
    /**
    * Send a message to the n8n assistant webhook.
    *
    * @return array{ok: bool, output: string|null, threadId: string|null, error: string|null}
    */
    public function send(string $message, ?string $sessionId = null): array
    {
        $webhookUrl = config('services.n8n.webhook_url');

        if (empty($webhookUrl)) {
            return [
                'ok' => false,
                'output' => null,
                'threadId' => null,
                'error' => 'Assistant webhook URL is not configured',
            ];
        }

        try {
            $payload = ['message' => $message];

            if (!empty($sessionId)) {
                $payload['sessionId'] = $sessionId;
            }

            $response = Http::timeout(30)
                ->retry(2, 300)
                ->acceptJson()
                ->asJson()
                ->post($webhookUrl, $payload);

            if ($response->failed()) {
                return [
                    'ok' => false,
                    'output' => null,
                    'threadId' => null,
                    'error' => sprintf(
                        'HTTP %s: %s',
                        $response->status(),
                        mb_substr($response->body(), 0, 2000)
                    ),
                ];
            }

            $data = $response->json() ?? [];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'output' => null,
                'threadId' => null,
                'error' => $exception->getMessage(),
            ];
        }

        $output = is_array($data) && isset($data['output']) && is_string($data['output'])
            ? trim($data['output'])
            : null;

        $threadId = is_array($data) && isset($data['threadId']) && is_string($data['threadId'])
            ? trim($data['threadId'])
            : null;

        if ($output === null) {
            return [
                'ok' => false,
                'output' => null,
                'threadId' => $threadId,
                'error' => 'Assistant returned an empty response',
            ];
        }

        return [
            'ok' => true,
            'output' => $output,
            'threadId' => $threadId,
            'error' => null,
        ];
    }
}
