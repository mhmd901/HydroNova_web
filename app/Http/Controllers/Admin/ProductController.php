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

    public function index()
    {
        $products = $this->firebase->getAll('products');
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
        ]);

        $this->firebase->getRef('products')->push($request->only('name','price'));

        return redirect()->route('products.index')->with('success','Product added.');
    }

    public function edit($id)
    {
        $product = $this->firebase->getRef('products/'.$id)->getValue();
        return view('admin.products.edit', compact('product','id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
        ]);

        $this->firebase->getRef('products/'.$id)->update($request->only('name','price'));

        return redirect()->route('products.index')->with('success','Product updated.');
    }

    public function destroy($id)
    {
        $this->firebase->getRef('products/'.$id)->remove();
        return redirect()->route('products.index')->with('success','Product deleted.');
    }
}
