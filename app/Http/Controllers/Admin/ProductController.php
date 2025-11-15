<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;

class ProductController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * List products.
     */
    public function index()
    {
        try {
            $products = $this->firebase->getAll('products') ?? [];
            return view('admin.products.index', compact('products'));
        } catch (\Throwable $e) {
            return view('admin.products.index', ['products' => []])
                ->with('error', 'Failed to load products: ' . $e->getMessage());
        }
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'model_3d'    => [
                'nullable',
                'file',
                'max:51200',
                function ($attribute, $file, $fail) {
                    if ($file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (!in_array($ext, ['stl', 'obj', 'glb'])) {
                            $fail('The model 3d must be a file of type: stl, obj, glb.');
                        }
                    }
                },
            ],
        ]);

        $imagePath = null;
        $imageUrl  = null;
        $modelPath = null;
        $modelUrl  = null;

        try {
            // Product image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products/images', 'public');
                $imageUrl  = asset('storage/' . $imagePath);
            }

            // 3D model upload
            if ($request->hasFile('model_3d')) {
                $modelPath = $request->file('model_3d')->store('products/models', 'public');
                $modelUrl  = asset('storage/' . $modelPath);
            }

            $payload = [
                'name'        => $request->name,
                'price'       => (float) $request->price,
                'description' => $request->description,
                'image_url'   => $imageUrl,
                'image_path'  => $imagePath,
                'model_url'   => $modelUrl,
                'model_path'  => $modelPath,
                'created_at'  => now()->toDateTimeString(),
            ];

            $this->firebase->getRef('products')->push($payload);

            return redirect()->route('admin.products.index')
                             ->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {
            // Clean up uploaded files on failure
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            if ($modelPath && Storage::disk('public')->exists($modelPath)) {
                Storage::disk('public')->delete($modelPath);
            }

            return redirect()->back()->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        try {
            $product = $this->firebase->getRef('products/' . $id)->getValue();

            if (!$product) {
                return redirect()->route('admin.products.index')
                                 ->with('error', 'Product not found.');
            }

            return view('admin.products.edit', compact('product', 'id'));
        } catch (\Throwable $e) {
            return redirect()->route('admin.products.index')
                             ->with('error', 'Failed to load product: ' . $e->getMessage());
        }
    }

    /**
     * Update a product.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'price'       => 'required|numeric',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'model_3d'    => [
                'nullable',
                'file',
                'max:51200',
                function ($attribute, $file, $fail) {
                    if ($file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (!in_array($ext, ['stl', 'obj', 'glb'])) {
                            $fail('The model 3d must be a file of type: stl, obj, glb.');
                        }
                    }
                },
            ],
        ]);

        try {
            $ref      = $this->firebase->getRef('products/' . $id);
            $existing = $ref->getValue();

            if (!$existing) {
                return redirect()->route('admin.products.index')
                                 ->with('error', 'Product not found.');
            }

            // Start with existing values
            $imagePath = $existing['image_path'] ?? null;
            $imageUrl  = $existing['image_url']  ?? null;
            $modelPath = $existing['model_path'] ?? null;
            $modelUrl  = $existing['model_url']  ?? null;

            // Replace image if uploaded
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('products/images', 'public');
                $imageUrl  = asset('storage/' . $imagePath);
            }

            // Replace model if uploaded
            if ($request->hasFile('model_3d')) {
                if ($modelPath && Storage::disk('public')->exists($modelPath)) {
                    Storage::disk('public')->delete($modelPath);
                }
                $modelPath = $request->file('model_3d')->store('products/models', 'public');
                $modelUrl  = asset('storage/' . $modelPath);
            }

            $payload = [
                'name'        => $request->name,
                'price'       => (float) $request->price,
                'description' => $request->description,
                'image_url'   => $imageUrl,
                'image_path'  => $imagePath,
                'model_url'   => $modelUrl,
                'model_path'  => $modelPath,
                'updated_at'  => now()->toDateTimeString(),
            ];

            $ref->update($payload);

            return redirect()->route('admin.products.index')
                             ->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()
                           ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Delete a product.
     */
    public function destroy($id)
    {
        try {
            $ref      = $this->firebase->getRef('products/' . $id);
            $existing = $ref->getValue();

            if (!$existing) {
                return redirect()->route('admin.products.index')
                                 ->with('error', 'Product not found.');
            }

            // Clean up stored image
            if (!empty($existing['image_path']) && Storage::disk('public')->exists($existing['image_path'])) {
                Storage::disk('public')->delete($existing['image_path']);
            }
            // Clean up stored model
            if (!empty($existing['model_path']) && Storage::disk('public')->exists($existing['model_path'])) {
                Storage::disk('public')->delete($existing['model_path']);
            }

            $ref->remove();

            return redirect()->route('admin.products.index')
                             ->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.products.index')
                             ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
