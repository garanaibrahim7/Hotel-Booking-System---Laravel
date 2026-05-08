@extends('client.layouts.template')
@section('title', 'Explore Nearby Rooms')

@section('content')
    <div class="container py-4">

        @if ($hotelsWithRooms->isEmpty())
            <div class="row my-5 pt-4">
                <div class="col-12 mb-4">
                    <div class="py-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-2" style="font-size: 0.85rem; letter-spacing: 1px;">
                                        <li class="breadcrumb-item text-uppercase text-muted">Exploration</li>
                                        <li class="breadcrumb-item text-uppercase active primaryfont" aria-current="page">
                                            Global
                                        </li>
                                    </ol>
                                </nav>
                                <h2 class="headingfonts fw-bold display-6 mb-0">
                                    {{-- @if (request('adults') && request('adults')) --}}
                                        No Rooms find according to Your Requirements
                                    {{-- @else
                                        We are Coming to Your City Soon...
                                    @endif --}}
                                </h2>
                                <div class="mt-2 primaryfont text-muted">
                                    <span><i class="bi bi-houses me-1"></i> Stay Tuned</span>
                                </div>
                            </div>
                            <div class="text-end d-none d-md-block">
                                <span class="badge rounded-pill bg-dark px-3 py-2">Best Prices Guaranteed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-4">
                <p class="px-5 fs-3">Find with Different Filters</p>
                @include('client.partials.searchForm', ['cities' => $cities])
            </div>
        @else
            {{-- Results State --}}
            <div class="row mt-5 pt-4">
                <div class="col-12 mb-2">
                    <div class="py-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-2" style="font-size: 0.85rem; letter-spacing: 1px;">
                                        <li class="breadcrumb-item text-uppercase text-muted">Exploration</li>
                                        <li class="breadcrumb-item text-uppercase active primaryfont" aria-current="page">
                                            {{ $hotelsWithRooms->first()->hotel->city->name ?? 'Global' }}
                                        </li>
                                    </ol>
                                </nav>

                                <h2 class="headingfonts fw-bold display-6 mb-0">
                                    Discover Stays in <span
                                        class="text-dark">{{ $hotelsWithRooms->first()->hotel->city->name ?? 'Selected City' }}</span>
                                </h2>

                                <div class="mt-2 primaryfont text-muted">
                                    @if (session('booking_check_in') && session('booking_check_out'))
                                        <span class="me-3">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ \Carbon\Carbon::parse(session('booking_check_in'))->format('d M') }} —
                                            {{ \Carbon\Carbon::parse(session('booking_check_out'))->format('d M, Y') }}
                                        </span>
                                    @endif
                                    <span>
                                        <i class="bi bi-houses me-1"></i>
                                        <strong>{{ $hotelsWithRooms->total() }}</strong> hotels found
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

            <div class="row">
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body p-0">
                            @include('client.layouts.sidebar')
                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
                    {{-- HOTEL-WISE DISPLAY START --}}
                    @foreach ($hotelsWithRooms as $item)
                        {{-- @json($item) --}}
                        <div class="hotel-group mb-5">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h4 class="headingfonts fw-bold text-uppercase mb-0" style="letter-spacing: 2px;">
                                        {{ $item->hotel->name }}
                                    </h4>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-geo-alt me-1"></i> {{ $item->hotel->address }}
                                    </p>
                                </div>
                                @if ($item->offer)
                                    <div class="ms-3">
                                        <span class="badge bg-danger text-uppercase px-3 py-2" style="border-radius: 0;">
                                            {{-- <i class="bi bi-percent me-1"></i> --}}
                                            {{ $item->rooms->first()?->offer_type }} Discount, {{ $item->offer?->message ?? '' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="row g-4">
                                @include('client.partials.roomcards', [
                                        'rooms' => $item->rooms,
                                        'hotel' => $item->hotel,
                                        ])
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-center mt-5">
                        {{ $hotelsWithRooms->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
