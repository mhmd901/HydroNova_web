@extends('layouts.app')

@section('content')
<section class="plans-section py-5 text-center text-dark">
  <div class="container">
    <h2 class="fw-bold mb-4">Our <span class="text-success">Plans</span></h2>
    <p class="text-muted mb-5">Pick the perfect HydroNova plan for your home, business, or organization.</p>

    <div class="row g-4 justify-content-center">
      @if(!empty($plans))
        @foreach($plans as $id => $plan)
          <div class="col-md-4" data-id="{{ $id }}">
            <div class="card plan-card border-0 shadow-lg h-100 overflow-hidden">
              <div class="image-wrapper position-relative">
                <img src="{{ $plan['image'] ?? asset('images/hero_bg.jpg') }}" 
                     class="card-img-top plan-image" alt="{{ $plan['name'] ?? 'Plan' }}">
                <button class="favorite-btn" onclick="togglePlanFavorite('{{ $id }}')">
                  <i class="bi bi-star"></i>
                </button>
              </div>
              <div class="card-body">
                <h4 class="fw-bold text-primary">{{ $plan['name'] ?? 'Unnamed Plan' }}</h4>
                <h2 class="fw-bold text-success">${{ $plan['price'] ?? '0' }}</h2>
                <p class="text-muted">{{ $plan['description'] ?? 'No description available.' }}</p>
                <a href="/contact" class="btn btn-outline-success rounded-pill mt-3">Get Started</a>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <p class="text-muted">No plans found at the moment.</p>
      @endif
    </div>
  </div>
</section>

<style>
  .plan-card {
    transition: all 0.3s ease;
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
  }
  .plan-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  }
  .plan-image {
    transition: transform 0.4s ease;
  }
  .plan-card:hover .plan-image {
    transform: scale(1.05);
  }
  .favorite-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255,255,255,0.8);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
  }
  .favorite-btn i {
    color: #ffc107;
    font-size: 1.2rem;
  }
  .favorite-btn.active {
    background: #ffc107;
  }
  .favorite-btn.active i {
    color: #fff;
  }
</style>

<script>
  // Handle Plan Favorites using localStorage
  const planFavorites = JSON.parse(localStorage.getItem('planFavorites') || '[]');

  function togglePlanFavorite(id) {
    const index = planFavorites.indexOf(id);
    if (index > -1) planFavorites.splice(index, 1);
    else planFavorites.push(id);

    localStorage.setItem('planFavorites', JSON.stringify(planFavorites));
    updatePlanFavoriteIcons();
  }

  function updatePlanFavoriteIcons() {
    document.querySelectorAll('.plan-card').forEach(card => {
      const id = card.parentElement.dataset.id;
      const btn = card.querySelector('.favorite-btn');
      if (planFavorites.includes(id)) btn.classList.add('active');
      else btn.classList.remove('active');
    });
  }

  document.addEventListener('DOMContentLoaded', updatePlanFavoriteIcons);
</script>
@endsection
