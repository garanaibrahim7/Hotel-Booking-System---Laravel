<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $payment->booking->reference_number }}</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 40px;
            background: #fff;
            line-height: 1.4;
        }

        .invoice-box {
            position: relative;
            width: 100%;
        }

        /* Using Tables for Layout instead of Flexbox */
        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header {
            border-bottom: 2px solid #bca47f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .hotel-name {
            font-size: 24px;
            text-transform: uppercase;
            font-weight: bold;
            color: #1a1a1a;
        }

        .invoice-label {
            color: #bca47f;
            font-size: 32px;
            margin: 0;
        }

        .stamp-container {
            position: absolute;
            top: 200px;
            left: 30%;
            z-index: 100;
        }

        .stamp {
            font-size: 60px;
            font-weight: bold;
            border: 10px double;
            padding: 10px;
            transform: rotate(-20deg);
            opacity: 0.10;
        }

        .stamp-success {
            color: #2e7d32;
            border-color: #2e7d32;
        }

        .stamp-failed {
            color: #d32f2f;
            border-color: #d32f2f;
        }

        table.items-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        table.items-table th {
            background: #f8f5f0;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            border-bottom: 1px solid #eee;
        }

        table.items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .total-section {
            margin-top: 30px;
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .gold-text {
            color: #bca47f;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="stamp-container">
            @if ($payment->status === 1)
                <div class="stamp stamp-success">SUCCESS</div>
            @else
                <div class="stamp stamp-failed">FAILED</div>
            @endif
        </div>

        <table class="layout-table header">
            <tr>
                <td style="width: 60%;">
                    <div class="hotel-name">{{ $payment->booking->hotel->name ?? 'CLASSIC LUXURY' }}</div>
                    <div style="font-size: 12px; color: #666;">
                        {{ $payment->booking->hotel->address ?? 'Luxury Avenue' }}<br>
                        @if (isset($payment->booking->hotel->city))
                            {{ $payment->booking->hotel->city->name }}
                        @endif
                    </div>
                </td>
                <td class="text-right" style="width: 40%;">
                    <h1 class="invoice-label">INVOICE</h1>
                    <div style="font-weight: bold;">#{{ $payment->booking->reference_number }}</div>
                </td>
            </tr>
        </table>

        <table class="layout-table" style="margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div style="font-size: 11px; color: #888; text-transform: uppercase;">Billed To:</div>
                    <div style="font-weight: bold;">{{ $payment->booking->user->name }}</div>
                    <div style="font-size: 12px;">{{ $payment->booking->user->email }}</div>
                </td>
                <td class="text-right" style="width: 50%; vertical-align: top;">
                    <div style="font-size: 11px; color: #888; text-transform: uppercase;">Date:</div>
                    <div style="font-weight: bold;">{{ $payment->created_at->format('d M, Y') }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Stay Dates</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payment->booking->items as $item)
                    <tr>
                        <td>
                            <strong>{{ strtoupper($item->room->details->category) }}</strong><br>
                            <small>Room: {{ $item->room->room_number }}</small>
                        </td>
                        <td style="text-align: center;">
                            {{ date('d M', strtotime($item->check_in)) }} -
                            {{ date('d M, Y', strtotime($item->check_out)) }}
                        </td>
                        <td class="text-right">
                            {{ number_format($item->price_at_booking * $payment->exchange_rate, 2) }}
                            {{ $payment->paid_currency }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="layout-table" style="margin-top: 20px;">
            <tr>
                <td style="width: 70%;"></td>
                <td style="width: 30%;">
                    <table class="layout-table">
                        <tr>
                            <td class="text-right" style="font-size: 12px; color: #666;">Subtotal:</td>
                            <td class="text-right" style="font-weight: bold;">
                                {{ number_format($payment->converted_amount, 2) }}</td>
                        </tr>
                        @if ($payment->booking->discount_amount > 0)
                            <tr>
                                <td class="text-right" style="font-size: 12px; color: #666;">Dsicount Applied :</td>
                                <td class="text-right" style="font-weight: bold;">
                                    -
                                    {{ number_format($payment->booking->discount_amount * $payment->exchange_rate, 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-right" style="font-weight: bold; padding-top: 10px;">TOTAL PAID:</td>
                            <td class="text-right gold-text" style="padding-top: 10px;">
                                {{ number_format($payment->converted_amount, 2) }} {{ $payment->paid_currency }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div
            style="margin-top: 60px; text-align: center; border-top: 1px solid #eee; padding-top: 20px; font-size: 10px; color: #999;">
            <p>COMPUTER GENERATED INVOICE - NO SIGNATURE REQUIRED</p>
        </div>
    </div>
</body>

</html>
