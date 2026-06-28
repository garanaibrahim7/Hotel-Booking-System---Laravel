@extends('client.layouts.template')

@section('title', 'Review Your Stay')

@section('content')
    <div class="container py-5 mt-5">
        @if (empty($stay) || empty($stay['items']))
            <div class="row justify-content-center py-5">
                <div class="col-md-6 text-center">
                    <div class="mb-4">
                        <i class="bi bi-calendar-x text-muted opacity-25" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="headingfonts fw-bold text-uppercase mb-3" style="letter-spacing: 2px;">Your Stay is Empty</h2>
                    <p class="text-muted mb-5">Select a room to begin your luxury experience.</p>
                    <a href="{{ route('client.hotels.explore') }}" class="btn-classic btn-classic-dark px-5 py-3">Explore
                        Hotels</a>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="d-flex justify-content-between align-items-end border-bottom pb-4">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2 small text-uppercase" style="letter-spacing: 1px;">
                                    <li class="breadcrumb-item text-muted">Reservation</li>
                                    <li class="breadcrumb-item active primaryfont">Review Stay</li>
                                </ol>
                            </nav>
                            <h2 class="headingfonts fw-bold text-uppercase mb-0" style="letter-spacing: 2px;">Review Your
                                Stay</h2>
                            at {{ session('stay.hotel_name', 'Hotel') }}
                        </div>
                        <div class="text-end d-none d-md-block">
                            <span class="badge bg-dark px-3 py-2 text-uppercase fw-normal"
                                style="border-radius: 0;">Verified Selection</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    @foreach ($stay['items'] as $id => $details)
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 0; height: 200px">
                            <div class="row g-0 h-100">
                                <div class="col-md-3">
                                    <img src="{{ $details['image'] ?? asset('storage/room_placeholder.jpeg') }}"
                                        class="img-fluid object-fit-cover w-100 h-100">
                                </div>
                                <div class="col-md-9 p-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="fw-bold mb-1 text-uppercase">{{ $details['title'] }}</h5>
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-door-closed me-1"></i> {{ $details['quantity'] }}
                                                {{ Str::plural('Room', $details['quantity']) }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            @if (isset($stay['discount_id']) && $details['price'] < $details['base_price'])
                                                <del class="text-muted small d-block">
                                                    {{-- {{ $summary->currency }} {{ number_format(($details['base_price'] / $details['price']) * $details['converted_price'], 2) }} --}}
                                                    {{ $summary->currency }}
                                                    {{ number_format($details['converted_base_price'], 2) }}
                                                </del>
                                                <span class="fw-bold text-primary fs-4">
                                                    {{ $summary->currency }}
                                                    {{ number_format($details['converted_price'], 2) }}
                                                </span>
                                            @else
                                                <span class="fw-bold text-dark fs-4">
                                                    {{ $summary->currency }}
                                                    {{ number_format(($details['base_price'] / $details['price']) * $details['converted_price'], 2) }}
                                                </span>
                                            @endif
                                            <small class="text-muted d-block small">per night</small>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                        <a href="{{ route('booking.stay.remove', $id) }}"
                                            class="remove-from-stay text-danger small text-decoration-none fw-bold">
                                            <i class="bi bi-trash3 me-1"></i> REMOVE
                                        </a>

                                        @if (isset($stay['discount_id']) && $stay['offer_message'])
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                                <i class="bi bi-patch-check-fill me-1"></i> {{ $stay['offer_message'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        @if ($summary->hotel_id)
                            <a href="{{ route('client.room.explore', $summary->hotel_id) }}"
                                class="btn btn-outline-dark px-4 py-2" style="border-radius: 0;">
                                <i class="bi bi-plus-lg me-1"></i> Add More Rooms
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px; border-radius: 0;">
                        <h5 class="fw-bold mb-4 headingfonts text-uppercase" style="letter-spacing: 1px;">Stay Summary</h5>

                        <div class="bg-light p-3 mb-4 border-start border-4 border-primary" id="dateEditTrigger"
                            style="cursor: pointer; position: relative;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Your
                                        Dates</small>
                                    <p class="mb-0 small fw-bold" id="display_stay_dates">
                                        {{ \Carbon\Carbon::parse(session('booking_check_in'))->format('d M') }} —
                                        {{ \Carbon\Carbon::parse(session('booking_check_out'))->format('d M, Y') }}
                                    </p>
                                </div>
                                <i class="bi bi-pencil-square text-primary"></i>
                            </div>
                            <input type="text" id="summary_date_picker"
                                style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                        </div>
                        @if ($summary->error_message)
                            <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>
                                    <strong>Wait!</strong> {{ $summary->error_message }}
                                </div>
                            </div>
                        @endif
                        <div id="customAlert"
                            class="alert alert-danger d-none align-items-center rounded-0 border-0 shadow-sm mb-3"
                            role="alert">
                            <div class="d-flex align-items-center w-100">
                                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                                <span id="customAlertMessage" class="small text-uppercase"
                                    style="font-size: 0.75rem;"></span>
                                <button type="button" id="closeAlertBtn" class="btn-close ms-auto" aria-label="Close"
                                    style="font-size: 0.8rem;"></button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small text-uppercase">Duration</span>
                            <span class="fw-bold small" id="summary_duration">{{ $summary->stayNights }}
                                {{ Str::plural('Night', $summary->stayNights) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small text-uppercase">Subtotal</span>
                            <span class="text-dark small" id="summary_subtotal">
                                {{ $summary->currency }} {{ number_format($summary->subtotal, 2) }}
                            </span>
                        </div>

                        {{-- Discount row toggled by discount_id activity --}}
                        <div id="summary_discount_row"
                            class="justify-content-between mb-2 text-success {{ isset($stay['discount_id']) ? 'd-flex' : 'd-none' }}">
                            <span class="small text-uppercase">Discount Applied</span>
                            <span class="small fw-bold" id="summary_discount">
                                - {{ $summary->currency }} {{ number_format($summary->totalSavings, 2) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                            <h5 class="fw-bold mb-0 text-uppercase small">Total Amount</h5>
                            <h4 class="fw-bold mb-0 text-primary" id="summary_grand_total">
                                {{ $summary->currency }} {{ number_format($summary->grandTotal, 2) }}
                            </h4>
                        </div>

                        <a href="{{ route('booking.checkout') }}"
                            class="btn-classic btn-classic-dark btn-checkout w-100 py-3 mt-4 fw-bold text-uppercase"
                            style="letter-spacing: 2px;">
                            <span>Checkout <i class="bi bi-chevron-right ms-2"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#summary_date_picker", {
                mode: "range",
                minDate: "today",
                dateFormat: "Y-m-d",
                defaultDate: ["{{ session('booking_check_in') }}", "{{ session('booking_check_out') }}"],
                onClose: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        const checkIn = flatpickr.formatDate(selectedDates[0], "Y-m-d");
                        const checkOut = flatpickr.formatDate(selectedDates[1], "Y-m-d");

                        $.ajax({
                            url: "{{ route('booking.stay.update_dates') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                check_in: checkIn,
                                check_out: checkOut
                            },
                            success: function(response) {
                                console.log(response);
                                // return;
                                const alertElement = document.getElementById(
                                    'customAlert');
                                const alertMessage = document.getElementById(
                                    'customAlertMessage');
                                const closeBtn = document.getElementById(
                                    'closeAlertBtn');
                                if (response.status) {
                                    document.getElementById('display_stay_dates')
                                        .innerText = response.date_display;
                                    document.getElementById('summary_duration').innerText =
                                        response.nights;
                                    document.getElementById('summary_subtotal').innerText =
                                        response.subtotal;
                                    document.getElementById('summary_grand_total')
                                        .innerText = response.grandTotal;
                                    document.getElementById('summary_discount').innerText =
                                        '- ' + response.discount;

                                    const discountRow = document.getElementById(
                                        'summary_discount_row');
                                    if (response.message || response.discount == 0) {
                                        discountRow.classList.add('d-none');


                                        alertMessage.innerText = response.message;
                                        alertElement.classList.remove('d-none');
                                        alertElement.style.setProperty('display', 'flex',
                                            'important');

                                        closeBtn.onclick = function() {
                                            alertElement.style.setProperty('display',
                                                'none', 'important');
                                        };


                                    } else {
                                        discountRow.style.setProperty('display', 'flex',
                                            'important');
                                    }
                                } else {


                                    if (alertElement && alertMessage) {
                                        alertMessage.innerText = response.message;

                                        alertElement.classList.remove('d-none');
                                        alertElement.style.setProperty('display', 'flex',
                                            'important');

                                        closeBtn.onclick = function() {
                                            alertElement.style.setProperty('display',
                                                'none', 'important');
                                        };
                                    }
                                }
                            }
                        });
                    }
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .bg-success-subtle {
            background-color: #e8f5e9 !important;
        }

        .text-success {
            color: #2e7d32 !important;
        }

        .remove-from-stay:hover {
            color: #dc3545 !important;
        }

        .btn-checkout span {
            display: inline-block;
            transition: transform 0.5s ease;
        }

        .btn-checkout:hover span {
            transform: translateX(180%)
        }
    </style>
@endpush
