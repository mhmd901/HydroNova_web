<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Services\FirebaseService;

class StlController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Stream a product's STL from local storage (or redirect to external URL).
     */
    public function show(string $id)
    {
        $product = $this->firebase->getRef('products/' . $id)->getValue();
        if (!$product) {
            abort(404, 'Product not found');
        }

        $modelPath = $product['model_path'] ?? null;
        $modelUrl  = $product['model_url']  ?? null;

        // Prefer serving from local storage when available
        if ($modelPath && Storage::disk('public')->exists($modelPath)) {
            $absPath = storage_path('app/public/' . ltrim($modelPath, '/'));
            $filename = basename($absPath) ?: 'model.stl';
            // Inline view with STL content type
            return response()->file($absPath, [
                'Content-Type'        => 'model/stl',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Cache-Control'       => 'public, max-age=604800',
            ]);
        }

        // Fallback: proxy external URL through the server to avoid CORS in the browser
        if ($modelUrl) {
            try {
                $resp = Http::withOptions(['verify' => false])->get($modelUrl);
                if ($resp->ok()) {
                    $filename = basename(parse_url($modelUrl, PHP_URL_PATH) ?: 'model.stl') ?: 'model.stl';
                    return response($resp->body(), 200, [
                        'Content-Type'        => 'model/stl',
                        'Content-Disposition' => 'inline; filename="' . $filename . '"',
                        'Cache-Control'       => 'public, max-age=604800',
                    ]);
                }
            } catch (\Throwable $e) {
                // fall through to 404 below
            }
        }

        abort(404, '3D model not found');
    }
}
