<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #bca47f;
        }

        .content {
            padding: 30px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        h1 {
            color: #1a1a1a;
            margin-top: 0;
        }

        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            color: #bca47f;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            text-align: left;
            font-size: 13px;
            color: #777;
            border-bottom: 2px solid #eee;
            padding: 10px 0;
        }

        td {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-top: 10px;
            border-top: 2px solid #eee;
            padding-top: 10px;
        }

        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #1a1a1a;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 5px;
        }

        .instruction-box {
            background-color: #fff9e6;
            border-left: 4px solid #ffcc00;
            padding: 15px;
            margin: 20px 0;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src='{{ env('APP_LOGO') }}' width='120' alt='Logo'>
        </div>

        <div class="content">
            <h1>Booking Confirmed!</h1>
            <p>Hello <strong>{{ $booking->user->name }}</strong>,</p>
            <p>Your stay at <strong>{{ $booking->hotel->name }}</strong> is officially booked. We look forward to
                welcoming you!</p>

            <div class="section-title">📋 Stay Information</div>
            <table style="margin-bottom: 30px;">
                <tr>
                    <td style="border:none;"><strong>Check-in:</strong>
                        {{ \Carbon\Carbon::parse($booking->items->first()->check_in)->format('D, M d, Y') }} (12:00 PM)
                    </td>
                </tr>
                <tr>
                    <td style="border:none;"><strong>Check-out:</strong>
                        {{ \Carbon\Carbon::parse($booking->items->first()->check_out)->format('D, M d, Y') }} (10:00 AM)
                    </td>
                </tr>
                <tr>
                    <td style="border:none;"><strong>Reference:</strong> <code>{{ $booking->reference_number }}</code>
                    </td>
                </tr>
            </table>

            <div class="section-title">🛏️ Rooms Selected</div>
            <table>
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Room No.</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($booking->items as $item)
                        <tr>
                            <td>{{ $item->room->details->title }}</td>
                            <td>{{ $item->room->room_number }}</td>
                            <td style="text-align: right;">{{ number_format($item->price_at_booking, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="section-title">💰 Payment Summary</div>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>{{ number_format($booking->sub_amount, 2) }} {{ $booking->payment->currency ?? 'INR' }}</span>
            </div>
            @if ($booking->discount_amount > 0)
                <div class="summary-row" style="color: #d9534f;">
                    <span>Discount Applied:</span>
                    <span>-{{ number_format($booking->discount_amount, 2) }}
                        {{ $booking->payment->currency ?? 'INR' }}</span>
                </div>
            @endif
            <div class="summary-row total">
                <span>Total Paid:</span>
                <span>{{ number_format($booking->total_amount, 2) }} {{ $booking->payment->currency ?? 'INR' }}</span>
            </div>
            <p style="font-size: 12px; color: #999;">Payment Date:
                {{ $booking->payment->updated_at->format('M d, Y h:i A') }}</p>

            @if ($booking->instructions)
                <div class="instruction-box">
                    <strong>Special Instructions:</strong> {{ $booking->instructions }}
                </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('booking.view', $booking->reference_number) }}" class="button text-light">View
                    My Booking</a>
                <a href="{{ route('booking.download_invoice', $booking->reference_number) }}" class="button text-light"
                    style="background-color: #666;">Download Invoice</a>
            </div>
        </div>

        <div class="footer">
            <p>Thanks for choosing us!</p>
            <strong>{{ config('app.name') }} Team</strong>
        </div>
    </div>
</body>

</html>
