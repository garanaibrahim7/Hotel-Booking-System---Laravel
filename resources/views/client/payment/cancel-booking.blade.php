@extends('client.layouts.template')

@section('title', 'Cancel Reservation')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 small text-uppercase" style="letter-spacing: 1px;">
                        <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Bookings</a></li>
                        <li class="breadcrumb-item active primaryfont">Cancel Request</li>
                    </ol>
                </nav>

                <h2 class="headingfonts fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">Cancel Reservation</h2>

                <div class="card border-0 shadow-sm rounded-0 overflow-hidden">
                    <div class="card-body p-0">
                        {{-- Alert Header --}}
                        <div class="alert alert-warning rounded-0 border-0 m-0 d-flex align-items-center p-4">
                            <i class="bi bi-exclamation-octagon fs-3 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-uppercase">Cancellation Policy Notice</h6>
                                <p class="small mb-0 opacity-75">Please review the charges below. This action cannot be
                                    undone.</p>
                            </div>
                        </div>

                        <div class="p-4 p-md-5">
                            <div class="row g-4">
                                {{-- Booking Details --}}
                                <div class="col-md-6 border-end">
                                    <label class="small text-muted text-uppercase fw-bold mb-3 d-block"
                                        style="letter-spacing: 1px;">Stay Information</label>
                                    <h5 class="fw-bold mb-1 text-uppercase">{{ $booking->hotel->name }}</h5>
                                    <p class="text-muted small mb-3">{{ $booking->hotel->address }}</p>

                                    <div class="d-flex gap-4 mb-4">
                                        <div>
                                            <small class="d-block text-muted text-uppercase"
                                                style="font-size: 0.65rem;">Check In</small>
                                            <span
                                                class="fw-bold">{{ \Carbon\Carbon::parse($booking->items->first()->check_in)->format('d M, Y') }}</span>
                                        </div>
                                        <div class="border-start ps-4">
                                            <small class="d-block text-muted text-uppercase"
                                                style="font-size: 0.65rem;">Check Out</small>
                                            <span
                                                class="fw-bold">{{ \Carbon\Carbon::parse($booking->items->first()->check_out)->format('d M, Y') }}</span>
                                        </div>
                                    </div>

                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                        style="letter-spacing: 1px;">Payment Method</label>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card-2-back me-2 text-primary"></i>
                                        <span
                                            class="text-uppercase small fw-bold">{{ $booking->payment->gateway ?? 'Stripe / Online' }}</span>
                                    </div>
                                </div>

                                {{-- Refund Calculation --}}
                                <div class="col-md-6">
                                    <label class="small text-muted text-uppercase fw-bold mb-3 d-block"
                                        style="letter-spacing: 1px;">Refund Summary</label>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Amount Paid ({{ $booking->payment->paid_currency }})</span>
                                        <span class="fw-bold">
                                            {{ number_format($booking->payment->converted_amount, 2) }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2 text-danger">
                                        <span class="small">Cancellation Fee ({{ $booking->hotel->name }})</span>
                                        @php
                                            $fee = ($booking->hotel->cancellation_charge ?? 0) * $booking->payment->exchange_rate;
                                            // If charge is percentage or fixed, calculate here. Assuming fixed for this example.
                                            $refundAmount = $booking->payment->converted_amount - $fee;
                                        @endphp
                                        <span class="fw-bold">- {{ $booking->payment->paid_currency }}
                                            {{ number_format($fee, 2) }}</span>
                                    </div>

                                    <hr class="my-3 opacity-10">

                                    <div
                                        class="d-flex justify-content-between align-items-center bg-light p-3 border-start border-4 border-primary">
                                        <span class="fw-bold text-uppercase small">Total Refund</span>
                                        <h4 class="fw-bold mb-0 text-primary">
                                            {{ $booking->payment->paid_currency }} {{ number_format($refundAmount, 2) }}
                                        </h4>
                                    </div>
                                    <small class="text-muted mt-2 d-block" style="font-size: 0.7rem;">
                                        * Refund will be processed to your original payment method within 5-7 business days.
                                    </small>
                                </div>
                            </div>

                            {{-- Reason & Confirm --}}
                            <form action="{{ route('booking.cancel.confirm', $booking->id) }}" method="POST"
                                class="mt-5 pt-4 border-top">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Reason for
                                        Cancellation (Optional)</label>
                                    <textarea name="cancellation_reason" class="form-control rounded-0 bg-light border-0 p-3" rows="3"
                                        placeholder="Tell us why you're cancelling..."></textarea>
                                </div>

                                <div class="d-flex gap-3">
                                    <a href="{{ route('booking.view', $booking->reference_number) }}"
                                        class="btn btn-outline-dark rounded-0 px-4 py-3 fw-bold text-uppercase w-50"
                                        style="letter-spacing: 1px;">
                                        Keep Reservation
                                    </a>
                                    <button type="submit"
                                        class="btn btn-danger rounded-0 px-4 py-3 fw-bold text-uppercase w-50"
                                        style="letter-spacing: 1px;">
                                        Confirm Cancellation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .primaryfont {
            font-family: 'Inter', sans-serif;
        }

        .headingfonts {
            font-family: 'Playfair Display', serif;
        }

        .btn-danger {
            background-color: #a43535;
            border-color: #a43535;
        }

        .btn-danger:hover {
            background-color: #822a2a;
            border-color: #822a2a;
        }
    </style>
@endpush
