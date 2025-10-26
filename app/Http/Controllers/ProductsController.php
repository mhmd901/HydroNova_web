<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductsController extends Controller
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->all();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = $this->productModel->find($id);
        return view('products.show', compact('product', 'id'));
    }
}
