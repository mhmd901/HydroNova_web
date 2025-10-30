@extends('layouts.app')

@section('content')
{{-- Hero Section --}}
<section id="hero" class="text-center text-white d-flex align-items-center justify-content-center"
         style="height: 100vh; background: url('{{ asset('images/1234.jpg') }}') center/cover no-repeat;">
  <div data-aos="fade-up" data-aos-duration="1000">
    <img src="{{ asset('images/hydronova_logo.png') }}" alt="HydroNova" class="mb-4" style="width:150px;">
    <h1 class="fw-bold mb-3">Reinventing Water with <span class="text-info">Technology</span></h1>
    <p class="lead text-light mb-4">Smart. Sustainable. Scalable.</p>
    <a href="#about" class="btn btn-primary px-4 py-2 rounded-pill">Discover More</a>
  </div>
</section>

{{-- About Section --}}
<section id="about" class="py-5 bg-light text-dark text-center">
  <div class="container" data-aos="fade-up" data-aos-delay="200">
    <h2 class="fw-bold mb-3">About <span class="text-primary">HydroNova</span></h2>
    <p class="text-muted mb-5">
      HydroNova merges innovation, sustainability, and smart technology to transform how we treat and manage water.
      From AI-powered filtration to IoT monitoring — we deliver the future of clean water.
    </p>

    <div class="row g-4">
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
        <div class="card border-0 shadow h-100">
          <div class="card-body">
            <i class="bi bi-droplet fs-1 text-info"></i>
            <h5 class="fw-bold mt-3">Clean Innovation</h5>
            <p class="text-muted">Engineering smarter water systems using IoT and data-driven technology.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="400">
        <div class="card border-0 shadow h-100">
          <div class="card-body">
            <i class="bi bi-cpu fs-1 text-primary"></i>
            <h5 class="fw-bold mt-3">Smart Integration</h5>
            <p class="text-muted">From sensors to apps, our systems stay connected 24/7 for reliable results.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="zoom-in" data-aos-delay="500">
        <div class="card border-0 shadow h-100">
          <div class="card-body">
            <i class="bi bi-globe2 fs-1 text-success"></i>
            <h5 class="fw-bold mt-3">Global Impact</h5>
            <p class="text-muted">HydroNova projects support sustainable access to clean water worldwide.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Plans Section Preview --}}
<section id="plans" class="py-5 text-center bg-dark text-light">
  <div class="container" data-aos="fade-up">
    <h2 class="fw-bold mb-4">Our <span class="text-success">Plans</span></h2>
    <p class="text-muted mb-5">Choose the plan that suits your needs — whether for home, business, or industrial use.</p>

    <div class="row g-4 justify-content-center">
      <div class="col-md-4" data-aos="flip-left" data-aos-delay="200">
        <div class="card border-0 shadow-lg">
          <div class="card-body">
            <h4 class="fw-bold text-primary">Basic Plan</h4>
            <p class="text-muted">Perfect for small households and personal use.</p>
            <h3 class="fw-bold">$15/mo</h3>
            <a href="/plans" class="btn btn-outline-light mt-3">View Details</a>
          </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="flip-left" data-aos-delay="400">
        <div class="card border-0 shadow-lg">
          <div class="card-body">
            <h4 class="fw-bold text-success">Premium Plan</h4>
            <p class="text-muted">Smart analytics and IoT-powered filtration systems.</p>
            <h3 class="fw-bold">$45/mo</h3>
            <a href="/plans" class="btn btn-outline-light mt-3">View Details</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Contact CTA --}}
<section id="contact" class="py-5 text-center bg-primary text-white">
  <div class="container" data-aos="fade-up" data-aos-delay="200">
    <h2 class="fw-bold mb-3">Get in Touch 💧</h2>
    <p class="mb-4">Ready to bring smart water technology to your project?</p>
    <a href="/contact" class="btn btn-light rounded-pill px-4 py-2">Contact Us</a>
  </div>
</section>

{{-- Scroll to Top Button --}}
<button id="scrollTopBtn" class="btn btn-primary rounded-circle">
  <i class="bi bi-arrow-up"></i>
</button>

{{-- Styles --}}
<style>
  html {
    scroll-behavior: smooth;
  }
  #scrollTopBtn {
    position: fixed;
    bottom: 30px;
    right: 25px;
    display: none;
    z-index: 1000;
  }
  #scrollTopBtn:hover {
    transform: scale(1.1);
  }
  section {
    scroll-margin-top: 70px;
  }
</style>

{{-- AOS Animation & Scroll Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />

<script>
  // Initialize AOS
  AOS.init({ once: true, duration: 800 });

  // Scroll-to-top visibility
  const scrollTopBtn = document.getElementById("scrollTopBtn");
  window.addEventListener("scroll", () => {
    scrollTopBtn.style.display = window.scrollY > 400 ? "block" : "none";
  });
  scrollTopBtn.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
</script>
@endsection
