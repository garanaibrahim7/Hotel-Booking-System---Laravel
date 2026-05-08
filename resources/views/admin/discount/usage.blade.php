@extends('layouts.adminlte')

@section('title', 'Discount Usage Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            
                            <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="bi bi-arrow-left"></i>
                            </a>

                            <div class="bg-soft-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px; background-color: #e7f1ff;">
                                <i class="bi bi-ticket-perforated text-primary fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-uppercase letter-spacing-1">{{ $discount->coupen_code }}</h4>
                                <span
                                    class="badge rounded-pill {{ $discount->active_status ? 'bg-success' : 'bg-secondary' }} px-3">
                                    {{ $discount->active_status ? 'Active Promotion' : 'Discontinued' }}
                                </span>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-1 text-uppercase fw-bold">Type</p>
                                <p class="fw-bold text-dark">{{ ucfirst($discount->type) }}</p>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-1 text-uppercase fw-bold">Value</p>
                                <p class="fw-bold text-dark">{{ $discount->formatted_value }}</p>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-1 text-uppercase fw-bold">Valid Until</p>
                                <p class="fw-bold text-dark">
                                    {{ $discount->ends_at ? $discount->ends_at->format('d M, Y') : 'Lifetime' }}</p>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-1 text-uppercase fw-bold">Min Stay</p>
                                <p class="fw-bold text-dark">{{ $discount->min_nights }} Nights</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 text-white"
                    style="border-radius: 15px; background: linear-gradient(45deg, #1a1a1a, #333);">
                    <div class="card-body p-4 d-flex flex-column justify-content-center text-center">
                        <p class="text-white-50 small mb-1 text-uppercase">Total Usage Count</p>
                        <h1 class="display-4 fw-bold mb-0">{{ $discount->used_count }}</h1>
                        <h4 class="mb-0">Total Availed Amount: {{ $discount->availedAmount }}</h4>
                        <div class="mt-3">
                            @if ($discount->usage_limit)
                                <div class="progress bg-dark" style="height: 6px;">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ ($discount->used_count / $discount->usage_limit) * 100 }}%"></div>
                                </div>
                                <p class="extra-small text-white-50 mt-2">Limit: {{ $discount->usage_limit }} Total Claims
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usage Table --}}
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark mb-0">Booking Redemptions</h5>
                <p class="text-muted small">List of bookings that applied this promotion</p>
            </div>
            <div class="card-body p-0">
                @if ($usageLogs->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small">
                                <tr>
                                    <th class="ps-4 border-0 text-muted py-3">Booking Info</th>
                                    <th class="border-0 text-muted">Guest</th>
                                    <th class="border-0 text-muted">Stay Dates</th>
                                    <th class="border-0 text-muted">Discount Applied</th>
                                    <th class="border-0 text-muted">Sub Total</th>
                                    <th class="border-0 text-muted">Final Total</th>
                                    <th class="border-0 text-muted text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usageLogs as $booking)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">#{{ $booking->reference_number }}</div>
                                            <div class="extra-small text-muted">
                                                {{ $booking->created_at->format('d M, Y h:i A') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $booking->user->name }}</div>
                                            <div class="extra-small text-muted">{{ $booking->user->phone }}</div>
                                        </td>

                                        <td>
                                            <span class="small fw-medium">{{ $booking->stay_dates['check_in'] }} <i
                                                    class="bi bi-arrow-right mx-1"></i>
                                                {{ $booking->stay_dates['check_out'] }}</span>
                                        </td>
                                        <td>

                                            <span class="text-success fw-bold">-
                                                {{ number_format($booking->discount_amount, 2) }}
                                                ({{ $booking->currency }})
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-dark">{{ number_format($booking->sub_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-bold text-dark">{{ number_format($booking->total_amount, 2) }}</span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                                class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clipboard-x display-1 text-light"></i>
                        <h5 class="text-muted mt-3">No redemptions yet for this promotion.</h5>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white border-0 py-3">
                {{ $usageLogs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <style>
        .letter-spacing-1 {
            letter-spacing: 1px;
        }

        .extra-small {
            font-size: 0.7rem;
        }

        .bg-soft-primary {
            background-color: #e7f1ff;
        }
    </style>
@endsection
