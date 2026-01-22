<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MobileSensorController extends Controller
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function ingest(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_id' => ['required', 'string', 'max:120'],
            'source' => ['required', 'string', 'max:40'],
            'ts' => ['required', 'integer'],
            'payload' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = $validator->validated();

        try {
            $this->firebase->getRef('sensor_ingests')->push([
                'user_id' => $user->id,
                'device_id' => $data['device_id'],
                'source' => $data['source'],
                'ts' => (int) $data['ts'],
                'payload' => $data['payload'],
                'created_at' => now()->toIso8601String(),
            ]);
        } catch (Throwable $e) {
            Log::error('mobile.sensor.ingest_failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
