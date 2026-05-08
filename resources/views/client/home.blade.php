@extends('client.layouts.template')

@section('title', 'Home')
@section('body-class', 'is-home')

@section('content')
    <section class="intro-section bg-off-white">
        <div class="hero-header position-relative">
            <div class="introtext d-flex flex-column justify-content-center align-items-center text-light gap-4">
                <span>Experience Luxury Like Never Before</span>
                <h1 class="headingfonts text-center mx-auto">Book Your Dream Stay Today.</h1>

                {{-- @include('client.partials.search-bar') --}}
                <div class="position-absolute" style="top: 70vh;">
                    @include('client.partials.searchForm')
                </div>
            </div>
        </div>




    </section>

    @if ($localSpecialRooms->isNotEmpty())
        <section class="bg-off-white py-5" id="explore">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-12 col-md-6">
                        <span class="text-uppercase small text-secondary d-block mb-2" style="letter-spacing:5px;">
                            Exclusive Deals
                        </span>
                        <h1 class="fw-normal lh-sm mb-3 headingfonts">
                            Deals in Your City
                        </h1>
                    </div>
                </div>

                <div class="swiper featuredRoomsSwiper px-2 py-4">
                    <div class="swiper-wrapper">
                        @forelse ($localSpecialRooms as $room)
                            <div class="swiper-slide h-auto">
                                <a class="text-decoration-none small fw-bold d-flex flex-column justify-content-between align-items-center"
                                    href="{{ route('client.room.details', $room->id) }}">
                                    <div class="roomcard border-0 shadow-sm h-100 mx-2 position-relative bg-white">

                                        @if ($room->offer_price)
                                            <div class="position-absolute top-0 start-0 z-3 m-3">
                                                <span class="badge rounded-0 px-3 py-2 text-uppercase fw-bold shadow-sm"
                                                    style="background: #bca47f; font-size: 0.65rem; letter-spacing: 1px;">
                                                    <i class="bi bi-tag-fill me-1"></i> Special Offer
                                                </span>
                                            </div>
                                        @endif

                                        <div class="ratio ratio-4x3 overflow-hidden">
                                            <img src="{{ $room->images->first() ? asset('storage/' . $room->images->first()->path) : asset('images/placeholder.jpg') }}"
                                                class="card-img-top object-fit-cover transition-zoom w-100 h-100"
                                                alt="{{ $room->title }}">
                                        </div>

                                        <div class="room-card-body d-flex flex-column align-items-center p-4">
                                            {{-- 2. Category & Hotel Name --}}
                                            <span class="text-uppercase text-muted"
                                                style="font-size: 0.6rem; letter-spacing: 2px;">
                                                {{ $room->category }} | {{ $room->hotel->name }}
                                            </span>

                                            <h5 class="headingfonts mt-2 text-dark fw-bold">{{ $room->title }}</h5>

                                            @if ($room->offer)
                                                <p class="small text-primary fw-bold mb-1" style="font-size: 0.7rem;">
                                                    {{ $room->offer }}
                                                </p>
                                            @endif

                                            <p class="text-muted small text-center px-2">
                                                {{ Str::limit($room->description, 80) }}
                                            </p>

                                            <div class="mt-auto pt-3 border-top w-100 text-center">

                                                <div class="fs-5 text-dark">
                                                    @if ($room->offer_price)
                                                        <small
                                                            class="text-muted text-decoration-line-through fs-6 fw-normal me-2">
                                                            {{ number_format($room->converted_price, 2) }}
                                                        </small>

                                                        <span class="text-primary">
                                                            {{ $room->user_currency_symbol }}
                                                            {{ number_format($room->offer_price, 2) }}
                                                        </span>
                                                    @else
                                                        {{ $room->user_currency_symbol }}
                                                        {{ number_format($room->converted_price, 2) }}
                                                    @endif
                                                </div>

                                                <small class="text-muted d-block mb-2">Per Night</small>

                                                <span
                                                    class="text-uppercase text-dark border-bottom border-dark pb-1 mt-2 d-inline-block"
                                                    style="letter-spacing: 1px; font-size: 0.7rem;">
                                                    Explore Details
                                                </span>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted">No special offers available in your city right now.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="countries-presence py-5 bg-white">
        <div class="content">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <span class="text-uppercase small text-secondary d-block mb-2" style="letter-spacing:5px;">
                        Our Presence
                    </span>
                    <h1 class="headingfonts mb-4" style="font-size: 40px;">
                        We are currently available in {{ $hotelCities->count() }} Cities
                    </h1>
                    <p class="text-secondary small lh-lg mb-4">
                        Explore our luxury stays across different borders. From urban escapes to tropical paradises, we
                        bring comfort to you globally.
                    </p>
                </div>

                <div class="col-md-7">
                    <div class="d-flex flex-wrap gap-3 justify-content-md-end">
                        @foreach ($hotelCities as $key => $city)
                            <div
                                class="country-badge px-4 py-3 border d-flex align-items-center gap-2 shadow-sm transition-hover">
                                @if ($key > 7)
                                    <span
                                        class="fw-bold headingfonts text-dark">{{ '+' . $hotelCities->count() - $key . ' More' }}</span>
                                    @break

                                @else
                                    <span class="fw-bold headingfonts text-dark">{{ $city->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="featuredrooms py-5">
        <div class="content">
            <div class="col-12 col-md-6 d-flex flex-column align-items-center align-items-md-start mb-5">
                <span class="text-uppercase small text-secondary d-block mb-2" style="letter-spacing:5px;">
                    rooms
                </span>
                <h1 class="fw-normal lh-sm mb-3 headingfonts text-center text-md-start">
                    Featured Rooms
                </h1>
            </div>


            <div class="swiper featuredRoomsSwiper px-2 py-4">
                <div class="swiper-wrapper">
                    @foreach ($featuredRooms as $room)
                        <div class="swiper-slide h-auto">
                            <a class="text-decoration-none small fw-bold d-flex flex-column justify-content-between align-items-center"
                                href="{{ route('client.room.details', $room->id) }}">
                                <div class="roomcard border-0 shadow-sm h-100 mx-2 position-relative bg-white">

                                    {{-- @if ($room->offer_price)
                                        <div class="position-absolute top-0 start-0 z-3 m-3">
                                            <span class="badge rounded-0 px-3 py-2 text-uppercase fw-bold shadow-sm"
                                                style="background: #bca47f; font-size: 0.65rem; letter-spacing: 1px;">
                                                <i class="bi bi-tag-fill me-1"></i> Special Offer
                                            </span>
                                        </div>
                                    @endif --}}

                                    <div class="ratio ratio-4x3 overflow-hidden">
                                        <img src="{{ $room->images->first() ? asset('storage/' . $room->images->first()->path) : asset('images/placeholder.jpg') }}"
                                            class="card-img-top object-fit-cover transition-zoom"
                                            alt="{{ $room->title }}">
                                    </div>

                                    <div class="room-card-body d-flex flex-column align-items-center p-4">
                                        {{-- 2. Category & Hotel Name --}}
                                        <span class="text-uppercase text-muted"
                                            style="font-size: 0.6rem; letter-spacing: 2px;">
                                            {{ $room->category }} | {{ $room->hotel->name }}
                                        </span>

                                        <h5 class="headingfonts mt-2 text-dark fw-bold">{{ $room->title }}</h5>

                                        {{-- @if ($room->offer)
                                            <p class="small text-primary fw-bold mb-1" style="font-size: 0.7rem;">
                                                {{ $room->offer }}
                                            </p>
                                        @endif --}}

                                        <p class="text-muted small text-center px-2">
                                            {{ Str::limit($room->description, 80) }}
                                        </p>

                                        <div class="mt-auto pt-3 border-top w-100 text-center">

                                            <span class="fs-5 text-dark d-flex flex-column align-items-center gap-2">
                                                <span class="text-primary">
                                                    {{ $room->hotel->city->state->country->currency_symbol }}
                                                    {{ number_format($room->price, 2) }}
                                                </span>

                                                <small class="text-muted mb-2 fs-6">Per Night</small>

                                                <span class="text-uppercase text-dark border-bottom border-dark pb-1 mt-2"
                                                    style="letter-spacing: 1px; font-size: 0.7rem;">Explore Details</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- <div class="swiper-slide h-auto">
                            <div class="roomcard border-0 shadow-sm h-100 mx-2">
                                <div class="ratio ratio-4x3 overflow-hidden">
                                    <img src="{{ $room->images->first() ? asset('storage/' . $room->images->first()->path) : asset('images/placeholder.jpg') }}"
                                        class="card-img-top object-fit-cover" alt="{{ $room->name }}">
                                </div>

                                <div class="room-card-body d-flex flex-column align-items-center p-4">
                                    <h5 class="headingfonts mt-2 text-dark">{{ $room->name }}</h5>
                                    <p class="text-muted small text-center py-2 px-2">
                                        {{ Str::limit($room->description, 80) }}
                                    </p>

                                    <div class="mt-auto pt-3 border-top w-100 text-center">
                                        <a class="text-decoration-none small fw-bold d-flex flex-column justify-content-between align-items-center"
                                            href="?{{ $room->id }}">
                                            <span class="fs-5 text-dark">
                                                {{ $room->hotel->city->state->country->currency_symbol . ' ' . number_format($room->price, 2) }}
                                                <small class="text-muted">/Night</small>
                                            </span>
                                            <span class="text-uppercase text-dark border-bottom border-dark pb-1"
                                                style="letter-spacing: 1px;">Book Now</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    @endforeach
                </div>

            </div>
        </div>
    </section>


    @include('client.layouts.stats')

    @include('client.layouts.reviews')


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.featuredRoomsSwiper', {
                // Infinite loop
                loop: true,

                // Auto play
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false, // Keeps autoplay running after manual swipe
                    pauseOnMouseEnter: true, // Stops when user hovers
                },

                // Responsive breakpoints
                slidesPerView: 1,
                spaceBetween: 20,
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },

                // Optional pagination dots
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
            });
        });


        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.custom-nav');
            if (window.scrollY > 700) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });


        var swiper = new Swiper(".featuredRoomsSwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
    </script>
@endpush

@push('styles')
    <style>
        .swiper-slide {
            height: initial !important;
        }

        .roomcard {
            transition: transform 0.3s ease;
        }

        .roomcard:hover {
            transform: translateY(-5px);
        }


        .roomcard {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .roomcard:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
        }

        .transition-zoom {
            transition: transform 0.5s ease;
        }

        .roomcard:hover .transition-zoom {
            transform: scale(1.1);
        }

        .text-primary {
            color: #c8964a !important;
            /* Gold/Bronze theme color */
        }

        .border-primary {
            border-color: #e2b26a !important;
        }
    </style>
@endpush
