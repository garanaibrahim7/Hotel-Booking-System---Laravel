@extends('client.layouts.template')

@section('title', 'Check Out')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row g-0 shadow-lg">
            {{-- Left Side: Guest Info --}}
            <div class="col-lg-8 bg-white p-5">
                <h3 class="fw-bold mb-4" style="color: var(--dark-bg); text-transform: uppercase; letter-spacing: 1px;">
                    Guest Information
                </h3>

                <form action="{{ route('booking.checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="small fw-bold text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', auth()->user()->name) }}">
                            @error('name')
                                <label for="name" class="small text-danger">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold text-uppercase">Email Address</label>
                            <input type="text" name="email" class="form-control"
                                value="{{ old('email', auth()->user()->email) }}">
                            @error('email')
                                <label for="name" class="small text-danger">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold text-uppercase">Phone Number</label>
                            <input type="tel" name="phone" class="form-control"
                                value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="+91 . . .">
                            @error('phone')
                                <label for="name" class="small text-danger">{{ $message }}</label>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold text-uppercase">Any Special Instructions</label>
                            <textarea name="instruction" class="form-control" rows="3"
                                placeholder="Any Special Instruction for Smooth Check IN">{{ old('instruction') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h5 class="fw-bold text-uppercase mb-3">Payment Method</h5>
                        <div class="border p-3 d-flex align-items-center mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="stripe" checked>
                            <label class="ms-3 fw-bold text-uppercase small">Credit / Debit Card (Stripe)</label>
                            <i class="bi bi-stripe ms-auto fs-4 text-muted"></i>
                        </div>
                        <div class="border p-3 d-flex align-items-center mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" value="at_hotel" disabled>
                            <label class="ms-3 fw-bold text-uppercase small text-muted">Pay at Hotel (Currently this Feature
                                is Not Available)</label>
                            <i class="bi bi-cash ms-auto fs-4 text-muted"></i>
                        </div>
                    </div>

                    {{-- Hidden Inputs for Form Submission --}}
                    <input type="hidden" name="check_in" value="{{ $checkoutPayload['checkIn'] }}">
                    <input type="hidden" name="check_out" value="{{ $checkoutPayload['checkOut'] }}">
                    <input type="hidden" name="coupon_code" id="hidden_coupon_code" value="{{ $coupon_code ?? '' }}">

                    @foreach ($checkoutPayload['rooms'] as $room)
                        <input type="hidden" name="room_requirements[]" value="{{ $room['id'] . ':' . 1 }}">
                    @endforeach

                    <button type="submit" class="btn btn-brand btn-lg w-100 mt-5 py-3 fw-bold">
                        Complete Booking
                    </button>
                </form>
            </div>

            {{-- Right Side: Summary --}}
            <div class="col-lg-4 bg-light p-5 border-start">
                <h4 class="fw-bold text-uppercase mb-4">Summary</h4>

                <div class="mb-4">
                    <p class="small text-muted mb-1 text-uppercase">Hotel</p>
                    <h6 class="fw-bold">{{ $checkoutPayload['hotel']['name'] }}</h6>
                    <small class="text-muted text-uppercase" style="font-size: 10px;">
                        {{ $checkoutPayload['hotel']['city'] }}, {{ $checkoutPayload['hotel']['state'] }}
                    </small>
                </div>

                <div class="mb-4">
                    <p class="small text-muted mb-1 text-uppercase">Stay Period</p>
                    <h6 class="fw-bold">
                        {{ \Carbon\Carbon::parse($checkoutPayload['checkIn'])->format('d M') }} —
                        {{ \Carbon\Carbon::parse($checkoutPayload['checkOut'])->format('d M, Y') }}
                    </h6>
                    <span class="badge bg-dark text-uppercase p-2 mt-1">{{ $checkoutPayload['nights'] }} Nights</span>
                </div>

                <div class="border-top border-bottom py-3 mb-4">
                    <p class="small text-muted mb-2 text-uppercase">Selected Rooms</p>
                    @php $calculatedSubtotal = 0; @endphp
                    @foreach ($checkoutPayload['rooms'] as $room)
                        @php
                            $roomTotal = $room['price'] * $checkoutPayload['nights'];
                            $calculatedSubtotal += $roomTotal;
                        @endphp
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ asset('storage/' . $room['path']) }}"
                                class="img-fluid rounded-3 shadow-sm border me-3"
                                style="height: 60px; width: 60px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <span class="small text-uppercase fw-bold d-block">{{ $room['title'] }}</span>
                                <small class="text-muted uppercase" style="font-size: 9px;">
                                    {{ $checkoutPayload['currency_symbol'] }} {{ number_format($room['price'], 2) }} x
                                    {{ $checkoutPayload['nights'] }} Nights
                                </small>
                            </div>
                            <span class="fw-bold small">
                                {{ $checkoutPayload['currency_symbol'] }} {{ number_format($roomTotal, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Coupon Section --}}
                <div class="border-bottom py-3 mb-4">
                    <div id="coupon-input-wrapper" style="display: {{ $coupon_code ? 'none' : 'block' }};">
                        <form onsubmit="applyCouponCode(event)">
                            @csrf
                            <div class="d-flex">
                                <input type="text" id="coupon_code_input" class="form-control"
                                    placeholder="Coupon Code">
                                <button type="submit" id="applyBtn" class="btn-classic btn-classic-dark w-25">
                                    <span id="btnText">Apply</span>
                                    <div id="btnLoader" class="spinner-border spinner-border-sm d-none"></div>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="applied-coupon-wrapper"
                        class="bg-white border p-2 d-flex justify-content-between align-items-center"
                        style="display: {{ $coupon_code ? 'flex' : 'none' }} !important; border-style: dashed !important; border-color: #198754 !important;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-ticket-perforated-fill text-success fs-5 me-2"></i>
                            <div>
                                <small class="text-uppercase fw-bold text-success d-block"
                                    style="font-size: 10px;">Applied</small>
                                <span class="fw-bold" id="active-coupon-name">{{ $coupon_code ?? '' }}</span>
                            </div>
                        </div>
                        <button type="button" onclick="removeCouponCode()"
                            class="btn btn-sm text-danger fw-bold text-uppercase" style="font-size: 11px;">
                            Remove
                        </button>
                    </div>
                    <label class="text-small d-none mt-2" id="couponMsg"></label>
                </div>

                {{-- Pricing Totals --}}
                <div>
                    {{-- We store original values in data attributes for JS reset --}}
                    @php
                        $savings = $calculatedSubtotal - $checkoutPayload['finalTotal'];
                    @endphp

                    <div id="discount-div" class="justify-content-between align-items-center mb-2 text-success"
                        style="display: {{ $savings > 0.5 ? 'flex' : 'none' }} !important;">
                        <span class="h6 fw-bold text-uppercase">Discount Applied</span>
                        <span class="h5 fw-bold" id="discount-amount-display">
                            - {{ $checkoutPayload['currency_symbol'] }} {{ number_format($savings, 2) }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 fw-bold text-uppercase">Total</span>
                        <span class="h3 fw-bold" style="color: var(--brand-gold);" id="display-final-total"
                            data-original="{{ $checkoutPayload['currency_symbol'] . number_format($calculatedSubtotal, 2) }}">
                            {{ $checkoutPayload['currency_symbol'] }}
                            {{ number_format($checkoutPayload['finalTotal'], 2) }}
                        </span>
                    </div>
                </div>

                {{-- Converted Totals --}}
                @if ($checkoutPayload['converted'])
                    @php
                        // Logic to find actual subtotal in hotel currency
                        $actualSubtotal =
                            $calculatedSubtotal /
                            ($checkoutPayload['finalTotal'] / $checkoutPayload['finalActualTotal']);
                    @endphp
                    <div id="actual-total-wrapper"
                        class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                        <span class="small fw-bold text-uppercase text-muted">Payable in Hotel Currency</span>
                        <span class="h5 fw-bold" id="display-actual-total" style="color: var(--brand-gold);"
                            data-original="{{ $checkoutPayload['hotelCurrencySymbol'] . number_format($actualSubtotal, 2) }}">
                            {{ $checkoutPayload['hotelCurrencySymbol'] }}
                            {{ number_format($checkoutPayload['finalActualTotal'], 2) }}
                        </span>
                    </div>
                    <small class="text-muted">*Exchange Rates may differ based on Bank's Rates</small>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const currencySymbol = @json($checkoutPayload['currency_symbol']);
        const hotelSymbol = @json($checkoutPayload['hotelCurrencySymbol']);
        // console.log(@json($checkoutPayload));

        async function applyCouponCode(e) {
            e.preventDefault();
            let code = document.getElementById('coupon_code_input').value;
            if (!code) return;

            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnLoader').classList.remove('d-none');

            $.ajax({
                url: "{{ route('booking.discount.apply') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    couponCode: code,
                    totalAmount: "{{ $checkoutPayload['finalActualTotal'] }}",
                    hotelId: "{{ $checkoutPayload['hotel']['id'] }}",
                    userCountryId: "{{ $checkoutPayload['userCountryId'] }}",
                    nights: "{{ $checkoutPayload['nights'] }}"
                },
                success: function(response) {
                    // console.log(response);
                    if (response.status) {
                        // console.log(response);
                        // return;
                        document.getElementById('coupon-input-wrapper').style.display = 'none';
                        document.getElementById('applied-coupon-wrapper').style.setProperty('display',
                            'flex', 'important');
                        document.getElementById('active-coupon-name').innerText = code;
                        document.getElementById('hidden_coupon_code').value = code;

                        document.getElementById('discount-div').style.setProperty('display', 'flex',
                            'important');
                        document.getElementById('discount-amount-display').innerText = '- ' +
                            currencySymbol + response.discount_amount;
                        document.getElementById('display-final-total').innerText = currencySymbol + response
                            .final_amount;


                        if (document.getElementById('display-actual-total')) {
                            document.getElementById('display-actual-total').innerText = hotelSymbol +
                                response.final_actual_total;
                        }
                    } else {
                        let label = document.getElementById('couponMsg');
                        label.classList.remove('d-none');
                        label.classList.add('d-block', 'text-danger');
                        label.innerText = response.error;
                    }
                },
                complete: function() {
                    document.getElementById('btnText').classList.remove('d-none');
                    document.getElementById('btnLoader').classList.add('d-none');
                }
            });
        }

        async function removeCouponCode() {
            // 1. Immediate UI Reset (Better UX)
            document.getElementById('hidden_coupon_code').value = '';
            document.getElementById('coupon-input-wrapper').style.display = 'block';
            document.getElementById('applied-coupon-wrapper').style.setProperty('display', 'none', 'important');
            document.getElementById('coupon_code_input').value = '';
            document.getElementById('couponMsg').classList.add('d-none');

            // Reset pricing displays to original values from data attributes
            const finalTotalEl = document.getElementById('display-final-total');
            finalTotalEl.innerText = finalTotalEl.getAttribute('data-original');

            const actualTotalEl = document.getElementById('display-actual-total');
            if (actualTotalEl) {
                actualTotalEl.innerText = actualTotalEl.getAttribute('data-original');
            }

            document.getElementById('discount-div').style.setProperty('display', 'none', 'important');

            try {
                await $.ajax({
                    url: "{{ route('booking.discount.remove') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log("Session cleared:", response.message);
                    },
                    error: function(xhr) {
                        console.error("Failed to clear session coupon:", xhr.responseText);
                    }
                });
            } catch (error) {
                console.error("AJAX Error:", error);
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .card,
        .form-control,
        .btn,
        .badge {
            border-radius: 0 !important;
        }

        :root {
            --brand-gold: #d0ac77;
            --dark-bg: #1a1a1a;
        }

        .text-primary {
            color: var(--brand-gold) !important;
        }

        .bg-primary {
            background-color: var(--brand-gold) !important;
        }

        .form-control {
            border: 1px solid #e0e0e0;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--brand-gold);
            box-shadow: none;
        }

        .btn-brand {
            background-color: var(--brand-gold);
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.3s;
            border: none;
        }

        .btn-brand:hover {
            background-color: #a8926d;
            color: white;
        }
    </style>
@endpush
