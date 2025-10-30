<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class ProductController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display all products
     */
    public function index()
    {
        $products = $this->firebase->getAll('products');
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store new product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $this->firebase->getRef('products')->push($request->only('name', 'price'));

        // ✅ Correct route
        return redirect()->route('admin.products.index')->with('success', 'Product added successfully!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $product = $this->firebase->getRef('products/' . $id)->getValue();
        return view('admin.products.edit', compact('product', 'id'));
    }

    /**
     * Update existing product
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $this->firebase->getRef('products/' . $id)->update($request->only('name', 'price'));

        // ✅ Correct route
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $this->firebase->getRef('products/' . $id)->remove();

        // ✅ Correct route
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
