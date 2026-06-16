@extends('layouts.adminlte')

@section('title', 'Manage Users')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="headingfonts fw-bold text-uppercase mb-1">Available Users</h2>
                <p class="text-muted small">Viewing all Users Availble</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2">
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                placeholder="Search by name, email, or phone..." value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100 text-uppercase fw-bold">Search</button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-dark w-100" title="Add new User">
                            <i class="bi bi-plus"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold">Guest Details</th>
                                <th class="py-3 text-uppercase small fw-bold">Role</th>
                                <th class="py-3 text-uppercase small fw-bold">Contact Info</th>
                                <th class="py-3 text-uppercase small fw-bold text-center">Total Bookings</th>
                                <th class="py-3 text-uppercase small fw-bold">Member Since</th>
                                <th class="pe-4 py-3 text-end text-uppercase small fw-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-secondary text-white rounded-circle overflow-hidden d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px; flex-shrink: 0;">
                                                @if ($user->profile?->pic)
                                                    <img src="{{ asset('storage/' . $user->profile?->pic?->path) }}"
                                                        alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <span class="fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">ID:
                                                    #USER-{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'manager' ? 'bg-primary' : 'bg-secondary') }} text-uppercase px-2"
                                            style="font-size: 0.65rem;">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small"><i
                                                class="bi bi-envelope me-2 text-muted"></i>{{ $user->email }}</div>
                                        <div class="small mt-1"><i
                                                class="bi bi-telephone me-2 text-muted"></i>{{ $user->phone ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border border-danger fw-normal px-3 py-2">
                                            {{ $user->bookings_count }} Bookings
                                        </span>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        {{-- <a href="{{ route('manager.bookings.index', ['guest_id' => $user->id]) }}"
                                            class="btn btn-sm btn-outline-dark rounded-0 text-uppercase fw-bold"
                                            style="font-size: 0.7rem;">
                                            View History
                                        </a> --}}

                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="btn btn-white btn-sm border-end text-primary" title="Profile & Bookings">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="btn btn-white btn-sm border-end text-warning" title="Edit User">
                                            <i class="bi bi-pen"></i>
                                        </a>
                                        <button class="btn btn-white btn-sm text-danger"
                                            onclick="triggerBookingModal('{{ route('admin.users.destroy', $user->id) }}', 'Delete User', 'Are you sure you want to Delete this User from Server ?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <p class="text-muted mb-0">No guests found matching your search.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($users->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $users->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    </div>

    <x-alert-model title="Confirm Action">
        <div class="text-center p-3">
            {{-- Added IDs to ensure JS can find them --}}
            <i id="actionIcon" class="bi bi-question-circle text-primary display-4 mb-3"></i>
            <p id="actionMessage" class="mb-0 fw-semibold text-dark"></p>
            <p class="small text-muted mt-2">This action will update the booking logs immediately.</p>
        </div>
        <x-slot:action>
            <form action="" method="post" id="deleteForm">
                @csrf
                @method('DELETE')
                <input type="submit" value="Confirm" id="actionConfirmBtn" class="btn btn-primary px-4 rounded-pill">
            </form>
            {{-- <a id="actionConfirmBtn" href="#" class="btn btn-primary px-4 rounded-pill">Confirm</a> --}}
        </x-slot>
    </x-alert-model>
@endsection

@push('scripts')
    <script>
        function triggerBookingModal(url, title, message) {
            const modalEl = document.getElementById('staticBackdrop');

            const titleEl = modalEl.querySelector('.modal-title') || document.getElementById('bookingActionModalLabel');
            if (titleEl) titleEl.innerText = title;

            const msgEl = document.getElementById('actionMessage');
            const btnEl = document.getElementById('actionConfirmBtn');
            const form = document.getElementById('deleteForm');

            if (msgEl) msgEl.innerText = message;
            if (btnEl) form.action = url;

            const icon = document.getElementById('actionIcon');
            if (icon && btnEl) {
                btnEl.className = 'btn btn-danger px-4 rounded-pill';
                icon.className = 'bi bi-exclamation-triangle text-danger display-4 mb-3';
            }

            let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }
    </script>
@endpush
