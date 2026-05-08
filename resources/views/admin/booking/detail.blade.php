@extends('layouts.adminlte')

@section('title', 'Booking Details #' . $booking->reference_number)

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="btn btn-white shadow-sm rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold text-dark mb-0">Booking #{{ $booking->reference_number }}</h3>
                    @php
                        switch ($booking->status) {
                            case 1:
                                $color = '#e8fadf';
                                $textColor = '#28a745';
                                $label = 'Confirmed';
                                break;

                            case 0:
                                $color = '#fff3cd';
                                $textColor = '#856404';
                                $label = 'Pending';
                                break;

                            case 2:
                                $color = '#ffe5e5';
                                $textColor = '#d9534f';
                                $label = 'Failed';
                                break;

                            case 3:
                                $color = '#e2f0ff';
                                $textColor = '#0d6efd';
                                $label = 'Processing';
                                break;

                            case 4:
                                $color = '#f8d7da';
                                $textColor = '#842029';
                                $label = 'Cancelled';
                                break;

                            case 5:
                                $color = '#f8d7da';
                                $textColor = '#842029';
                                $label = 'Rejected';
                                break;

                            default:
                                $color = '#f0f0f0';
                                $textColor = '#6c757d';
                                $label = 'Unknown';
                        }
                    @endphp

                    <span class="badge rounded-pill px-3 py-1 mt-1"
                        style="background-color: {{ $color }}; color: {{ $textColor }};">
                        {{ $label }}
                    </span>
                </div>
            </div>

            <div class="d-flex gap-2">
                @php
                    $today = \Carbon\Carbon::today();
                    $checkIn = $booking->items?->first()
                        ? \Carbon\Carbon::parse($booking->items->first()->check_in)
                        : null;
                    $checkOut = $booking->items?->first()
                        ? \Carbon\Carbon::parse($booking->items->first()->check_out)
                        : null;
                @endphp

                @if ($booking->status == 1)

                    @if ($checkIn && is_null($booking->arrival) && $checkIn->isPast())

                        @if ($checkOut->isPast())
                            <button class="btn btn-warning px-4 rounded-pill shadow-sm">
                                <i class="bi bi-exclamation-circle me-2"></i>Customer didn't Came
                            </button>
                        @else
                            <button class="btn btn-warning px-4 rounded-pill shadow-sm"
                            onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.mark-arrival', $booking->id) }}', 'Mark Late Checkin', 'Checkin Missed, want to mark Late Checkin?')">
                                <i class="bi bi-exclamation-circle me-2"></i>CheckIn Missed - Mark Late Checkin
                            </button>
                        @endif
                    @elseif ($checkIn->isToday() && is_null($booking->arrival))
                        <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm"
                            onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.mark-arrival', $booking->id) }}', 'Mark Arrived', 'Are you sure the guest has arrived at the property?')">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Mark Arrived
                        </button>
                    @elseif (($checkIn->isToday() || $checkIn->isPast()) && $booking->arrival && is_null($booking->leaved))
                        @if ($checkOut->isToday())
                            <button type="button" class="btn btn-warning px-4 rounded-pill shadow-sm"
                                onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.mark-leaved', $booking->id) }}', 'Mark Leaved', 'Is the guest ready to check out and leave?')">
                                <i class="bi bi-box-arrow-left me-1"></i> Mark Leaved
                            </button>
                        @elseif($checkOut->isFuture())
                            <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                <i class="bi bi-person-check me-1"></i> Ongoing Stay
                            </button>
                            <button type="button" class="btn btn-warning px-4 rounded-pill shadow-sm"
                                onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.mark-leaved', $booking->id) }}', 'Mark Leaved', 'Is the guest ready to check out and leave?')">
                                <i class="bi bi-box-arrow-left me-1"></i> Early Checkout
                            </button>
                        @elseif (is_null($booking->leaved) && $checkOut->isPast())
                            <button type="button" class="btn btn-danger px-4 rounded-pill shadow-sm"
                                onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.mark-leaved', $booking->id) }}', 'Mark Missed Checkout', 'Checkout Missed, want to mark Late Checkout?')">
                                <i class="bi bi-box-arrow-right me-1"></i> Checkout missed or Not Leaved
                            </button>
                        @endif
                    @elseif ($booking->leaved)
                        @if ($checkOut->isBefore($booking->leaved))
                            <span class="badge bg-light text-dark border border-warning px-3 py-2 rounded-pill">
                                <i class="bi bi-check-all me-1"></i> Stay Completed (Late Checkout)
                            </span>
                        @else
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                <i class="bi bi-check-all me-1"></i> Stay Completed
                            </span>
                        @endif
                    @elseif($checkIn->isFuture())
                        <span class="btn btn-primary px-3 py-2 rounded-pill">
                            <i class="bi bi-check me-1"></i>Confirmed Booking
                        </span>
                        <button class="btn btn-outline-danger px-4 rounded-pill"
                            onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.cancel-booking', 1) }}', 'Reject Booking', 'Are you sure you want to Reject this Upcoming Booking?')">
                            <i class="bi bi-x-circle me-1"></i> Reject Booking
                        </button>
                    @endif
                @elseif($booking->status == 0)
                    <span class="btn btn-outline-secondary text-muted px-3 py-2 rounded-pill">
                        <i class="bi bi-clock me-1"></i>Pending
                    </span>
                @elseif($booking->status == 3)
                    <button class="btn btn-outline-secondary text-muted px-3 py-2 rounded-pill"
                        onclick="triggerBookingModal('{{ route(auth()->user()->role . '.booking.mark-success', $booking->id) }}', 'Accept Booking', 'Are you sure you want to Accept and Confirm this Booking?')">
                        <i class="bi bi-clock me-1"></i>Processing
                    </button>
                @else
                    <span class="badge bg-soft-danger text-danger px-3 py-2 rounded-pill"
                        style="background-color: #ffe5e5;">
                        <i class="bi bi-x-octagon me-1"></i>
                        {{ $booking->status == 2 ? 'Failed' : ($booking->status == 4 ? 'Cancelled' : 'Rejected') }}
                    </span>
                @endif
            </div>


        </div>

        <div class="row g-4">
            {{-- Left Column: Guest & Payment --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-4">Guest Information</h6>
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-soft-dark rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px; background: #f0f2f5;">
                                <i class="bi bi-person fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-5 text-dark">{{ $booking->user->name }}</div>
                                <div class="text-muted small">{{ $booking->user->email }}</div>
                            </div>
                        </div>
                        <div class="mb-2 small">
                            <i class="bi bi-telephone me-2 text-muted"></i> {{ $booking->user->phone ?? 'Not provided' }}
                        </div>
                        @if ($booking->arrival && $checkIn->isPast())
                            <div class="mb-2 small">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                {{ $booking->arrival?->format('d-M y \a\t h:m A') ?? 'Not Arrived' }}
                            </div>
                        @endif
                        @if ($booking->arrival)
                            <div class="mb-2 small">
                                <i class="bi bi-box-arrow-in-left me-1"></i>
                                {{ $booking->leaved?->format('d-M y \a\t h:m A') ?? 'Ongoing' }}
                            </div>
                        @endif

                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-4">Payment Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Sub Amount</span>
                            <span class="fw-semibold text-dark">{{ number_format($booking->sub_amount, 2) }}
                                {{ $booking->currency }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Discount Applied</span>
                            <span class="fw-semibold text-dark">{{ number_format($booking->discount_amount, 2) }}
                                {{ $booking->currency }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Taxes & Fees</span>
                            <span class="fw-semibold text-dark">{{ number_format($booking?->tax, 2) }}
                                {{ $booking->currency }}</span>
                        </div>
                        <hr class="my-3 opacity-50">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold text-dark fs-5">Total Amount</span>
                            <span class="fw-semibold text-dark">{{ number_format($booking->total_amount, 2) }}
                                {{ $booking->currency }}</span>
                        </div>

                        <div class="bg-light p-3 rounded-3">
                            <div class="small text-muted mb-1">Payment Method</div>
                            <div class="fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-{{ strtolower($booking->payment->gateway ?? 'cash') }} me-2"></i>
                                {{ $booking->payment->gateway ?? 'Cash' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-4">Reserved Rooms</h6>

                        @foreach ($booking->items as $item)
                            <div class="p-3 border rounded-3 mb-3 bg-light-hover transition-all">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="/storage/{{ $item->room->details->images->first()->path ?? '' }}"
                                            class="img-fluid rounded-3 shadow-sm"
                                            style="height: 60px; width: 100%; object-fit: cover;">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fw-bold text-dark">{{ $item->room->details->title }}</div>
                                        <div class="small text-muted">Room #{{ $item->room->room_number }} —
                                            {{ $item->room->hotel->name }}</div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="small fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($item->check_in)->format('d M') }} -
                                            {{ \Carbon\Carbon::parse($item->check_out)->format('d M, Y') }}
                                        </div>
                                        <div class="extra-small text-muted">
                                            {{ \Carbon\Carbon::parse($item->check_in)->diffInDays($item->check_out) }}
                                            Nights stay</div>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="fw-bold text-dark">{{ number_format($item->room->details->price, 2) }}
                                            {{ $booking->currency }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($booking->special_requests)
                            <div class="mt-4">
                                <h6 class="fw-bold text-uppercase small text-muted mb-2">Special Requests</h6>
                                <div class="p-3 bg-light border-start border-warning border-4 rounded-end">
                                    {{ $booking->special_requests }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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
            <a id="actionConfirmBtn" href="#" class="btn btn-primary px-4 rounded-pill">Confirm</a>
        </x-slot>
    </x-alert-model>


@endsection

@push('scripts')
    <script>
        function confirmReject() {
            let modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        function triggerBookingModal(url, title, message) {
            const modalEl = document.getElementById('staticBackdrop');

            const titleEl = modalEl.querySelector('.modal-title') || document.getElementById('bookingActionModalLabel');
            if (titleEl) titleEl.innerText = title;

            const msgEl = document.getElementById('actionMessage');
            const btnEl = document.getElementById('actionConfirmBtn');

            if (msgEl) msgEl.innerText = message;
            if (btnEl) btnEl.href = url;

            const icon = document.getElementById('actionIcon');
            if (icon && btnEl) {
                if (title.includes('Reject') || title.includes('Missed')) {
                    btnEl.className = 'btn btn-danger px-4 rounded-pill';
                    icon.className = 'bi bi-exclamation-triangle text-danger display-4 mb-3';
                } else if (title.includes('Leaved') || title.includes('Late')) {
                    btnEl.className = 'btn btn-warning px-4 rounded-pill text-white';
                    icon.className = 'bi bi-box-arrow-right text-warning display-4 mb-3';
                } else {
                    btnEl.className = 'btn btn-primary px-4 rounded-pill';
                    icon.className = 'bi bi-check-circle text-primary display-4 mb-3';
                }
            }

            let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }
    </script>
@endpush

@push('styles')
    <style>
        .btn-white {
            background: #fff;
            border: 1px solid #f0f0f0;
        }

        .bg-light-hover:hover {
            background-color: #f8f9fa;
        }

        .transition-all {
            transition: all 0.2s ease;
        }

        .extra-small {
            font-size: 0.7rem;
        }
    </style>
@endpush
