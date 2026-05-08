@extends('client.layouts.template')

@section('title', 'Explore Hotels')

@section('content')
    <section class="py-5 mt-5 bg-light">
        <div class="container">

            <div class="row">
                <div class="col-12 mb-2">
                    <div class="py-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-2" style="font-size: 0.85rem; letter-spacing: 1px;">
                                        <li class="breadcrumb-item text-uppercase text-muted">Exploration</li>
                                        <li class="breadcrumb-item text-uppercase active primaryfont" aria-current="page">
                                            {{ request('city_id') ? $hotels->first()->city->name ?? 'City' : $hotels->first()->city->state->country->name ?? 'Selected Country' }}
                                        </li>
                                    </ol>
                                </nav>

                                <h2 class="headingfonts fw-bold display-6 mb-0">
                                    Discover Hotels in <span
                                        class="text-dark">{{ request('city_id') ? $hotels->first()->city->name ?? 'City' : $hotels->first()->city->state->country->name ?? 'Selected Country' }}</span>
                                </h2>

                                <div class="mt-2 primaryfont text-muted">
                                    <span>
                                        <i class="bi bi-houses me-1"></i>
                                        <strong>{{ $hotels->total() }}</strong> Hotels found
                                    </span>
                                </div>
                            </div>

                            <div class="text-end d-none d-md-block">
                                <span class="badge rounded-pill bg-dark px-3 py-2">
                                    Best Prices Guaranteed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- Sidebar Filter --}}
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 100px;">
                        <h5 class="headingfonts mb-4">Refine Search</h5>
                        <form action="{{ route('client.hotels.explore') }}" method="GET">
                            <div class="mb-4">
                                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Destination</label>
                                <input type="hidden" name="city_id" id="selected_city_id" value="{{ request('city_id') }}">
                                <div class="position-relative">
                                    <input type="text" id="city_search" class="form-control border-0 bg-light p-3"
                                        placeholder="Search City..." list="city_list"
                                        value="{{ $cities->firstWhere('id', request('city_id'))->full_name ?? '' }}"
                                        autocomplete="off">
                                    <datalist id="city_list">
                                        @foreach ($cities as $city)
                                            <option data-id="{{ $city->id }}" value="{{ $city->full_name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Room Type</label>
                                <select name="room_type" class="form-control border-0 bg-light"
                                    onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}"
                                            {{ request('room_type') == $type ? 'selected' : '' }}>{{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Room
                                    Category</label>
                                <select name="room_category" class="form-control border-0 bg-light"
                                    onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}"
                                            {{ request('room_category') == $category ? 'selected' : '' }}>
                                            {{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn-classic btn-classic-dark w-100 py-2 mt-2">Update Results</button>
                            <a href="{{ route('client.hotels.explore') }}"
                                class="btn btn-link btn-sm w-100 text-decoration-none mt-2 text-muted">Clear All</a>
                        </form>
                    </div>
                </div>

                {{-- Hotel Listing --}}
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="headingfonts m-0">Available Hotels ({{ $hotels->total() }})</h4>
                    </div>

                    @forelse($hotels as $hotel)
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden hotel-card position-relative">

                            @if ($hotel->active_offer)
                                {{-- <div class="position-absolute top-0 end-0 z-3 m-3">
                                    <span class="badge bg-danger text-uppercase px-3 py-2 shadow-sm"
                                        style="border-radius: 0; letter-spacing: 1px;">
                                        {{ number_format($hotel->active_offer->value) }}{{ $hotel->active_offer->type == 'fixed' ? $hotel->currency_symbol : '%' }}
                                        OFF - {{ $hotel->active_offer->message ?? 'Limited Time Offer' }}
                                    </span>
                                </div> --}}
                            @endif

                            <div class="row g-0">
                                <div class="col-md-4">
                                    <img src="{{ asset('storage/' . ($hotel->images->first()->path ?? 'placeholders/hotel.jpg')) }}"
                                        class="img-fluid h-100 object-fit-cover" alt="{{ $hotel->name }}"
                                        style="min-height: 250px;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body p-4 d-flex flex-column h-100">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="headingfonts mb-1">{{ $hotel->name }}</h4>
                                                <p class="text-muted small mb-2">
                                                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $hotel->city->name }}
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                @if ($hotel->reviews_avg_rating)
                                                    <span class="badge bg-success fw-bold">
                                                        {{ number_format($hotel->reviews_avg_rating, 1) }} ★
                                                    </span>
                                                    <p class="small text-muted mt-1 mb-0">{{ $hotel->reviews_count }}
                                                        Reviews</p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- PROMO MESSAGE SECTION --}}
                                        @if ($hotel->active_offer)
                                            <div class="alert alert-warning py-2 px-3 border-0 mb-3"
                                                style="border-radius: 0; background-color: #ffb49f6c;">
                                                <p class="mb-0 small fw-bold text-dark">
                                                    <i class="bi bi-megaphone-fill me-2 text-warning"></i>
                                                    Flat
                                                    {{-- {{ $hotel->active_offer->type }} --}}
                                                    {{ $hotel->active_offer->type == 'percentage' ? $hotel->active_offer->value . '%' : ($currency ?? '$') . number_format($hotel->active_offer->value) }}
                                                    Limited time Discount at this Hotel !
                                                </p>
                                            </div>
                                        @endif

                                        <p class="text-secondary small flex-grow-1">
                                            {{ Str::limit($hotel->description ?? 'Experience the pinnacle of luxury and comfort in the heart of the city.', 160) }}
                                        </p>

                                        <div class="d-flex justify-content-between align-items-end mt-3 border-top pt-3">
                                            <div>
                                                @if ($hotel->active_offer)
                                                    <p class="text-danger mb-0 small fw-bold">
                                                        <span class="badge bg-danger small text-uppercase"
                                                            style="font-size: 0.7rem;">
                                                            {{ $hotel->active_offer->type == 'percentage' ? $hotel->active_offer->value . '%' : ($currency ?? '$') . number_format($hotel->active_offer->value) }}
                                                            Off
                                                        </span>
                                                    </p>
                                                @else
                                                    <p class="text-muted mb-0 small">Best Price Guaranteed</p>
                                                @endif
                                                <span class="text-muted small d-block">Starting from</span>

                                                @php
                                                    $minPrice = $hotel->rooms->min('price');
                                                    $currency = $hotel->rooms->first()->currency_symbol ?? '$';
                                                    $offerType = $hotel->rooms->first()->offer_type ?? null;

                                                    if ($hotel->active_offer) {
                                                        $discountVal = $hotel->active_offer->value;
                                                        $finalPrice =
                                                            $hotel->active_offer->type == 'percentage'
                                                                ? $minPrice - ($minPrice * $discountVal) / 100
                                                                : $minPrice - $discountVal;
                                                    }
                                                @endphp
                                                @if ($hotel->active_offer)
                                                    <span class="fs-4 fw-bold text-primary">
                                                        {{ $currency }}{{ number_format($finalPrice, 2) }}
                                                    </span>
                                                    <del
                                                        class="text-muted small ms-2">{{ $currency }}{{ number_format($minPrice, 2) }}</del>
                                                @else
                                                    <span class="fs-4 fw-bold text-dark">
                                                        {{ $currency }}{{ number_format($minPrice, 2) }}
                                                    </span>
                                                @endif
                                            </div>

                                            <a href="{{ route('client.room.explore', $hotel->id) }}"
                                                class="btn-classic btn-classic-dark text-center px-4 stretched-link">
                                                View Rooms
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white shadow-sm">
                            <i class="bi bi-search text-muted display-1 mb-3"></i>
                            <h5 class="text-muted">No hotels match your current filters.</h5>
                            <a href="{{ route('client.hotels.explore') }}" class="btn btn-link">Clear all filters</a>
                        </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $hotels->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Variables check kar lena ki upar defined hain (allCities, searchInput, dataList, hiddenInput)
        const allCities = @json($cities->map(fn($city) => ['id' => $city->id, 'name' => $city->full_name]));
        const searchInput = document.getElementById('city_search');
        const dataList = document.getElementById('city_list');
        const hiddenInput = document.getElementById('selected_city_id');

        searchInput.addEventListener('input', function(e) {
            const val = e.target.value.trim();
            dataList.innerHTML = '';

            if (val === "") {
                hiddenInput.value = "";
                return;
            }

            const matches = allCities
                .filter(city => city.name.toLowerCase().includes(val.toLowerCase()))
                .slice(0, 10);

            matches.forEach(city => {
                const option = document.createElement('option');
                option.value = city.name;
                dataList.appendChild(option);
            });

            const exactMatch = matches.find(city => city.name.toLowerCase() === val.toLowerCase());
            if (exactMatch) {
                hiddenInput.value = exactMatch.id;
            } else {
                hiddenInput.value = "";
            }
        });

        searchInput.addEventListener('change', function() {
            const val = this.value.trim();

            if (val === "") {
                hiddenInput.value = "";
                this.form.submit();
            } else {
                const selectedCity = allCities.find(c => c.name.toLowerCase() === val.toLowerCase());
                if (selectedCity) {
                    hiddenInput.value = selectedCity.id;
                    this.form.submit();
                } else {
                    hiddenInput.value = "";
                }
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .hotel-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endpush
