@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

<section id="hero" class="hn-hero">
  <div class="hn-hero-bg"></div>
  <div class="container hn-hero-inner">
    <div class="hn-hero-copy">
      <div class="hn-kicker fade-up" style="animation-delay: 0.05s;">HydroNova smart hydroponics</div>
      <h1 class="hn-title fade-up" style="animation-delay: 0.12s;">
        Grow cleaner, faster, and smarter with a connected hydroponic ecosystem.
      </h1>
      <p class="hn-subtitle fade-up" style="animation-delay: 0.18s;">
        HydroNova combines IoT modules, a mobile gateway, and a web platform so anyone can build, monitor,
        and optimize a hydroponic farm with confidence.
      </p>
      <div class="hn-cta fade-up" style="animation-delay: 0.26s;">
        <a href="/products" class="btn hn-btn hn-btn-primary">Explore Modules</a>
        <a href="/plans" class="btn hn-btn hn-btn-outline">View Planting Plans</a>
      </div>
      <div class="hn-trust fade-up" style="animation-delay: 0.34s;">
        <div class="hn-trust-item">
          <span class="hn-trust-value">24/7</span>
          <span class="hn-trust-label">Sensor monitoring</span>
        </div>
        <div class="hn-trust-item">
          <span class="hn-trust-value">Bluetooth</span>
          <span class="hn-trust-label">Mobile gateway sync</span>
        </div>
        <div class="hn-trust-item">
          <span class="hn-trust-value">Cloud</span>
          <span class="hn-trust-label">Centralized data</span>
        </div>
      </div>
    </div>
    <div class="hn-hero-stack fade-up" style="animation-delay: 0.2s;">
      <div class="hn-stack-card">
        <div class="hn-stack-icon"><i class="bi bi-cpu"></i></div>
        <div>
          <div class="hn-stack-title">Arduino + Sensors</div>
          <div class="hn-stack-text">pH, EC, temp, humidity, water level</div>
        </div>
      </div>
      <div class="hn-stack-card">
        <div class="hn-stack-icon"><i class="bi bi-bluetooth"></i></div>
        <div>
          <div class="hn-stack-title">Mobile Gateway</div>
          <div class="hn-stack-text">Bluetooth sync to cloud</div>
        </div>
      </div>
      <div class="hn-stack-card">
        <div class="hn-stack-icon"><i class="bi bi-cloud-check"></i></div>
        <div>
          <div class="hn-stack-title">Web Dashboard</div>
          <div class="hn-stack-text">Plans, modules, and analytics</div>
        </div>
      </div>
      <div class="hn-stack-card">
        <div class="hn-stack-icon"><i class="bi bi-phone"></i></div>
        <div>
          <div class="hn-stack-title">Mobile App</div>
          <div class="hn-stack-text">Live alerts and growth insights</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="ecosystem" class="hn-section hn-section-light">
  <div class="container">
    <div class="hn-section-head fade-up">
      <h2 class="hn-section-title">One ecosystem, four connected layers</h2>
      <p class="hn-section-subtitle">
        HydroNova links physical hardware to mobile and web software so every module, sensor, and plan is always in sync.
      </p>
    </div>
    <div class="row g-4">
      <div class="col-md-3 fade-up" style="animation-delay: 0.05s;">
        <div class="hn-card">
          <div class="hn-card-icon"><i class="bi bi-hdd-network"></i></div>
          <h5>Smart Modules</h5>
          <p>Customizable planting modules built for home or commercial setups.</p>
        </div>
      </div>
      <div class="col-md-3 fade-up" style="animation-delay: 0.1s;">
        <div class="hn-card">
          <div class="hn-card-icon"><i class="bi bi-activity"></i></div>
          <h5>Live Sensors</h5>
          <p>Environmental readings captured by Arduino and sensor arrays.</p>
        </div>
      </div>
      <div class="col-md-3 fade-up" style="animation-delay: 0.15s;">
        <div class="hn-card">
          <div class="hn-card-icon"><i class="bi bi-phone-vibrate"></i></div>
          <h5>Mobile Hub</h5>
          <p>Bluetooth gateway sends data and enables local control.</p>
        </div>
      </div>
      <div class="col-md-3 fade-up" style="animation-delay: 0.2s;">
        <div class="hn-card">
          <div class="hn-card-icon"><i class="bi bi-graph-up"></i></div>
          <h5>Cloud Platform</h5>
          <p>Centralized analytics, alerts, and plan management.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="workflow" class="hn-section hn-section-gradient">
  <div class="container">
    <div class="hn-section-head fade-up">
      <h2 class="hn-section-title text-white">How HydroNova works</h2>
      <p class="hn-section-subtitle text-white-50">
        A clean flow from hardware to cloud gives you clarity and control in minutes.
      </p>
    </div>
    <div class="hn-steps">
      <div class="hn-step fade-up" style="animation-delay: 0.05s;">
        <div class="hn-step-number">1</div>
        <div class="hn-step-body">
          <h5>Choose a module</h5>
          <p>Select a hydroponic kit and planting plan from the web store.</p>
        </div>
      </div>
      <div class="hn-step fade-up" style="animation-delay: 0.1s;">
        <div class="hn-step-number">2</div>
        <div class="hn-step-body">
          <h5>Connect the mobile app</h5>
          <p>The app pairs via Bluetooth and syncs sensor data to the cloud.</p>
        </div>
      </div>
      <div class="hn-step fade-up" style="animation-delay: 0.15s;">
        <div class="hn-step-number">3</div>
        <div class="hn-step-body">
          <h5>Track and optimize</h5>
          <p>View live insights, receive alerts, and follow plan guidance.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="platform" class="hn-section hn-section-light">
  <div class="container">
    <div class="hn-section-head fade-up">
      <h2 class="hn-section-title">Designed for growers at every level</h2>
      <p class="hn-section-subtitle">
        From beginners to commercial teams, HydroNova keeps the experience simple and scalable.
      </p>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 fade-up" style="animation-delay: 0.05s;">
        <div class="hn-card hn-card-soft">
          <div class="hn-card-label">Web Store</div>
          <h4>Build your system online</h4>
          <p>Configure modules, compare plans, and order replacements without guesswork.</p>
        </div>
      </div>
      <div class="col-lg-4 fade-up" style="animation-delay: 0.1s;">
        <div class="hn-card hn-card-soft">
          <div class="hn-card-label">Mobile App</div>
          <h4>Your smart hydroponic assistant</h4>
          <p>Monitor growth conditions, view trends, and get alerts anywhere.</p>
        </div>
      </div>
      <div class="col-lg-4 fade-up" style="animation-delay: 0.15s;">
        <div class="hn-card hn-card-soft">
          <div class="hn-card-label">Hardware Layer</div>
          <h4>Reliable sensor foundation</h4>
          <p>Arduino powered hardware ensures accurate readings and easy expansion.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="plans-preview" class="hn-section hn-section-dark">
  <div class="container">
    <div class="hn-section-head fade-up">
      <h2 class="hn-section-title text-white">Plans that guide the harvest</h2>
      <p class="hn-section-subtitle text-white-50">
        Each plan includes recommended modules, crop guidance, and monitoring targets.
      </p>
    </div>
    <div class="row g-4 justify-content-center">
      @php
        $plans = $plans ?? [];
        $planPreview = array_slice($plans, 0, 2, true);
      @endphp
      @forelse ($planPreview as $plan)
        <div class="col-md-5 fade-up">
          <div class="hn-plan-card">
            <div class="hn-plan-thumb">
              <img src="{{ !empty($plan['image_path']) ? asset('storage/' . $plan['image_path']) : ($plan['image_url'] ?? asset('images/hero_bg.jpg')) }}" alt="{{ $plan['name'] ?? 'Plan' }}">
            </div>
            <h4>{{ $plan['name'] ?? 'Plan' }}</h4>
            <p>{{ $plan['description'] ?? 'Flexible planting plan with guided targets.' }}</p>
            <ul>
              <li>Recommended module setup</li>
              <li>Sensor goals and checkpoints</li>
              <li>Mobile + web monitoring</li>
            </ul>
            <div class="fw-semibold mb-3">
              ${{ number_format((float)($plan['price'] ?? 0), 2) }} / mo
            </div>
            <a href="/plans" class="btn hn-btn hn-btn-outline-light">View Plan</a>
          </div>
        </div>
      @empty
        <div class="col-md-8 fade-up">
          <div class="hn-plan-card text-center">
            <h4>Plans coming soon</h4>
            <p>We are preparing plan bundles for every growing style.</p>
            <a href="/plans" class="btn hn-btn hn-btn-outline-light">View Plans</a>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</section>

