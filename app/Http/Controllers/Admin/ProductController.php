<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $this->productModel->create($request->all());
        return redirect()->route('products.index');
    }

    public function edit($id)
    {
        $product = $this->productModel->find($id);
        return view('admin.products.edit', compact('product', 'id'));
    }

    public function update(Request $request, $id)
    {
        $this->productModel->update($id, $request->all());
        return redirect()->route('products.index');
    }

    public function destroy($id)
    {
        $this->productModel->delete($id);
        return redirect()->route('products.index');
    }
}
