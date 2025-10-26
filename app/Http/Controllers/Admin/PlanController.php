<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected $planModel;

    public function __construct()
    {
        $this->planModel = new Plan();
    }

    public function index()
    {
        $plans = $this->planModel->all();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $this->planModel->create($request->all());
        return redirect()->route('plans.index');
    }

    public function edit($id)
    {
        $plan = $this->planModel->find($id);
        return view('admin.plans.edit', compact('plan', 'id'));
    }

    public function update(Request $request, $id)
    {
        $this->planModel->update($id, $request->all());
        return redirect()->route('plans.index');
    }

    public function destroy($id)
    {
        $this->planModel->delete($id);
        return redirect()->route('plans.index');
    }
}
