<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $payment->booking->reference_number }}</title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
            /* This removes the default header/footer */
        }

        body {
            font-family: 'serif';
            color: #333;
            margin: 0;
            padding: 50px;
            /* Adjust padding for paper edge */
            background: #fff;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }

        /* Layout Styling */
        .header {
            border-bottom: 2px solid #bca47f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .hotel-name {
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: #f8f5f0;
            padding: 12px;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .total-section {
            margin-top: 30px;
            text-align: right;
        }

        .gold-text {
            color: #bca47f;
            font-weight: bold;
            font-size: 20px;
        }

        /* Print Button (Screen only) */
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f1f1f1;
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 30px;
            }
        }


        .stamp-container {
            position: absolute;
            top: 40%;
            right: 35%;
            z-index: 100;
            pointer-events: none;
        }

        .stamp {
            font-size: 50px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 20px;
            border: 8px double;
            display: inline-block;
            transform: rotate(-25deg);
            opacity: 0.2;
            font-family: 'sans-serif';
            letter-spacing: 5px;
        }

        .stamp-success {
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .stamp-failed {
            color: #d32f2f;
            border-color: #d32f2f;
        }

        .stamp-processing {
            color: #d3ba2f;
            border-color: #d3ba2f;
        }

        .invoice-box {
            position: relative;
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()"
            style="padding: 10px 25px; background: #bca47f; color: #fff; border: none; cursor: pointer; font-weight: bold;">PRINT
            INVOICE</button>
        <a href="{{ route('client.home') }}" style="margin-left: 15px; color: #333;">Return to Site</a>
    </div>

    <div class="invoice-box">
        <div class="stamp-container">
            @if ($payment->status === 1)
                <div class="stamp stamp-success">SUCCESS</div>
            @else
                <div class="stamp stamp-failed">FAILED</div>
            @endif
        </div>
        <div class="header">
            <div class="row align-items-center">
                <div>
                    {{-- Dynamic Hotel Name & Address --}}
                    <div class="hotel-name">{{ $payment->booking->hotel->name ?? 'CLASSIC LUXURY' }}</div>
                    <p style="font-size: 13px; color: #666; margin: 5px 0;">
                        {{ $payment->booking->hotel->address ?? '123 Luxury Avenue, Marine Drive, Mumbai' }},
                        @if (isset($payment->booking->hotel->city))
                            {{ $payment->booking->hotel->city->location_details->city }},
                            {{ $payment->booking->hotel->city->location_details->state }},
                            {{ $payment->booking->hotel->city->location_details->country }}
                        @endif
                    </p>
                </div>
                <div style="text-align: right;">
                    <h1 style="margin: 0; color: #bca47f; font-size: 32px; letter-spacing: 3px;">INVOICE</h1>
                    <p style="margin: 0; font-weight: bold;">REF: #{{ $payment->booking->reference_number }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div style="width: 50%;">
                <strong style="text-transform: uppercase; font-size: 11px; color: #888;">Billed To:</strong><br>
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">{{ $payment->booking->user->name }}
                </div>
                <div>{{ $payment->booking->user->phone }}</div>
                <div>{{ $payment->booking->user->email }}</div>
            </div>
            <div style="text-align: right; width: 50%;">
                <strong style="text-transform: uppercase; font-size: 11px; color: #888;">Booking Date:</strong><br>
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">
                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d M, Y') }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 45%;">Description</th>
                    <th style="text-align: center; width: 35%;">Stay Dates</th>
                    <th style="text-align: right; width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment->booking->items as $item)
                    <tr>
                        <td>
                            <strong style="text-transform: uppercase;">{{ $item->room->details->category }}
                                {{ $item->room->details->type }}</strong><br>
                            <small style="color: #666;">Room: {{ $item->room->room_number }}</small>
                        </td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($item->check_in)->format('d M') }} -
                            {{ \Carbon\Carbon::parse($item->check_out)->format('d M, Y') }}
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            {{ number_format($item->price_at_booking * $payment->exchange_rate, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="row" style="justify-content: flex-end; margin-bottom: 5px;">
                <div style="width: 200px; text-align: right; color: #666; font-size: 14px;">Subtotal:</div>
                <div style="width: 120px; text-align: right; font-weight: bold;">
                    {{ number_format(($payment->booking->sub_amount * $payment->exchange_rate), 2) }}</div>
            </div>

            @if ($payment->booking->discount_amount > 0)
                <div class="row" style="justify-content: flex-end; margin-bottom: 5px; color: #2e7d32;">
                    <div style="width: 200px; text-align: right; font-size: 14px;">Discount Applied:</div>
                    <div style="width: 120px; text-align: right; font-weight: bold;">
                        -{{ number_format(($payment->booking->discount_amount * $payment->exchange_rate), 2) }}</div>
                </div>
            @endif

            <div class="row" style="justify-content: flex-end; margin-top: 15px;">
                <div style="width: 200px; text-align: right; font-weight: bold; text-transform: uppercase;">Total Paid:
                </div>
                <div style="width: 200px; text-align: right;" class="gold-text">
                    {{ number_format($payment->converted_amount, 2) }}
                    <span style="font-size: 14px;">{{ $payment->paid_currency }}</span>
                </div>
            </div>
        </div>

        <div
            style="margin-top: 80px; border-top: 1px solid #eee; padding-top: 20px; font-size: 11px; color: #999; text-align: center; text-transform: uppercase; letter-spacing: 1px;">
            <p>This is a computer-generated invoice and does not require a physical signature.</p>
            <p style="color: #bca47f; font-weight: bold;">Thank you for choosing
                {{ $payment->booking->hotel->name ?? 'Classic Luxury Hotels' }}</p>
        </div>
    </div>
</body>

</html>
