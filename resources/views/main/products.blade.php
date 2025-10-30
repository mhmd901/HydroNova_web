@extends('layouts.app')

@section('content')
<section class="products-section py-5 text-center text-dark">
  <div class="container">
    <h2 class="fw-bold mb-4">Our <span class="text-primary">Products</span></h2>
    <p class="text-muted mb-5">Explore HydroNova’s smart water solutions powered by sustainable technology.</p>

    <div class="row g-4">
      @if(!empty($products))
        @foreach($products as $id => $product)
          <div class="col-md-4" data-id="{{ $id }}">
            <div class="card product-card border-0 shadow-lg h-100 overflow-hidden">
              <div class="image-wrapper position-relative">
                <img src="{{ $product['image'] ?? asset('images/hero_bg.jpg') }}" 
                     class="card-img-top product-image" alt="{{ $product['name'] ?? 'Product' }}">
                <button class="favorite-btn" onclick="toggleFavorite('{{ $id }}')">
                  <i class="bi bi-heart"></i>
                </button>
              </div>
              <div class="card-body">
                <h5 class="fw-bold text-dark">{{ $product['name'] ?? 'Unnamed Product' }}</h5>
                <p class="text-muted small">Smart IoT-enabled water technology designed for efficiency and impact.</p>
                <h5 class="text-primary fw-bold mb-3">${{ $product['price'] ?? '0' }}</h5>
                <a href="#contact" class="btn btn-outline-primary rounded-pill">Learn More</a>
              </div>
            </div>
          </div>
        @endforeach
      @else
        <p class="text-muted">No products available at this time.</p>
      @endif
    </div>
  </div>
</section>

<style>
  .product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: rgba(255,255,255,0.9);
    border-radius: 16px;
  }
  .product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
  }
  .image-wrapper {
    position: relative;
    overflow: hidden;
  }
  .product-image {
    transition: transform 0.4s ease;
  }
  .product-card:hover .product-image {
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
    color: #ff4b5c;
    font-size: 1.2rem;
  }
  .favorite-btn.active {
    background: #ff4b5c;
  }
  .favorite-btn.active i {
    color: #fff;
  }
</style>

<script>
  // Handle Favorites using localStorage
  const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');

  function toggleFavorite(id) {
    const index = favorites.indexOf(id);
    if (index > -1) favorites.splice(index, 1);
    else favorites.push(id);

    localStorage.setItem('favorites', JSON.stringify(favorites));
    updateFavoriteIcons();
  }

  function updateFavoriteIcons() {
    document.querySelectorAll('.product-card').forEach(card => {
      const id = card.parentElement.dataset.id;
      const btn = card.querySelector('.favorite-btn');
      if (favorites.includes(id)) btn.classList.add('active');
      else btn.classList.remove('active');
    });
  }

  document.addEventListener('DOMContentLoaded', updateFavoriteIcons);
</script>
@endsection
