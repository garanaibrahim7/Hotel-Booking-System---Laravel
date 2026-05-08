@extends('layouts.adminlte')

@section('title', 'Daily Operations | Front Desk')

@section('content')
    <div class="container py-5">

        <div class="mb-4">
            <h3 class="fw-bold mb-1 headingfonts text-uppercase" style="letter-spacing: 1px;">Live Bookings</h3>
            <p class="text-muted small mb-0"><i class="bi bi-calendar-event me-2"></i>{{ $today->format('l, F j, Y') }}</p>
        </div>

        <div class="card border border-light shadow-sm rounded-3">
            <div class="card-body p-4">

                <ul class="nav nav-pills custom-nav-pills nav-fill mb-4" id="operationsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active w-100" id="arrivals-tab" data-bs-toggle="pill"
                            data-bs-target="#arrivals" type="button" role="tab">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Arrivals
                            <span class="badge rounded-pill badge-soft ms-2">{{ $arrivals->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100" id="ongoing-tab" data-bs-toggle="pill" data-bs-target="#ongoing"
                            type="button" role="tab">
                            <i class="bi bi-house-door me-2"></i>In-House
                            <span class="badge rounded-pill badge-soft ms-2">{{ $ongoingStays->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link w-100" id="checkouts-tab" data-bs-toggle="pill" data-bs-target="#checkouts"
                            type="button" role="tab">
                            <i class="bi bi-box-arrow-right me-2"></i>Checkouts
                            <span class="badge rounded-pill badge-soft ms-2">{{ $checkouts->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="operationsTabContent">

                    <!-- ARRIVALS TAB -->
                    <div class="tab-pane fade show active" id="arrivals" role="tabpanel">
                        @if ($arrivals->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x empty-state-icon"></i>
                                <p class="text-muted small fw-medium mt-2">No arrivals found for Today.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-muted py-3 px-3">Guest</th>
                                            <th class="small text-muted py-3">Room</th>
                                            <th class="small text-muted py-3">Check-Out</th>
                                            <th class="small text-muted py-3 text-end px-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($arrivals as $item)
                                            <tr>
                                                <td class="px-3 py-3">
                                                    <div class="fw-bold">{{ $item->booking->user->name ?? 'Guest' }}</div>
                                                    <div class="extra-small text-muted">ID: #BKG-{{ $item->booking_id }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-bold small">Room:
                                                        {{ $item->room->room_number ?? 'Pending' }}</div>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $item->room->details->title ?? 'N/A' }}</span>
                                                </td>
                                                <td class="py-3 small">
                                                    {{ \Carbon\Carbon::parse($item->check_out)->format('M d, Y') }}
                                                </td>
                                                <td class="py-3 px-3 text-end">
                                                    <a href="{{ route(auth()->user()->role.'.bookings.show', $item->booking_id) }}"
                                                        class="btn btn-sm btn-outline-dark px-3">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- ONGOING STAYS TAB -->
                    <div class="tab-pane fade" id="ongoing" role="tabpanel">
                        @if ($ongoingStays->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x empty-state-icon"></i>
                                <p class="text-muted small fw-medium mt-2">No ongoing stays found for Today.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-muted py-3 px-3">Guest</th>
                                            <th class="small text-muted py-3">Room</th>
                                            <th class="small text-muted py-3">Dates</th>
                                            <th class="small text-muted py-3 text-end px-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ongoingStays as $item)
                                            <tr>
                                                <td class="px-3 py-3">
                                                    <div class="fw-bold">{{ $item->booking->user->name ?? 'Guest' }}</div>
                                                    <div class="extra-small text-muted">ID: #BKG-{{ $item->booking_id }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-bold small">Room:
                                                        {{ $item->room->room_number ?? 'Pending' }}</div>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $item->room->details->title ?? 'N/A' }}</span>
                                                </td>
                                                <td class="py-3 small">
                                                    <div><span class="text-muted">In:</span>
                                                        {{ \Carbon\Carbon::parse($item->check_in)->format('M d') }}</div>
                                                    <div><span class="text-muted">Out:</span>
                                                        {{ \Carbon\Carbon::parse($item->check_out)->format('M d') }}</div>
                                                </td>
                                                <td class="py-3 px-3 text-end">
                                                    <a href="{{ route(auth()->user()->role.'.bookings.show', $item->booking_id) }}"
                                                        class="btn btn-sm btn-outline-dark px-3">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- CHECKOUTS TAB -->
                    <div class="tab-pane fade" id="checkouts" role="tabpanel">
                        @if ($checkouts->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x empty-state-icon"></i>
                                <p class="text-muted small fw-medium mt-2">No checkouts found for Today.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-muted py-3 px-3">Guest</th>
                                            <th class="small text-muted py-3">Room</th>
                                            <th class="small text-muted py-3">Status</th>
                                            <th class="small text-muted py-3 text-end px-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($checkouts as $item)
                                            <tr>
                                                <td class="px-3 py-3">
                                                    <div class="fw-bold">{{ $item->booking->user->name ?? 'Guest' }}</div>
                                                    <div class="extra-small text-muted">ID: #BKG-{{ $item->booking_id }}
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="fw-bold small">Room:
                                                        {{ $item->room->room_number ?? 'Pending' }}</div>
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $item->room->details->title ?? 'N/A' }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-warning text-dark px-2 py-1">Leaving Today</span>
                                                </td>
                                                <td class="py-3 px-3 text-end">
                                                    <a href="{{ route(auth()->user()->role.'.bookings.show', $item->booking_id) }}"
                                                        class="btn btn-sm btn-outline-dark px-3">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom Segmented Control UI matching the image */
        .custom-nav-pills {
            background-color: #f4f6f8;
            border-radius: 0.5rem;
            padding: 0.35rem;
        }

        .custom-nav-pills .nav-link {
            color: #6c757d;
            border-radius: 0.375rem;
            font-weight: 600;
            padding: 0.85rem 1rem;
            border: border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
        }

        .custom-nav-pills .nav-link.active {
            background-color: #ffffff;
            color: #212529;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .badge-soft {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            font-weight: 600;
        }

        .nav-link.active .badge-soft {
            background-color: #f1f3f5;
            border-color: #ced4da;
        }

        /* Faded Empty State Icon */
        .empty-state-icon {
            font-size: 5rem;
            color: #f1f3f5;
            opacity: 0.8;
            margin-bottom: 0.5rem;
        }
    </style>
@endpush
