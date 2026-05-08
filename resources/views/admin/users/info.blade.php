@extends('layouts.adminlte')

@section('title', 'User Profile | ' . $user->name)

@section('content')
    <div class="container py-5">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-dark small fw-bold">
                <i class="bi bi-arrow-left me-1"></i> BACK TO DIRECTORY
            </a>
        </div>

        <div class="row">
            <!-- Sidebar: User Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <!-- Avatar Section -->
                        <div class="avatar-lg mx-auto mb-3 bg-secondary text-white rounded-circle overflow-hidden d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            @if ($user->profile?->pic)
                                <img src="{{ asset('storage/' . $user->profile->pic->path) }}"
                                    class="w-100 h-100 object-fit-cover">
                            @else
                                <h1 class="mb-0">{{ strtoupper(substr($user->name, 0, 1)) }}</h1>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-1 text-capitalize">{{ $user->name }}</h4>
                        <p class="text-muted small mb-2">{{ $user->email }}</p>

                        <div class="mb-4">
                            <span class="badge bg-light text-dark border px-3 py-2 text-uppercase"
                                style="font-size: 0.7rem;">
                                {{ $user->role }}
                            </span>
                            @if ($user->role === 'admin' && $user->id === auth()->user()->id)
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-sm btn-warning fw-bold text-uppercase" style="font-size: 0.7rem;">
                                    Update
                                </a>
                            @endif
                        </div>


                        @if ($user->role !== 'admin')
                            <div class="d-flex flex-wrap justify-content-center gap-2 mb-2">


                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-dark dropdown-toggle fw-bold text-uppercase"
                                        type="button" data-bs-toggle="dropdown" style="font-size: 0.7rem;">
                                        Change Role
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border-0">
                                        <li>
                                            <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="role" value="manager">
                                                <button type="submit"
                                                    class="dropdown-item small  {{ $user->role === 'manager' ? 'active' : '' }}">Make
                                                    Manager</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="role" value="customer">
                                                <button type="submit"
                                                    class="dropdown-item small {{ $user->role === 'customer' ? 'active' : '' }}">Make
                                                    User</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-sm btn-warning fw-bold text-uppercase" style="font-size: 0.7rem;">
                                    Edit
                                </a>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold text-uppercase"
                                        style="font-size: 0.7rem;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer bg-white border-top-0 p-4">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="small text-muted text-uppercase" style="font-size: 0.65rem;">Bookings</div>
                                <div class="fw-bold h5 mb-0">{{ $user->bookings_count }}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted text-uppercase" style="font-size: 0.65rem;">Spend</div>
                                <div class="fw-bold h5 mb-0">${{ number_format($totalSpend, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase mb-3 small">Account Details</h6>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Phone Number</label>
                            <span class="fw-normal">{{ $user->phone ?? 'Not Provided' }}</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Member Since</label>
                            <span class="fw-normal">{{ $user->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small d-block">Last Updated</label>
                            <span class="fw-normal">{{ $user->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content: Booking History -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-0">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="mb-0 fw-bold text-uppercase small" style="letter-spacing: 1px;">Booking History</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 small fw-bold">ID & DATE</th>
                                        <th class="py-3 small fw-bold">HOTEL</th>
                                        <th class="py-3 small fw-bold">AMOUNT</th>
                                        <th class="py-3 small fw-bold">STATUS</th>
                                        <th class="pe-4 py-3 text-end small fw-bold">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->bookings as $booking)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark">#{{ $booking->id }}</div>
                                                <div class="text-muted small">{{ $booking->created_at->format('d M, Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small fw-bold">{{ $booking->hotel->name ?? 'N/A' }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">
                                                    {{ $booking->hotel->city->name ?? '' }}</div>
                                            </td>
                                            <td>
                                                <div class="small fw-bold">{{ $booking->currency }}
                                                    {{ number_format($booking->total_amount, 2) }}</div>
                                            </td>
                                            <td>
                                                @if ($booking->status == 1)
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Confirmed</span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Pending</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                                    class="btn btn-sm btn-dark rounded-0 px-3">View</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No bookings found for
                                                this user.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
