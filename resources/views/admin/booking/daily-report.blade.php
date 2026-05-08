<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daily Report - {{ $reportTitle }}</title>
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

<body>
    <div class="container-fluid">
        <div
            class="print-header d-flex justify-content-between align-items-center py-4 border-bottom border-3 border-dark mb-4">
            <div class="d-flex align-items-center">
                <div>
                    <h4 class="text-secondary fw-normal mb-1">{{ config('app.name', 'Hotel System') }}</h4>
                    <span class="fw-black text-uppercase tracking-wider mb-0" style="letter-spacing: 2px;">
                        Today's Booking Report - {{ now()->format('d, F Y - l') }}
                    </span>
                </div>
            </div>

            <div class="text-end">
                <p class="small text-muted mb-0">
                    <strong>Report Period:</strong> Last 24 Hours
                </p>
                <p class="small text-muted mb-0">
                    <strong>Generated:</strong> {{ now()->format('d M, Y H:i A') }}
                </p>
            </div>
        </div>

        @include('admin.booking.booking-print-table')
    </div>
</body>

</html>
