@foreach ($rooms as $room)
    <div class="card mb-3 border-0 shadow-sm overflow-hidden hotel-room-card"
        onclick="handleCardClick(event, '{{ route('client.room.details', $room->id) }}')" style="cursor: pointer;">
        <div class="row g-0 align-items-start">
            <div class="col-md-4">
                <img src="{{ $room->cover_image }}"
                    class="img-fluid object-fit-cover" style="height: 220px; width: 100%;">
            </div>

            <div class="col-md-8 p-4 d-flex flex-column" style="min-height: 220px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="headingfonts">
                        <h5 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 1px;">{{ $room->title }}</h5>
                        <div class="mb-2">
                            <span
                                class="badge bg-light text-dark border-0 shadow-sm small fw-normal">{{ $room->type }}</span>
                            <span
                                class="badge bg-light text-dark border-0 shadow-sm small fw-normal">{{ $room->category }}</span>
                        </div>

                        <ul class="list-inline small text-muted my-3">
                            <li class="list-inline-item me-3">
                                <i class="bi bi-people-fill me-1"></i>
                                {{ $room->max_adults }} Adults
                                @if (($room->max_children ?? 0) > 0)
                                    , {{ $room->max_children }} Child
                                @endif
                            </li>
                            <li class="list-inline-item">
                                @if ($hotel->cancellation_charge == 0)
                                    <i class="bi bi-check2-circle text-success me-1"></i>
                                    Free Cancellation
                                    {{-- @else
                                    {{ $hotel->cancellation_charge . $hotel->country->currency_symbol }} --}}
                                @endif
                            </li>
                        </ul>
                    </div>

                    <div class="text-end primaryfont">
                        @if ($room->offer ?? null)
                            <div class="mb-1">
                                <span class="badge bg-danger small text-uppercase" style="font-size: 0.7rem;">
                                    {{ $room?->offer_type }} Off
                                </span>
                            </div>
                            <h4 class="text-primary fw-bold mb-0">
                                {{ $room->user_currency_symbol }} {{ number_format($room->offer_price, 2) }}
                            </h4>
                            <div class="text-muted small">
                                <del>{{ $room->user_currency_symbol }}
                                    {{ number_format($room->converted_price, 2) }}</del>
                                <span class="ms-1">/ night</span>
                            </div>
                        @else
                            <h4 class="text-primary fw-bold mb-0">
                                {{ $room->user_currency_symbol }}{{ number_format($room->converted_price, 2) }}
                            </h4>
                            <small class="text-muted">/ per night</small>
                        @endif
                    </div>
                </div>
                {{-- {{ $room->coupon_code }} --}}

                <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                    <div class="pe-2">
                        @if ($room->offer ?? null)
                            <p class="text-danger mb-0 small fw-bold">
                                <i class="bi bi-ticket-perforated-fill me-1"></i> {{ $room->offer }}
                            </p>
                        @else
                            <p class="text-muted mb-0 small">Best Price Guaranteed</p>
                        @endif
                    </div>

                    @if (session('stay.hotel_id') !== null && session('stay.hotel_id') != $room->hotel_id)
                        {{-- <span class="badge bg-secondary text-warning px-2 py-2"
                            title="You've already rooms from other Hotel, Clear your Stay list to Book this Room">
                            Can't Select rooms From Multiple Hotel
                        </span> --}}
                        <a href="{{ route('client.room.details', $room->id) }}"
                            class="btn-classic btn-classic-dark px-2 py-2 text-uppercase text-center">
                            Explore Room
                        </a>
                    @else
                        <button type="button" onclick="addToCartManual({{ $room->id }}, {{ $room->hotel_id }})"
                            class="btn-classic btn-classic-dark px-2 py-2 text-uppercase">
                            Choose to Book
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach



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

@push('scripts')
    <script>
        function handleCardClick(event, url) {
            if (event.target.closest('button')) {
                return;
            }
            window.location.href = url;
        }


        function addToCartManual(roomId, hotelId, checkIn = null, checkOut = null) {

            // console.log(roomId + ' - - - ' + hotelId);
            // return;
            let data;
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

@push('styles')
    <style>
        .hotel-room-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hotel-room-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endpush
