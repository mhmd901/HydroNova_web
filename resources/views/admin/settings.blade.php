@extends('layouts.admin')

@section('content')
<div class="container py-5">
  <div class="card p-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-gear text-info"></i> Admin Settings</h3>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="{{ $admin['username'] ?? '' }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
      </div>

      <button type="submit" class="btn btn-info text-white px-4"><i class="bi bi-save"></i> Save</button>
    </form>
  </div>
</div>
@endsection
