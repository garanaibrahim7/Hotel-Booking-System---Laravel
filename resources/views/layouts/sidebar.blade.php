<aside class="app-sidebar bg-body-secondary shadow" id="sidebar" data-bs-theme="dark">
    <div class="sidebar-brand">
        {{-- <a href="{{ route('dashboard') }}" class="brand-link"> --}}
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img src="{{ asset('assets/img/AdminLTELogo.png') }}" class="brand-image opacity-75 shadow" alt="AdminLTELogo"
                height="35" width="35">
            <span class="brand-text fw-light">AdminLTE 4</span>
        </a>
    </div>




    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                @foreach ($sidebarlinks as $link)
                    @if (is_array($link['link']))
                        @php
                            $active = request()->routeIs(...$link['link']);
                        @endphp
                        <li class="nav-item {{ $active ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ $active ? 'active' : '' }}">
                                <i class="nav-icon {{ $active ? $link['fill-icon'] : $link['icon'] }}"></i>
                                <p>
                                    {{ $link['title'] }}
                                </p>
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach ($link['link'] as $link)
                                    @php
                                        $active = request()->routeIs($link['link']);
                                        $linkHref = Route::has($link['link']) ? route($link['link']) : $link['link'];
                                    @endphp
                                    <li class="nav-item ms-3">
                                        <a href="{{ $linkHref }}"
                                            class="nav-link
                                            {{ $active ? 'active' : '' }}">
                                            <i class="nav-icon {{ $active ? $link['fill-icon'] : $link['icon'] }}"></i>
                                            <p>{{ $link['title'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        @php
                            $active = request()->routeIs($link['link']);
                            $linkHref = Route::has($link['link']) ? route($link['link']) : $link['link'];
                        @endphp
                        <li class="nav-item">
                            <a href="{{ $linkHref }}" class="nav-link {{ $active ? 'active' : '' }}">
                                <i class="nav-icon {{ $active ? $link['fill-icon'] : $link['icon'] }}"></i>
                                <p>{{ $link['title'] }}</p>
                            </a>
                        </li>
                    @endif
                @endforeach





                {{-- @for ($i = 1; $i <= 20; $i++)
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-app"></i>
                            <p>Other Options {{ $i }}</p>
                        </a>
                    </li>
                @endfor --}}
            </ul>
        </nav>
    </div>
</aside>
