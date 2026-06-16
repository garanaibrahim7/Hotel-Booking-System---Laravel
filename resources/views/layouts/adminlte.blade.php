<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title', 'Dashboard')</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <!--end::Primary Meta Tags-->

    <meta name="supported-color-schemes" content="light dark" />

    @vite(['resources/css/adminlte.scss', 'resources/js/adminlte.js'])

    @stack('styles')



    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <div class="app-wrapper">
        @auth
            @include('layouts.navbar')
            @include('layouts.sidebar')
        @endauth

        @if (session('message'))
            <div id="flashAlert" class="alert alert-{{ session('type', 'success') }} alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    @yield('content', 'No Content')
                </div>
            </div>
        </main>

        {{-- <x-alert-model id="confirmActionModal" title="Confirm Action">
            <span id="confirmModalBody">Are you sure?</span>
            <x-slot:action>
                <form id="confirmActionForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" id="confirmModalBtn" class="btn btn-danger">Confirm</button>
                </form>
            </x-slot>
        </x-alert-model> --}}

        <div class="alert alert-success d-none">
            User created successfully
        </div>

        @include('layouts.footer')

    </div>


    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmModalBody">Are you sure?</p>
                </div>
                <div class="modal-footer">

                    <form id="confirmActionForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="confirmModalBtn" class="btn btn-danger">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


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


    <x-alert-model title="Confirm Action">
        <div class="text-center p-3">

            <i id="actionIcon" class="bi bi-question-circle text-primary display-4 mb-3"></i>
            <p id="actionMessage" class="mb-0 fw-semibold text-dark"></p>
            <p class="small text-muted mt-2">This action will update the booking logs immediately.</p>
        </div>
        <x-slot:action>
            <form action="" method="post" id="deleteForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="DELETE">
                <input type="submit" value="Confirm" id="actionConfirmBtn" class="btn btn-primary px-4 rounded-pill">
            </form>
            {{-- <a id="actionConfirmBtn" href="#" class="btn btn-primary px-4 rounded-pill">Confirm</a> --}}
        </x-slot>
    </x-alert-model>


    @if (session('success'))
        <script>
            // console.log('{{ session('success') }}');

            document.addEventListener('DOMContentLoaded', function() {
                // Toast.fire({
                //     icon: 'success',
                //     title: '{{ session('success') }}'
                // });

                Swal.fire({
                    title: 'Done',
                    text: '{{ session('success') }}',
                    icon: 'success'
                });
            });
        </script>
    @endif


    @if (session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Warning',
                    text: '{{ session('warning') }}',
                    icon: 'warning'
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error',
                    text: '{{ session('error') }}',
                    icon: 'error'
                });
            });
        </script>
    @endif


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @stack('afterLoadScripts')
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });



        });
        function confirmAction(url, title = 'Are you sure?', message = 'Do you really want to proceed?',
            btnClass =
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

        function triggerBookingModal(url, title, message, method = null) {
            const modalEl = document.getElementById('staticBackdrop');

            const titleEl = modalEl.querySelector('.modal-title') || document.getElementById('bookingActionModalLabel');
            if (titleEl) titleEl.innerText = title;

            const msgEl = document.getElementById('actionMessage');
            const btnEl = document.getElementById('actionConfirmBtn');
            const form = document.getElementById('deleteForm');

            if (msgEl) msgEl.innerText = message;
            if (btnEl) form.action = url;

            if (method) {
                document.getElementById('formMethod').value = method;
            }

            const icon = document.getElementById('actionIcon');
            if (icon && btnEl) {
                btnEl.className = 'btn btn-danger px-4 rounded-pill';
                icon.className = 'bi bi-exclamation-triangle text-danger display-4 mb-3';
            }

            let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }
    </script>
    @stack('scripts')
</body>

</html>
