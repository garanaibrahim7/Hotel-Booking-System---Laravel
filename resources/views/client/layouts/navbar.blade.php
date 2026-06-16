<nav class="navbar navbar-expand-lg fixed-top custom-nav py-3" style="z-index: 1050;">
    <div class="container-fluid px-4 px-md-5">
        <a class="navbar-brand headingfonts text-light" href="{{ route('client.home') }}">HOTEL LOGO</a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse d-none d-lg-block">
            <ul class="navbar-nav ms-auto gap-4">
                @php
                    $roomsCount = count(session('stay.items', []));
                @endphp
                @foreach (config('menu.client.navbar') as $title => $link)
                    @if ($title == 'Login')
                        @auth
                            <li>
                                <a class="nav-link {{ request()->routeIs('client.profile') ? 'active-link' : '' }}"
                                    href="{{ Route::has('client.profile') ? route('client.profile') : 'client.profile' }}">
                                    {{ 'Profile' }}
                                </a>
                            </li>
                            @continue
                        @endauth
                    @elseif($title == 'Stay Summary')
                        <li>
                            <a class="nav-link d-flex justify-content-between align-items-center position-relative {{ request()->routeIs($link) ? 'active-link' : '' }}"
                                href="{{ route($link) }}">

                                <span>{{ $title }}</span>
                                @if ($roomsCount > 0)
                                    <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                                        {{ $roomsCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @continue
                    @endif
                    <li>
                        <a class="nav-link {{ request()->routeIs($link) ? 'active-link' : '' }}"
                            href="{{ Route::has($link) ? route($link) : route('client.home') . $link }}">
                            {{ $title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title headingfonts" id="mobileMenuLabel">MENU</h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="list-group list-group-flush">
            @foreach (config('menu.client.navbar') as $title => $link)
                @if ($title == 'Login')
                    @auth
                        <li class="list-group-item border-0 py-3 px-4">
                            <a href="{{ Route::has('client.profile') ? route('client.profile') : 'client.profile' }}"
                                class="text-decoration-none text-dark fw-bold d-block  {{ request()->routeIs('client.profile') ? 'active-link' : '' }}">
                                Profile
                            </a>
                        </li>
                        @continue
                    @endauth
                @endif
                <li class="list-group-item border-0 py-3 px-4">
                    <a href="{{ Route::has($link) ? route($link) : $link }}"
                        class="text-decoration-none
                        text-dark fw-bold d-block {{ request()->routeIs($link) ? 'active-link' : '' }}">
                        {{ $title }}
                    </a>
                </li>
            @endforeach
            {{-- <li class="list-group-item border-0 py-3 px-4">
                <a href="#" class="text-decoration-none text-dark fw-bold d-block">HOME</a>
            </li>
            <li class="list-group-item border-0 py-3 px-4">
                <a href="{{ route('client.hotels.explore') }}"
                    class="text-decoration-none text-dark fw-bold d-block">HOTELS</a>
            </li>
            <li class="list-group-item border-0 py-3 px-4">
                <a href="{{ route('client.rooms') }}" class="text-decoration-none text-dark fw-bold d-block">ROOMS</a>
            </li>
            <li class="list-group-item border-0 py-3 px-4">
                <a href="#" class="text-decoration-none text-dark fw-bold d-block">ABOUT</a>
            </li>
            <li class="list-group-item border-0 py-3 px-4">
                <a href="#" class="text-decoration-none text-dark fw-bold d-block">CONTACT</a>
            </li> --}}
        </ul>
    </div>
</div>
