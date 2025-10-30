@extends('layouts.app')

@section('content')
<section class="about-section py-5 text-center text-dark">
  <div class="container">
    <h2 class="fw-bold mb-4">About <span class="text-primary">HydroNova</span></h2>
    <p class="lead mb-5 text-muted">
      At HydroNova, we believe clean water is a right, not a privilege. 
      Our mission is to blend <strong>AI, IoT, and sustainable design</strong> 
      to make water purification smarter, greener, and more accessible.
    </p>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card border-0 shadow h-100">
          <div class="card-body">
            <i class="bi bi-droplet fs-1 text-primary"></i>
            <h5 class="fw-bold mt-3">Our Vision</h5>
            <p class="text-muted">To become a global leader in smart water management through innovation and technology.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow h-100">
          <div class="card-body">
            <i class="bi bi-lightbulb fs-1 text-success"></i>
            <h5 class="fw-bold mt-3">Our Mission</h5>
            <p class="text-muted">Empowering communities with intelligent purification systems and sustainable engineering solutions.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow h-100">
          <div class="card-body">
            <i class="bi bi-people fs-1 text-info"></i>
            <h5 class="fw-bold mt-3">Our Values</h5>
            <p class="text-muted">Integrity, Innovation, Sustainability, and Impact — the core of every project we deliver.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
