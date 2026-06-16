@extends('layouts.adminlte')

@section('title', 'All Reservations')

@section('content')

    @if (!request('hotel'))
        @include('admin.partials.booking-stats')
    @endif

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill h-100 me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold text-dark mb-0">Bookings of
                        {{ request('hotel') ? $bookings->first()?->booking->hotel?->name . ' on ' : '' }}
                        {{ request('date') ? (request('date') == today()->toDateString() ? 'Today' : \Carbon\Carbon::parse(request('date'))->format('d-m-Y l')) : \Str::title(request('bookingsOf') ?? 'Today') }}
                    </h3>
                    <p class="text-muted small mb-0">Overview of all confirmed and pending guest stays</p>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <div class="d-flex gap-2 justify-content-end">

                    <form method="get" class="w-25">
                        @foreach (request()->all() as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <input type="date" name="date" class="form-control"
                            value="{{ request('date') ?? today()->toDateString() }}" onchange="this.form.submit()">
                    </form>
                    <form method="get" class="w-50">
                        @foreach (request()->all() as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <input type="text" name="search" class="form-control" placeholder="Search Bookings"
                            value="{{ request('search') }}" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="d-flex gap-2">
                    @foreach (['today', 'week', 'month', 'year', 'all'] as $item)
                        @can('manager-access')
                            <a href="{{ route('manager.bookings.index', request('bookingsOf') == $item ? [...request()->query()] : [...request()->query(), 'bookingsOf' => $item]) }}"
                                class="btn btn-{{ request('bookingsOf') == $item ? 'dark' : 'secondary' }}">{{ \Str::title($item) }}</a>
                        @endcan
                        @can('admin-access')
                            <a href="{{ route('admin.bookings.index', request('bookingsOf') == $item ? [...request()->query()] : [...request()->query(), 'bookingsOf' => $item]) }}"
                                class="btn btn-{{ request('bookingsOf') == $item ? 'dark' : 'secondary' }}">{{ \Str::title($item) }}</a>
                        @endcan
                    @endforeach
                    {{-- <button class="btn btn-outline-dark shadow-sm border px-3"
                        onclick="alert('This Button will Print Report of Bookings')">
                        <i class="bi bi-printer me-1"></i> Print
                    </button> --}}
                    @can('manager-access')
                        <a href="{{ route('manager.bookings.print', request()->query()) }}" target="_blank"
                            class="btn btn-dark rounded-pill px-4">
                            <i class="bi bi-printer me-2"></i> Print Report
                        </a>
                    @endcan
                    @can('admin-access')
                        <a href="{{ route('admin.bookings.print', request()->query()) }}" target="_blank"
                            class="btn btn-dark rounded-pill px-4">
                            <i class="bi bi-printer me-2"></i> Print Report
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-0">

                @include('admin.booking.booking-table', ['bookings' => $bookings])

            </div>

            <div class="card-footer bg-white border-0 py-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>

    {{-- <x-alert-model id="staticBackdrop" title="Cancel Reservation">
        <div class="text-center p-3">
            <i class="bi bi-exclamation-triangle text-danger display-4 mb-3"></i>
            <p class="mb-0">Are you sure you want to delete this booking?</p>
            <p class="small text-muted">This action will release the room back into inventory.</p>
        </div>
        <x-slot:action>
            <form id="deleteRoomForm" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger px-4" style="border-radius: 8px;">Confirm Delete</button>
            </form>
        </x-slot>
    </x-alert-model> --}}

    {{-- @if (session('message'))
        <div id="flashAlert"
            class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show position-fixed bottom-0 end-0 m-4 shadow-lg"
            style="z-index: 1060; border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                <div>{{ session('message') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif --}}

    <style>
        .btn-white {
            background: #fff;
            color: #333;
            transition: 0.2s;
        }

        .btn-white:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
        }
    </style>
@endsection

@push('scripts')
    <script>
        function deleteAlert(bookingId) {
            document.getElementById('deleteRoomForm').action = `/admin/bookings/${bookingId}`;
            let modal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
            modal.show();
        }

        setTimeout(() => {
            const alert = document.getElementById('flashAlert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 4000);
    </script>
@endpush
