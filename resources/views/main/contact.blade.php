@extends('layouts.app')

@section('content')
<section class="contact-section py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-dark">Get in Touch 💧</h2>
      <p class="text-muted">We’d love to hear from you! Fill out the form below and we’ll respond as soon as possible.</p>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0">
          <div class="card-body p-4">
            <form action="{{ route('main.contact.submit') }}" method="POST">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="name" class="form-label fw-semibold">Full Name</label>
                  <input type="text" name="name" id="name" class="form-control border-info" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label fw-semibold">Email</label>
                  <input type="email" name="email" id="email" class="form-control border-info" required>
                </div>
              </div>

              <div class="mt-3">
                <label for="subject" class="form-label fw-semibold">Subject</label>
                <input type="text" name="subject" id="subject" class="form-control border-info" required>
              </div>

              <div class="mt-3">
                <label for="message" class="form-label fw-semibold">Message</label>
                <textarea name="message" id="message" rows="5" class="form-control border-info" required></textarea>
              </div>

              <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4">
                  <i class="bi bi-send"></i> Send Message
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Contact Page Style --}}
<style>
  .contact-section {
    background: linear-gradient(180deg, #f0f9ff, #e0f7f3);
    min-height: 90vh;
  }
</style>
@endsection
