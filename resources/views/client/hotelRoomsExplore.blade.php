@extends('client.layouts.template')

@section('title', 'Explore Rooms of ' . $hotel->name)

@section('content')
    <div class="container py-5 mt-4">
        <div class="row">
            <div class="col-12 mb-2">
                <div class="pt-4 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 2px;">
                                    <li class="breadcrumb-item text-muted">Exploration</li>
                                    <li class="breadcrumb-item active text-dark fw-bold">
                                        {{ $hotel->city->name ?? 'Selected City' }}</li>
                                </ol>
                            </nav>
                            <h2 class="headingfonts fw-bold display-6 mb-0">
                                Discover Rooms of <span class="text-dark">{{ $hotel->name ?? 'Selected Hotel' }}</span>
                            </h2>
                            <div class="mt-2 primaryfont text-muted small">
                                @if (session('booking_check_in') && session('booking_check_out'))
                                    <span class="me-3" id="dateEditTrigger" style="cursor: pointer;">
                                        <i class="bi bi-calendar3 me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse(session('booking_check_in'))->format('d M') }} —
                                        {{ \Carbon\Carbon::parse(session('booking_check_out'))->format('d M, Y') }}
                                    </span>
                                @endif
                                <span><i class="bi bi-houses me-1"></i> <strong>{{ $rooms->count() }}</strong>
                                    Categories Available</span>
                            </div>
                        </div>
                        <div class="text-end d-none d-md-block">
                            @if ($offer->message)
                                <span class="badge rounded-0 bg-danger px-3 py-2 text-uppercase"
                                    style="letter-spacing: 1px;">% {{ $offer->message }}</span>
                            @else
                                <span class="badge rounded-0 bg-dark px-3 py-2 text-uppercase"
                                    style="letter-spacing: 1px;">Best
                                    Prices Guaranteed</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-3 align-items-center">
                        <div class="col-md-8">
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                {{ $hotel->city->name }}, {{ $hotel->city->state->name }},
                                {{ $hotel->city->state->country->name }}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if ($hotel->reviews_avg_rating)
                                <span class="badge bg-success fs-6">{{ number_format($hotel->reviews_avg_rating, 1) }}
                                    ★</span>
                                <span class="text-muted small ms-2">({{ $hotel->reviews_count }} Reviews)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-2 mb-5 shadow-sm rounded-0 overflow-hidden bg-white">
            <div class="col-md-8">
                <div id="hotelImageSlider" class="carousel slide h-100" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        @foreach ($hotel->images as $index => $img)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }} h-100">
                                <img src="{{ asset('storage/' . $img->path) }}" class="d-block w-100 object-fit-cover"
                                    style="height: 450px;" alt="Hotel Image">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#hotelImageSlider"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#hotelImageSlider"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
            <div class="col-md-4 d-none d-md-flex flex-column gap-2">
                {{-- {{ $rooms->count() }} --}}
                @foreach ($rooms->skip(rand(0, $rooms->count() - 2))->take(2) as $room)
                    <img src="{{ asset('storage/' . $room->images->first()->path) }}"
                        class="img-fluid w-100 object-fit-cover" style="height: 221px;">
                @endforeach
            </div>
        </div>

        @if ($offer)
            <div class="alert border-0 p-4 d-flex align-items-center justify-content-between shadow-sm my-4"
                style="background-color: #fff9e6; border-left: 5px solid #bca47f !important; border-radius: 0;">
                <div class="d-flex align-items-center">
                    <i class="bi bi-gift-fill text-warning me-4 d-none d-md-block" style="font-size: 2rem;"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1 text-uppercase">
                            {{ $offer->type == 'percentage' ? $offer->value . '% OFF' : 'FLAT ' . $offer->value }}
                            {{ $offer->message ?? 'Limited Time Offer, Grab it Fast' }}</h6>
                        <p class="mb-0 text-muted small">Automatic discount applied to all eligible rooms.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-5">
            <div class="col-lg-8">
                <div class="mb-5">
                    <h4 class="fw-bold mb-3 headingfonts text-uppercase" style="letter-spacing: 1px;">About the Hotel</h4>
                    <p class="text-secondary lh-lg">
                        {{ $hotel->description ?? 'Experience luxury at ' . $hotel->name . '.' }}
                    </p>
                    <div class="d-flex flex-wrap gap-4 mt-4 py-3 border-top border-bottom">
                        @foreach ($hotel->amenities as $item)
                            <div class="text-center">
                                <i class="bi {{ $item->icon }} fs-4 text-primary"></i>
                                <p class="extra-small text-uppercase mb-0 mt-1" style="font-size: 0.65rem;">
                                    {{ $item->title }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold headingfonts mb-0 text-uppercase" style="letter-spacing: 1px;">Select Your Room</h4>
                    @if ($offer)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <i class="bi bi-tag-fill me-1"></i> SPECIAL OFFER PRICES
                        </span>
                    @endif
                </div>

                @include('client.partials.roomcards', ['rooms' => $rooms, 'offer' => $offer])
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; max-height: calc(100vh - 120px); overflow-y: auto;">
                    <div class="card border-0 shadow-sm p-4 rounded-0 mb-4">
                        <h5 class="fw-bold mb-4 headingfonts text-uppercase" style="letter-spacing: 1px;">Why book with us?
                        </h5>
                        <ul class="list-unstyled mb-0">
                            @if ($hotel->cancellation_charge == 0)
                                <li class="mb-4 d-flex align-items-start">
                                    <i class="bi bi-receipt text-primary me-3 fs-4"></i>
                                    <div>
                                        <span class="d-block fw-bold small text-uppercase">Free Cancellation</span>
                                        <span class="text-muted extra-small">No Charge if Change in you Plan</span>
                                    </div>
                                </li>
                            @endif
                            <li class="mb-4 d-flex align-items-start">
                                <i class="bi bi-shield-check text-primary me-3 fs-4"></i>
                                <div>
                                    <span class="d-block fw-bold small text-uppercase">Best Price Guaranteed</span>
                                    <span class="text-muted extra-small">Professional price matching.</span>
                                </div>
                            </li>
                            <li class="mb-4 d-flex align-items-start">
                                <i class="bi bi-clock-history text-primary me-3 fs-4"></i>
                                <div>
                                    <span class="d-block fw-bold small text-uppercase">24/7 Concierge</span>
                                    <span class="text-muted extra-small">Always available for support.</span>
                                </div>
                            </li>
                            <li class="mb-0 d-flex align-items-start">
                                <i class="bi bi-credit-card text-primary me-3 fs-4"></i>
                                <div>
                                    <span class="d-block fw-bold small text-uppercase">Secure Checkout</span>
                                    <span class="text-muted extra-small">Encrypted Stripe payments.</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    @if (($hotel->city->state->country->iso_code ?? '') != 'IN')
                        <div class="p-3 bg-light border-start border-3 border-primary small text-muted">
                            Prices are converted to <strong>{{ $userCountry['currency_code'] ?? 'USD' }}</strong>
                            dynamically.
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm p-4 rounded-0 mb-4">
                        <h5 class="fw-bold mb-3 headingfonts text-uppercase" style="letter-spacing: 1px;">Guest Reviews
                        </h5>

                        <!-- Summary Rating -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-primary text-white px-3 py-2 fw-bold me-3">
                                {{ number_format($hotel->reviews_avg_rating, 1) }} <i class="bi bi-star-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase small">Excellent Performance</div>
                                <div class="text-muted extra-small">Based on {{ $hotel->reviews_count }} verified stays
                                </div>
                            </div>
                        </div>


                        <div class="category-ratings mb-4">
                            @php
                                $categories = [
                                    'Services' => $hotel->reviews_avg_services ?? $hotel->reviews_avg_rating,
                                    'Cleanliness' => $hotel->reviews_avg_cleaning ?? $hotel->reviews_avg_rating,
                                    'Food' => $hotel->reviews_avg_food ?? $hotel->reviews_avg_rating,
                                    'Hospitality' => $hotel->reviews_avg_hospitality ?? $hotel->reviews_avg_rating,
                                ];
                            @endphp

                            @foreach ($categories as $label => $val)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between small text-uppercase fw-bold mb-1"
                                        style="font-size: 0.65rem;">
                                        <span>{{ $label }}</span>
                                        <span>{{ number_format($val, 1) }} <i class="bi bi-star-fill"></i></span>
                                    </div>
                                    <div class="progress rounded-0" style="height: 4px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ ($val / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="reviews-list border-top pt-4" style="max-height: 400px; overflow-y: auto;">
                            @forelse($hotel->reviews->take(5) as $review)
                                <div class="review-item mb-4 pb-3 border-bottom border-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold small text-capitalize">{{ $review->user->name }}</span>
                                        <span class="badge bg-light text-dark extra-small border">{{ $review->rating }} <i
                                                class="bi bi-star-fill"></i></span>
                                    </div>
                                    <p class="text-muted small mb-0 italic">"{{ Str::limit($review->comment, 120) }}"</p>
                                </div>
                            @empty
                                <div class="text-center text-muted small">No reviews yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