<section id="cta" class="hn-section hn-section-light">
  <div class="container">
    <div class="hn-cta-panel fade-up">
      <div>
        <h2 class="hn-section-title">Ready to build your HydroNova system?</h2>
        <p class="hn-section-subtitle">
          Choose a module, pick a plan, and start tracking your first harvest.
        </p>
      </div>
      <div class="hn-cta-actions">
        <a href="/products" class="btn hn-btn hn-btn-primary">Browse Modules</a>
        <a href="/contact" class="btn hn-btn hn-btn-outline">Talk to the Team</a>
      </div>
    </div>
  </div>
</section>

<button id="scrollTopBtn" class="hn-scroll-top" type="button" aria-label="Scroll to top">
  <i class="bi bi-arrow-up"></i>
</button>

<style>
  :root {
    --hn-ink: #0b2a2a;
    --hn-forest: #0c4a3a;
    --hn-mint: #9ae6b4;
    --hn-teal: #1aa7a1;
    --hn-sand: #f3efe7;
    --hn-cream: #f9f7f2;
    --hn-sun: #f7b733;
    --hn-dark: #062022;
  }

  html {
    scroll-behavior: smooth;
  }

  body {
    font-family: "Space Grotesk", sans-serif;
    color: var(--hn-ink);
    background: #f6fbfb;
  }

  .hn-title,
  .hn-section-title {
    font-family: "Fraunces", serif;
  }

  .fade-up {
    opacity: 0;
    transform: translateY(14px);
    animation: fadeUp 0.8s ease forwards;
  }

  @keyframes fadeUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .hn-hero {
    position: relative;
    padding: 120px 0 90px;
    overflow: hidden;
  }

  .hn-hero-bg {
    position: absolute;
    inset: 0;
    background:
      linear-gradient(120deg, rgba(10, 58, 52, 0.9), rgba(10, 58, 52, 0.4)),
      url('{{ asset('images/1234.jpg') }}') center/cover no-repeat;
  }

  .hn-hero-inner {
    position: relative;
    display: grid;
    gap: 48px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    align-items: center;
  }

  .hn-hero-copy {
    color: #f2fffb;
  }

  .hn-kicker {
    display: inline-flex;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.2);
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-size: 0.75rem;
  }

  .hn-title {
    font-size: clamp(2.3rem, 4vw, 3.6rem);
    line-height: 1.1;
    margin: 16px 0;
  }

  .hn-subtitle {
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.82);
    max-width: 520px;
  }

  .hn-cta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin: 24px 0;
  }

  .hn-btn {
    border-radius: 999px;
    padding: 12px 26px;
    font-weight: 600;
  }

  .hn-btn-primary {
    background: var(--hn-mint);
    color: #07312f;
    border: none;
  }

  .hn-btn-primary:hover {
    background: #7adba2;
    color: #07312f;
  }

  .hn-btn-outline {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.6);
    color: #f6fffb;
  }

  .hn-btn-outline:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
  }

  .hn-btn-outline-light {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.6);
    color: #ffffff;
    border-radius: 999px;
    padding: 10px 24px;
    font-weight: 600;
  }

  .hn-trust {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    margin-top: 30px;
  }

  .hn-trust-item {
    display: grid;
    gap: 2px;
  }

  .hn-trust-value {
    font-weight: 700;
    font-size: 1.05rem;
  }

  .hn-trust-label {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .hn-hero-stack {
    display: grid;
    gap: 16px;
  }

  .hn-stack-card {
    background: rgba(255, 255, 255, 0.94);
    border-radius: 16px;
    padding: 16px;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 12px;
    box-shadow: 0 20px 40px rgba(8, 38, 34, 0.25);
  }

  .hn-stack-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: var(--hn-sand);
    display: grid;
    place-items: center;
    color: var(--hn-forest);
    font-size: 1.2rem;
  }

  .hn-stack-title {
    font-weight: 600;
  }

  .hn-stack-text {
    color: #4b6b66;
    font-size: 0.9rem;
  }

  .hn-section {
    padding: 90px 0;
  }

  .hn-section-light {
    background: var(--hn-cream);
  }

  .hn-section-dark {
    background: var(--hn-dark);
  }

  .hn-section-gradient {
    background: linear-gradient(135deg, #083c3a, #0f665d);
  }

  .hn-section-head {
    text-align: center;
    max-width: 720px;
    margin: 0 auto 50px;
  }

  .hn-section-title {
    font-size: clamp(2rem, 3vw, 2.8rem);
    margin-bottom: 12px;
  }

  .hn-section-subtitle {
    color: #566b6b;
  }

  .hn-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 18px 35px rgba(13, 49, 45, 0.08);
    height: 100%;
  }

  .hn-card h5 {
    margin-top: 12px;
    margin-bottom: 10px;
    font-weight: 600;
  }

  .hn-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 16px;
    background: var(--hn-sand);
    color: var(--hn-forest);
    display: grid;
    place-items: center;
    font-size: 1.4rem;
  }

  .hn-card-soft {
    background: linear-gradient(180deg, #ffffff, #f5fbf9);
  }

  .hn-card-label {
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 0.7rem;
    color: var(--hn-teal);
    font-weight: 700;
  }

  .hn-steps {
    display: grid;
    gap: 20px;
  }

  .hn-step {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 20px;
    background: rgba(255, 255, 255, 0.12);
    padding: 20px;
    border-radius: 16px;
    color: #ffffff;
  }

  .hn-step-number {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.2);
    display: grid;
    place-items: center;
    font-weight: 700;
  }

  .hn-step-body h5 {
    margin-bottom: 6px;
  }

  .hn-plan-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 28px;
    color: #ffffff;
    height: 100%;
    border: 1px solid rgba(255, 255, 255, 0.15);
  }

  .hn-plan-thumb {
    width: 100%;
    height: 160px;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
  }

  .hn-plan-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .hn-plan-card ul {
    padding-left: 18px;
    margin: 16px 0 20px;
  }

  .hn-cta-panel {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 20px 40px rgba(13, 49, 45, 0.08);
  }

  .hn-cta-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .hn-scroll-top {
    position: fixed;
    bottom: 30px;
    right: 24px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: var(--hn-forest);
    color: #ffffff;
    display: none;
    box-shadow: 0 12px 24px rgba(6, 32, 34, 0.3);
    z-index: 1000;
  }

  .hn-scroll-top:hover {
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .hn-hero {
      padding-top: 100px;
    }

    .hn-hero-inner {
      text-align: center;
    }

    .hn-subtitle {
      margin-left: auto;
      margin-right: auto;
    }

    .hn-cta {
      justify-content: center;
    }

    .hn-trust {
      justify-content: center;
    }
  }
</style>

<script>
  const scrollTopBtn = document.getElementById("scrollTopBtn");
  window.addEventListener("scroll", () => {
    if (!scrollTopBtn) return;
    scrollTopBtn.style.display = window.scrollY > 400 ? "grid" : "none";
  });
  if (scrollTopBtn) {
    scrollTopBtn.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }
</script>
@endsection
