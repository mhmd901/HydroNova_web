<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FirebaseService;

class PlanController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index()
    {
        $plans = $this->firebase->getAll('plans');
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $products = $this->firebase->getAll('products') ?? [];
        return view('admin.plans.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'product_items' => 'nullable|array',
            'product_items.*' => 'nullable|integer|min:0',
        ]);

        $payload = $request->only('name', 'price', 'description');
        $imagePath = null;
        $rawItems = (array) $request->input('product_items', []);
        $items = [];
        foreach ($rawItems as $productId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $items[$productId] = $qty;
            }
        }
        $payload['product_items'] = $items;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('plans/images', 'public');
            $payload['image_path'] = $imagePath;
            $payload['image_url'] = asset('storage/' . $imagePath);
        }

        $this->firebase->getRef('plans')->push($payload);

        return redirect()->route('admin.plans.index')->with('success', 'Plan added successfully.');
    }

    public function edit($id)
    {
        $plan = $this->firebase->getRef('plans/' . $id)->getValue();
        $products = $this->firebase->getAll('products') ?? [];
        return view('admin.plans.edit', compact('plan', 'id', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'product_items' => 'nullable|array',
            'product_items.*' => 'nullable|integer|min:0',
        ]);

        $payload = $request->only('name', 'price', 'description');
        $existing = $this->firebase->getRef('plans/' . $id)->getValue();
        $imagePath = $existing['image_path'] ?? null;
        $rawItems = (array) $request->input('product_items', []);
        $items = [];
        foreach ($rawItems as $productId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $items[$productId] = $qty;
            }
        }
        $payload['product_items'] = $items;

        if ($request->hasFile('image')) {
            $this->deleteFile($imagePath);
            $imagePath = $request->file('image')->store('plans/images', 'public');
            $payload['image_path'] = $imagePath;
            $payload['image_url'] = asset('storage/' . $imagePath);
        } elseif ($imagePath) {
            $payload['image_path'] = $imagePath;
            $payload['image_url'] = asset('storage/' . $imagePath);
        }

        $this->firebase->getRef('plans/' . $id)->update($payload);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $existing = $this->firebase->getRef('plans/' . $id)->getValue();
        $this->deleteFile($existing['image_path'] ?? null);
        $this->firebase->getRef('plans/' . $id)->remove();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
