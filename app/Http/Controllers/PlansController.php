<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class PlansController extends Controller
{
    protected $planModel;

    public function __construct()
    {
        $this->planModel = new Plan();
    }

    public function index()
    {
        $plans = $this->planModel->all();
        return view('plans.index', compact('plans'));
    }

    public function show($id)
    {
        $plan = $this->planModel->find($id);
        return view('plans.show', compact('plan', 'id'));
    }
}
