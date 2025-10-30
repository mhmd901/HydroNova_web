@extends('layouts.admin')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-white"><i class="bi bi-envelope-paper"></i> Messages Inbox</h2>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card bg-dark text-light shadow-lg border-0">
    <div class="card-body">
      @if(!empty($messages))
        <table class="table table-dark table-hover align-middle mb-0">
          <thead>
            <tr class="text-info">
              <th>#</th>
              <th>Sender</th>
              <th>Email</th>
              <th>Subject</th>
              <th>Date</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($messages as $id => $msg)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $msg['name'] ?? 'Unknown' }}</td>
                <td>{{ $msg['email'] ?? 'N/A' }}</td>
                <td>{{ $msg['subject'] ?? 'No subject' }}</td>
                <td>{{ $msg['timestamp'] ?? '—' }}</td>
                <td class="text-center">
                  {{-- View Modal Trigger --}}
                  <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $loop->iteration }}">
                    <i class="bi bi-eye"></i>
                  </button>

                  {{-- Delete Form --}}
                  <form action="{{ route('admin.messages.delete', $id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>

              {{-- Modal for Viewing Message --}}
              <div class="modal fade" id="viewModal{{ $loop->iteration }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content bg-dark text-light">
                    <div class="modal-header border-0">
                      <h5 class="modal-title"><i class="bi bi-envelope-open"></i> Message from {{ $msg['name'] ?? 'Unknown' }}</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Email:</strong> {{ $msg['email'] ?? 'N/A' }}</p>
                      <p><strong>Subject:</strong> {{ $msg['subject'] ?? 'No subject' }}</p>
                      <p><strong>Message:</strong></p>
                      <p class="border rounded p-3 bg-secondary bg-opacity-25">{{ $msg['message'] ?? '—' }}</p>
                      <small class="text-muted">Sent at: {{ $msg['timestamp'] ?? '' }}</small>
                    </div>
                    <div class="modal-footer border-0">
                      <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </tbody>
        </table>
      @else
        <p class="text-center text-muted mb-0">No messages found.</p>
      @endif
    </div>
  </div>
</div>
@endsection
