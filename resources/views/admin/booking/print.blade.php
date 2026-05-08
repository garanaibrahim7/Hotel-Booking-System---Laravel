<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    @vite(['resources/css/adminlte.scss'])
    <style>
        body {
            background: white !important;
            font-family: 'Inter', sans-serif;
        }

        .print-header {
            border-bottom: 2px solid #333;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .table th {
            background-color: #f8f9fa !important;
            color: #333 !important;
            text-transform: uppercase;
            font-size: 11px;
        }

        .table td {
            font-size: 12px;
            vertical-align: middle;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 20px;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body onload="window.print()">

    {{-- <body> --}}
    <div class="container-fluid">
        <div
            class="print-header d-flex justify-content-between align-items-center py-4 border-bottom border-3 border-dark mb-4">
            <div class="d-flex align-items-center">
                <div>
                    <h1 class="fw-black text-uppercase tracking-wider mb-0" style="letter-spacing: 2px;">
                        {{ config('app.name', 'Hotel System') }}
                    </h1>
                    <h5 class="text-secondary fw-normal mb-0">{{ $reportTitle }}
                        @can('manager-access')
                            of {{ $bookings->first()->hotel->name }}
                        @endcan
                    </h5>
                    <h6 class="text-secondary fw-normal mb-0">Total Bookings: {{ $bookings->count() }} </h6>

                </div>
            </div>

            <div class="text-end">
                <div class="badge bg-dark px-3 py-2 mb-2 text-uppercase">Booking Report</div>
                <p class="small text-muted mb-0">
                    <i class="bi bi-calendar3 me-1"></i> <strong>Generated:</strong> {{ now()->format('d M, Y') }}
                </p>
                <p class="small text-muted mb-0">
                    <i class="bi bi-clock me-1"></i> <strong>Time:</strong> {{ now()->format('H:i A') }}
                </p>
            </div>
        </div>


        @include('admin.booking.booking-print-table')

        @if (!request('operation') || request('operation') == 'print')
            <div class="d-print-none py-5 mt-5"></div>

            <div class="fixed-bottom bg-white border-top shadow-lg py-3 d-print-none text-center">
                <div class="container-fluid pb-3 d-flex justify-content-center align-items-center gap-1">

                    <button onclick="window.close()" class="btn btn-outline-secondary px-4 rounded-pill">
                        Close Preview
                    </button>

                    <button onclick="window.print()" class="btn btn-dark px-4 rounded-pill">
                        Print Again
                    </button>

                    <div class="border-start border-2 mx-2" style="height: 30px;"></div>

                    @can('manager-access')
                        <a href="{{ route('manager.bookings.print', [...request()->query(), 'operation' => 'download-pdf']) }}"
                            class="btn btn-primary px-4 rounded-pill">
                            Download PDF
                        </a>
                        <a href="{{ route('manager.bookings.print', [...request()->query(), 'operation' => 'download-csv']) }}"
                            class="btn btn-success px-4 rounded-pill">
                            Download CSV
                        </a>
                    @endcan
                    @can('admin-access')
                        <a href="{{ route('admin.bookings.print', [...request()->query(), 'operation' => 'download-pdf']) }}"
                            class="btn btn-primary px-4 rounded-pill">
                            Download PDF
                        </a>
                        <a href="{{ route('admin.bookings.print', [...request()->query(), 'operation' => 'download-csv']) }}"
                            class="btn btn-success px-4 rounded-pill">
                            Download CSV
                        </a>
                    @endcan

                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>

</html>
