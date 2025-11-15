<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;

class NormalizeProductModels extends Command
{
    protected $signature = 'products:normalize-model-urls';
    protected $description = 'Ensure each product has a valid model_url derived from model_path when available';

    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        parent::__construct();
        $this->firebase = $firebase;
    }

    public function handle(): int
    {
        $products = $this->firebase->getAll('products') ?? [];
        $count = 0;
        foreach ($products as $id => $product) {
            $path = $product['model_path'] ?? null;
            $url  = $product['model_url'] ?? null;
            if ($path && Storage::disk('public')->exists($path)) {
                $newUrl = Storage::url($path);
                if ($newUrl !== $url) {
                    $this->firebase->getRef('products/' . $id)->update([
                        'model_url'  => $newUrl,
                        'updated_at' => now()->toISOString(),
                    ]);
                    $this->line("Updated {$id} -> {$newUrl}");
                    $count++;
                }
            }
        }
        $this->info("Normalized {$count} product(s).");
        return 0;
    }
}

