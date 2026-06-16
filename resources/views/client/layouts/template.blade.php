<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Hotel System')</title>

    {{-- <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" /> --}}




    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}

    @vite(['resources/css/clientside/main.scss', 'resources/js/clientside.js'])
    {{-- @vite(['resources/css/clientside/main.scss']) --}}

    @stack('styles')
</head>

<body class="@yield('body-class') d-flex flex-column min-vh-100">

    @include('client.layouts.navbar')


    <main class="flex-fill">
        @yield('content')
        {{-- @include('client.alerts') --}}
    </main>


    @if (session('success') || session('error') || $errors->any())
        <div id="flash-alert"
            class="luxury-alert {{ session('error') || $errors->any() ? 'alert-error' : 'alert-success' }}">
            <div class="alert-content">
                <span class="alert-icon">
                    {!! session('error') || $errors->any() ? '&#10005;' : '&#10003;' !!}
                </span>
                <span class="alert-text">
                    {{ session('success') ?? (session('error') ?? $errors->first()) }}
                </span>
            </div>
            <button type="button" class="alert-close" onclick="closeAlert()">&times;</button>
        </div>

        <script>
            // Auto-hide after 4 seconds
            setTimeout(function() {
                closeAlert();
            }, 4000);

            function closeAlert() {
                var alert = document.getElementById('flash-alert');
                if (alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }
            }
        </script>

        <style>

        </style>
    @endif


    {{-- <x-booking-cart /> --}}
    {{-- Alert Box --}}
    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i id="confirmIcon" class="bi bi-exclamation-circle text-warning display-4"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="confirmTitle">Are you sure?</h5>
                    <p class="text-muted small mb-4" id="confirmMessage">This action cannot be undone.</p>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 py-2 rounded-pill fw-semibold"
                            data-bs-dismiss="modal">Cancel</button>
                        <a href="#" id="confirmActionBtn"
                            class="btn btn-dark w-100 py-2 rounded-pill fw-semibold">Confirm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('client.layouts.footer')

    <script>
        function confirmAction(url, title = 'Are you sure?', message = 'Do you really want to proceed?', btnClass =
            'btn-dark') {
            const modal = new bootstrap.Modal(document.getElementById('globalConfirmModal'));
            const confirmBtn = document.getElementById('confirmActionBtn');

            document.getElementById('confirmTitle').innerText = title;
            document.getElementById('confirmMessage').innerText = message;

            // Reset and apply button classes
            confirmBtn.href = url;
            confirmBtn.className = `btn w-100 py-2 rounded-pill fw-semibold ${btnClass}`;

            modal.show();
        }
    </script>
    @stack('scripts')
</body>

</html>
