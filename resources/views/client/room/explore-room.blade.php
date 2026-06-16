@extends('client.layouts.template')

@section('title', $room->title . ' | ' . $hotel->name)

@section('content')
    <div class="container py-5 mt-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb small text-uppercase" style="letter-spacing: 2px;">
                            <li class="breadcrumb-item"><a href="{{ route('client.home') }}"
                                    class="text-muted text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('client.rooms') }}"
                                    class="text-muted text-decoration-none">Collections</a></li>
                            <li class="breadcrumb-item active text-dark fw-bold">{{ $room->category }}</li>
                        </ol>
                    </nav>

                    @if ($hotel->reviews_avg_rating)
                        <div class="bg-primary text-white px-2 py-1 h-50 small fw-bold">
                            {{ number_format($hotel->reviews_avg_rating, 1) }} <i class="bi bi-star-fill"></i> Hotel Ratings
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <h1 class="headingfonts display-5 fw-bold mb-2 text-uppercase" style="letter-spacing: -1px;">
                        {{ $room->title }}
                        <a href="{{ route('client.room.explore', $hotel->id) }}" class="text-decoration-none text-muted">
                            <span class="fs-4 headingfonts2">At
                                {{ $hotel->name }}</span></a>
                    </h1>
                    <p class="text-muted mb-0"><i
                            class="bi bi-geo-alt me-2 text-primary"></i>{{ $hotel->address ?? $hotel->city->full_name }}
                    </p>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-12">
                        <div class="ratio ratio-21x9 overflow-hidden shadow-sm border border-white"
                            style="border-width: 10px !important;">
                            <img src="{{ asset('storage/' . ($room->images->first()->path ?? 'default.jpg')) }}"
                                class="object-fit-cover" alt="Main Image">
                        </div>
                    </div>
                    @foreach ($room->images->skip(1)->take(3) as $img)
                        <div class="col-4">
                            <div class="ratio ratio-4x3 overflow-hidden shadow-sm border border-white"
                                style="border-width: 5px !important;">
                                <img src="{{ asset('storage/' . $img->path) }}" class="object-fit-cover transition-zoom"
                                    alt="Room Image">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="amenities-section mb-5 p-4 border bg-light bg-opacity-50">
                    <h5 class="headingfonts fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">
                        <span class="border-bottom border-primary border-3 pb-2">Room Amenities</span>
                    </h5>
                    <div class="row g-4">
                        @forelse($hotel->amenities as $amenity)
                            <div class="col-md-4 col-6">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 text-primary bg-white shadow-sm d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="bi {{ $amenity->icon ?? 'bi-check2-circle' }} fs-5"></i>
                                    </div>
                                    <span class="small fw-bold text-uppercase text-secondary"
                                        style="letter-spacing: 1px;">{{ $amenity->title }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="small text-muted">Standard luxury amenities included.</p>
                        @endforelse
                    </div>
                </div>

                <div class="description-section mb-5">
                    <h5 class="headingfonts fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">
                        <span class="border-bottom border-primary border-3 pb-2">Experience</span>
                    </h5>
                    <p class="text-secondary lh-lg fs-5 fw-light" style="font-family: 'Inter', sans-serif;">
                        {{ $room->description }}</p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 120px; z-index: 10;">

                    @if ($room->offer_price)
                        <div class="bg-primary text-white p-3 text-center text-uppercase fw-bold small mb-0"
                            style="letter-spacing: 2px;">
                            Limited Time Offer - {{ $room->offer_message ?? 'Save Now' }}
                        </div>
                    @endif

                    <div class="card border-0 shadow-lg rounded-0">
                        <div class="card-body p-4 p-md-5">
                            <label class="small text-muted text-uppercase fw-bold mb-3 d-block"
                                style="letter-spacing: 2px;">Reservations</label>

                            <div class="price-box mb-4">
                                @if ($room->offer_price)
                                    <div class="d-flex flex-column">
                                        <span
                                            class="text-muted text-decoration-line-through small">{{ $room->user_currency_symbol }}
                                            {{ number_format($room->converted_price, 2) }}</span>
                                        <h2 class="fw-bold text-primary mb-0 display-6">{{ $room->user_currency_symbol }}
                                            {{ number_format($room->offer_price, 2) }}</h2>
                                    </div>
                                @else
                                    <h2 class="fw-bold mb-0 display-6">{{ $room->user_currency_symbol }}
                                        {{ number_format($room->converted_price, 2) }}</h2>
                                @endif
                                <small class="text-muted">Per Night (Excl. Taxes)</small>
                            </div>

                            <div class="mb-4">
                                <label class="small text-uppercase text-muted fw-bold mb-2 d-block"
                                    style="letter-spacing: 2px;">
                                    Select Dates
                                </label>
                                <div class="position-relative">
                                    <input type="text" id="availability-calendar"
                                        class="form-control rounded-0 py-3 bg-light border"
                                        placeholder="Check-in — Check-out" readonly>
                                    <i
                                        class="bi bi-calendar3 position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                                </div>
                                <div id="calendar-status-msg" class="small mt-2 d-none"></div>
                            </div>

                            @if (session('stay.hotel_id') !== null && session('stay.hotel_id') != $room->hotel_id)
                                <span class="badge text-danger px-2 py-2"
                                    title="You've already rooms from other Hotel, Clear your Stay list to Book this Room">
                                    Can't Select rooms From Multiple Hotel
                                </span>
                            @else
                                <button type="button"
                                    onclick="addToCartManual({{ $room->id }}, {{ $room->hotel_id }})"
                                    class="btn btn-dark w-100 py-3 rounded-0 fw-bold text-uppercase shadow-none border-0">
                                    Choose to Book
                                </button>
                            @endif

                            <div class="mt-4 pt-4 border-top">
                                <ul class="list-unstyled mb-0">
                                    <li class="small text-muted mb-2"><i class="bi bi-check-lg text-primary me-2"></i> Free
                                        Cancellation (24h before)</li>
                                    <li class="small text-muted mb-2"><i class="bi bi-check-lg text-primary me-2"></i>
                                        Instant Confirmation</li>
                                    <li class="small text-muted"><i class="bi bi-check-lg text-primary me-2"></i> Secure
                                        Payment via Stripe</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if (!$hotel->reviews->isEmpty())

                        <div class="card border-0 shadow-sm rounded-0 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="small text-muted text-uppercase fw-bold mb-0" style="letter-spacing: 2px;">
                                        Guest
                                        Ratings</h6>
                                    <div class="bg-primary text-white px-2 py-1 small fw-bold">
                                        {{ number_format($hotel->reviews_avg_rating, 1) }} <i class="bi bi-star-fill"></i>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    @php
                                        $metrics = [
                                            [
                                                'label' => 'Cleaning',
                                                'value' => $hotel->reviews_avg_cleaning ?? $hotel->reviews_avg_rating,
                                            ],
                                            [
                                                'label' => 'Services',
                                                'value' => $hotel->reviews_avg_services ?? $hotel->reviews_avg_rating,
                                            ],
                                            [
                                                'label' => 'Food',
                                                'value' => $hotel->reviews_avg_food ?? $hotel->reviews_avg_rating,
                                            ],
                                            [
                                                'label' => 'Hospitality',
                                                'value' =>
                                                    $hotel->reviews_avg_hospitality ?? $hotel->reviews_avg_rating,
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($metrics as $metric)
                                        <div class="col-6">
                                            <div class="d-flex flex-column">
                                                <span class="extra-small text-muted text-uppercase fw-bold mb-1"
                                                    style="font-size: 0.6rem;">{{ $metric['label'] }}</span>
                                                <div class="progress rounded-0" style="height: 4px;">
                                                    <div class="progress-bar bg-dark" role="progressbar"
                                                        style="width: {{ ($metric['value'] / 5) * 100 }}%"></div>
                                                </div>
                                                <span class="small fw-bold mt-1"
                                                    style="font-size: 0.7rem;">{{ number_format($metric['value'], 1) }} <i
                                                        class="bi bi-star-fill"></i></span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="my-3 pt-3 border-top text-center">
                                    <span class="text-muted small">Based on <strong>{{ $hotel->reviews_count }}</strong>
                                        verified guest reviews</span>
                                </div>

                                <div style="max-height: 300px; overflow-y: auto;" class="pe-2">
                                    @forelse($hotel->reviews as $review)
                                        <div class="mb-3 pb-3 border-bottom border-light">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span
                                                    class="small fw-bold text-capitalize">{{ $review->user->name }}</span>
                                                <span class="text-warning extra-small">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                                    @endfor
                                                </span>
                                            </div>
                                            <p class="text-muted extra-small mb-0 italic">"{{ $review->comment }}"</p>
                                        </div>
                                    @empty
                                        <p class="text-muted extra-small text-center py-3">No comments available yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    @endif

                    <div class="mt-4 p-4 border bg-white d-flex align-items-center justify-content-center">
                        <i class="bi bi-headset fs-2 me-3 text-muted"></i>
                        <div class="text-start">
                            <h6 class="mb-0 fw-bold small text-uppercase">Concierge Desk</h6>
                            <span class="text-muted small">+1 800-LUXURY</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="datemodal" style="background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-classic-dark text-dark">
                    <h5 class="modal-title">Select Booking Dates</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form onsubmit="addDates(event)">
                    @csrf
                    <div class="modal-body p-4">
                        <input type="hidden" name="hotel_id" id="hotel_id">
                        <input type="hidden" name="room_detail_id" id="room_id">
                        <div class="row">
                            <div class="col-6">
                                <label class="small fw-bold">Check-In</label>
                                <input type="date" name="check_in" class="form-control" required
                                    min="{{ date('Y-m-d') }}" value="{{ session('booking_check_in') }}">
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold">Check-Out</label>
                                <input type="date" name="check_out" class="form-control" required
                                    min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-classic btn-classic-dark w-100 py-2">Confirm & Add to
                            Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .text-primary {
            color: #bca47f !important;
        }

        .bg-primary {
            background-color: #bca47f !important;
        }

        .border-primary {
            border-color: #bca47f !important;
        }

        .transition-zoom {
            transition: transform 0.6s ease;
        }

        .transition-zoom:hover {
            transform: scale(1.05);
        }

        .headingfonts {
            font-family: 'Playfair Display', serif;
        }



        /* Custom Flatpickr Luxury Theme */
        .flatpickr-day.sold_out {
            background: #ffeded !important;
            color: #ff4d4d !important;
            text-decoration: line-through;
            border-color: transparent !important;
        }

        .flatpickr-day.blocked {
            background: #f8f9fa !important;
            color: #ccc !important;
            cursor: not-allowed !important;
            border-color: transparent !important;
        }

        .flatpickr-day.limited {
            border-bottom: 3px solid #ffc107 !important;
        }

        .flatpickr-day.is-user-booking {
            border: 2px solid #0d6efd !important;
            font-weight: bold;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let fp;

        document.addEventListener('DOMContentLoaded', function() {
            let dateStates = {};
            let userBookings = [];

            fp = flatpickr("#availability-calendar", {
                mode: "range",
                minDate: "today",
                dateFormat: "Y-m-d",
                showMonths: 1,

                onMonthChange: (selectedDates, dateStr, instance) => {
                    fetchAvailability(instance.currentMonth + 1, instance.currentYear);
                },

                onDayCreate: (dObj, dStr, instance, dayElem) => {
                    const dateStrFormatted = instance.formatDate(dayElem.dateObj, "Y-m-d");

                    const state = dateStates[dateStrFormatted];
                    if (state) {
                        dayElem.classList.add(state.status);

                        if (state.status === 'sold_out' || state.status === 'blocked') {
                            dayElem.classList.add('flatpickr-disabled');

                            dayElem.title = state.reason || (state.status === 'sold_out' ? 'Sold Out' :
                                'Unavailable');
                        } else if (state.status === 'limited') {
                            dayElem.title = `Only ${state.rooms_left} room(s) left`;
                        } else {
                            dayElem.title = 'Available';
                        }
                    }

                    const isUserBooking = userBookings.some(booking => {
                        return dateStrFormatted >= booking.start && dateStrFormatted <= booking
                            .end;
                    });

                    if (isUserBooking) {
                        dayElem.classList.add('is-user-booking');
                        dayElem.title = "Your Existing Booking";
                    }
                },
                onChange: (selectedDates) => {
                    if (selectedDates.length === 2) {
                        // console.log("Selected Range:", selectedDates);
                    }
                }
            });

            function fetchAvailability(month, year) {
                const url = `/room/{{ $room->id }}/availability?month=${month}&year=${year}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        dateStates = data.date_states;
                        userBookings = data.user_bookings;
                        fp.redraw();
                        // console.log(data);
                    })
                    .catch(error => console.error("Error fetching calendar data:", error));
            }

            fetchAvailability(new Date().getMonth() + 1, new Date().getFullYear());
        });







        function addToCartManual(roomId, hotelId, checkIn = null, checkOut = null) {

            let data;

            if (!checkIn && !checkOut && typeof fp !== 'undefined' && fp.selectedDates.length === 2) {
                checkIn = fp.formatDate(fp.selectedDates[0], "Y-m-d");
                checkOut = fp.formatDate(fp.selectedDates[1], "Y-m-d");
            }
            if (checkIn && checkOut) {
                data = {
                    _token: "{{ csrf_token() }}",
                    room_detail_id: roomId,
                    hotel_id: hotelId,
                    check_in: checkIn,
                    check_out: checkOut
                };
            } else {
                data = {
                    _token: "{{ csrf_token() }}",
                    room_detail_id: roomId,
                    hotel_id: hotelId
                };
            }

            $.ajax({
                url: "{{ route('booking.stay.add') }}",
                method: "POST",
                data: data,
                success: function(response) {

                    console.log(response);

                    if (response.show_modal) {
                        document.getElementById('datemodal').classList.add('show', 'd-block');
                        document.getElementById('room_id').value = response.room_id;
                        document.getElementById('hotel_id').value = response.hotel_id;

                    } else {
                        window.location.reload();
                    }
                },
                failed: function(response) {
                    console.log(response);
                },
                error: function(xhr) {
                    console.error("Status:", xhr.status);
                    console.error("Response:", xhr.responseText);
                }
            });
        }

        function addDates(e) {
            e.preventDefault();
            const roomId = e.target['room_detail_id'].value;
            const hotelId = e.target['hotel_id'].value;
            const checkIn = e.target['check_in'].value;
            const checkOut = e.target['check_out'].value;

            addToCartManual(roomId, hotelId, checkIn, checkOut);
        }
    </script>
@endpush
