<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'string',
        ]);

        $payload = $request->only('name', 'price', 'description');
        $payload['product_ids'] = array_values((array) $request->input('product_ids', []));

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
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'string',
        ]);

        $payload = $request->only('name', 'price', 'description');
        $payload['product_ids'] = array_values((array) $request->input('product_ids', []));

        $this->firebase->getRef('plans/' . $id)->update($payload);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $this->firebase->getRef('plans/' . $id)->remove();

        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
