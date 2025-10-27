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
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
        ]);

        $this->firebase->getRef('plans')->push($request->only('name','price'));

        return redirect()->route('plans.index')->with('success','Plan added.');
    }

    public function edit($id)
    {
        $plan = $this->firebase->getRef('plans/'.$id)->getValue();
        return view('admin.plans.edit', compact('plan','id'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
        ]);

        $this->firebase->getRef('plans/'.$id)->update($request->only('name','price'));

        return redirect()->route('plans.index')->with('success','Plan updated.');
    }

    public function destroy($id)
    {
        $this->firebase->getRef('plans/'.$id)->remove();
        return redirect()->route('plans.index')->with('success','Plan deleted.');
    }
}
