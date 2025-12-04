<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        try {
            $products = $this->firebase->getAll('products') ?? [];
        } catch (\Throwable $e) {
            return view('admin.products.index', ['products' => []])->with('error', $e->getMessage());
        }

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'model_3d'    => 'nullable|file|mimes:stl|max:204800',
        ]);

        $imagePath = null;
        $modelPath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products/images', 'public');
            }

            if ($request->hasFile('model_3d')) {
                $modelPath = $request->file('model_3d')->store('models', 'public');
            }

            $this->firebase->getRef('products')->push([
                'name'        => $validated['name'],
                'price'       => (float) $validated['price'],
                'description' => $validated['description'] ?? null,
                'image_path'  => $imagePath,
                'image_url'   => $imagePath ? asset('storage/' . $imagePath) : null,
                'model_3d'    => $modelPath,
                'model_3d_url'=> $this->modelUrl($modelPath),
                'created_at'  => now()->toDateTimeString(),
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            $this->deleteFile($imagePath);
            $this->deleteFile($modelPath);

            return back()->withInput()->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        try {
            $product = $this->firebase->getRef('products/' . $id)->getValue();
        } catch (\Throwable $e) {
            return redirect()->route('admin.products.index')->with('error', 'Failed to load product: ' . $e->getMessage());
        }

        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        return view('admin.products.edit', compact('product', 'id'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'model_3d'    => 'nullable|file|mimes:stl|max:204800',
        ]);

        try {
            $ref      = $this->firebase->getRef('products/' . $id);
            $existing = $ref->getValue();
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to update product: ' . $e->getMessage());
        }

        if (!$existing) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        $imagePath = $existing['image_path'] ?? null;
        $modelPath = $existing['model_3d'] ?? null;
        $imageUrl  = $existing['image_url'] ?? null;
        $modelUrl  = $existing['model_3d_url'] ?? null;

        try {
            if ($request->hasFile('image')) {
                $this->deleteFile($imagePath);
                $imagePath = $request->file('image')->store('products/images', 'public');
                $imageUrl  = asset('storage/' . $imagePath);
            } elseif ($imagePath) {
                $imageUrl = asset('storage/' . $imagePath);
            }

            if ($request->hasFile('model_3d')) {
                $this->deleteFile($modelPath);
                $modelPath = $request->file('model_3d')->store('models', 'public');
                $modelUrl  = $this->modelUrl($modelPath);
            } elseif ($modelPath) {
                $modelUrl = $this->modelUrl($modelPath);
            }

            $ref->update([
                'name'        => $validated['name'],
                'price'       => (float) $validated['price'],
                'description' => $validated['description'] ?? null,
                'image_path'  => $imagePath,
                'image_url'   => $imageUrl,
                'model_3d'    => $modelPath,
                'model_3d_url'=> $modelUrl,
                'updated_at'  => now()->toDateTimeString(),
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $ref      = $this->firebase->getRef('products/' . $id);
            $existing = $ref->getValue();
        } catch (\Throwable $e) {
            return redirect()->route('admin.products.index')->with('error', 'Failed to delete product: ' . $e->getMessage());
        }

        if (!$existing) {
            return redirect()->route('admin.products.index')->with('error', 'Product not found.');
        }

        $this->deleteFile($existing['image_path'] ?? null);
        $this->deleteFile($existing['model_3d'] ?? null);

        $ref->remove();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function modelUrl(?string $path): ?string
    {
        return $path ? asset('storage/' . $path) : null;
    }
}
