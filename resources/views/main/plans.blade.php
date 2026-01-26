@extends('layouts.app')

@section('content')
<section class="plans-section py-5 text-center text-dark">
  <div class="container">
    @php
      $products = $products ?? [];
    @endphp
    <h2 class="fw-bold mb-4">Our <span class="text-success">Plans</span></h2>
    <p class="text-muted mb-5">Pick the perfect HydroNova plan for your home, business, or organization.</p>

    <div class="row g-4 justify-content-center">
      @if(!empty($plans))
        @foreach($plans as $id => $plan)
          <div class="col-md-4" data-id="{{ $id }}">
            @php
              $includedItems = $plan['product_items'] ?? ($plan['product_ids'] ?? []);
              if (is_object($includedItems)) {
                  $includedItems = (array) $includedItems;
              } elseif (!is_array($includedItems)) {
                  $includedItems = [];
              }

              $includedValues = array_values($includedItems);
              $includedKeys = array_keys($includedItems);
              $hasBoolMap = !empty($includedValues)
                  && count(array_filter($includedValues, 'is_bool')) === count($includedValues);

              if ($hasBoolMap) {
                  $includedItems = array_fill_keys($includedKeys, 1);
              } elseif (!array_is_list($includedItems)) {
                  $normalizedItems = [];
                  foreach ($includedItems as $productId => $qty) {
                      $qty = (int) $qty;
                      if ($qty > 0) {
                          $normalizedItems[$productId] = $qty;
                      }
                  }
                  $includedItems = $normalizedItems;
              } else {
                  $includedItems = array_fill_keys($includedItems, 1);
              }
            @endphp
            <div class="card plan-card border-0 shadow-lg h-100 overflow-hidden plan-flip" data-plan-flip>
              <div class="plan-flip-inner">
                <div class="plan-flip-face plan-flip-front">
                  <div class="image-wrapper position-relative">
                    <img src="{{ !empty($plan['image_path']) ? asset('storage/' . $plan['image_path']) : ($plan['image_url'] ?? ($plan['image'] ?? asset('images/hero_bg.jpg'))) }}"
                         class="card-img-top plan-image" alt="{{ $plan['name'] ?? 'Plan' }}">
                    <button class="favorite-btn" onclick="togglePlanFavorite('{{ $id }}')">
                      <i class="bi bi-star"></i>
                    </button>
                    <button class="flip-btn" type="button" data-flip-toggle>
                      Details
                    </button>
                  </div>
                  <div class="card-body">
                    <h4 class="fw-bold text-primary">{{ $plan['name'] ?? 'Unnamed Plan' }}</h4>
                    <h2 class="fw-bold text-success">${{ $plan['price'] ?? '0' }}</h2>
                    <a href="/contact" class="btn btn-outline-success rounded-pill mt-3">Get Started</a>
                  </div>
                </div>
                <div class="plan-flip-face plan-flip-back">
                  <div class="card-body text-start">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                      <h5 class="fw-bold text-dark mb-0">Plan Details</h5>
                      <button class="flip-btn flip-btn-outline" type="button" data-flip-toggle>
                        Back
                      </button>
                    </div>
                    <p class="text-muted">{{ $plan['description'] ?? 'No description available.' }}</p>
                    @if (!empty($includedItems))
                      <div class="small text-muted">
                        <div class="fw-semibold text-dark mb-2">Includes:</div>
                        @foreach ($includedItems as $productId => $qty)
                          @php
                            $product = $products[$productId] ?? null;
                          @endphp
                          @if ($product)
                            <div class="d-flex align-items-start gap-2 mb-1">
                              <i class="bi bi-check-circle-fill text-success"></i>
                              <span>
                                {{ $product['name'] ?? 'Unnamed Product' }}
                                @if ($qty > 1)
                                  <span class="text-muted">x{{ $qty }}</span>
                                @endif
                              </span>
                            </div>
                          @endif
                        @endforeach
                      </div>
                    @else
                      <div class="small text-muted">No products included.</div>
                    @endif
                  </div>
                </div>
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
  .plan-flip {
    perspective: 1200px;
  }
  .plan-flip-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transform-style: preserve-3d;
    transition: transform 0.6s ease;
  }
  .plan-flip.is-flipped .plan-flip-inner {
    transform: rotateY(180deg);
  }
  .plan-flip-face {
    position: relative;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    flex-direction: column;
    background: rgba(255,255,255,0.95);
    border-radius: 16px;
    overflow: hidden;
  }
  .plan-flip-back {
    position: absolute;
    inset: 0;
    transform: rotateY(180deg);
  }
  .flip-btn {
    position: absolute;
    left: 12px;
    top: 12px;
    border: none;
    border-radius: 999px;
    padding: 6px 12px;
    background: rgba(255,255,255,0.9);
    font-weight: 600;
    font-size: 0.85rem;
  }
  .flip-btn-outline {
    position: static;
    border: 1px solid #198754;
    color: #198754;
    background: #fff;
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

  function initPlanFlips() {
    document.querySelectorAll('[data-plan-flip]').forEach(card => {
      card.querySelectorAll('[data-flip-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
          card.classList.toggle('is-flipped');
        });
      });
    });
  }

  document.addEventListener('DOMContentLoaded', updatePlanFavoriteIcons);
  document.addEventListener('DOMContentLoaded', initPlanFlips);
</script>
@endsection
