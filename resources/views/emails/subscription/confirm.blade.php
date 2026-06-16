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
            margin-top: 25px;
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
            background-color: #f8f9fa;
            border-left: 4px solid #bca47f;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }

        .facility-list {
            margin: 0;
            padding-left: 20px;
            font-size: 14px;
        }

        .facility-list li {
            margin-bottom: 8px;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src='{{ env('APP_LOGO') }}' width='120' alt='Logo'>
        </div>

        <div class="content">
            <h1>Subscription Active!</h1>
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            <p>Your subscription to the <strong>{{ $plan->name }}</strong> is officially active. Welcome to premium!</p>

            <div class="section-title">📦 Subscription Details</div>
            <table style="margin-bottom: 10px;">
                <tr>
                    <td style="border:none; padding-bottom: 4px;"><strong>Plan Name:</strong> {{ $plan->name }}</td>
                </tr>
                <tr>
                    <td style="border:none; padding-bottom: 4px;"><strong>Billing Cycle:</strong> {{ ucfirst($plan->type) }}</td>
                </tr>
                <tr>
                    <td style="border:none; padding-bottom: 4px;"><strong>Status:</strong> <span style="color: #28a745; font-weight: bold;">Active</span></td>
                </tr>
            </table>

            <div class="section-title">✨ Included Facilities</div>
            @php
                // Handle JSON decoding gracefully inside Blade
                $facilities = is_array($plan->facilities) ? $plan->facilities : (json_decode($plan->facilities, true) ?? []);
            @endphp

            <ul class="facility-list">
                @forelse ($facilities as $facility)
                    <li>{{ $facility }}</li>
                @empty
                    <li>Full platform access</li>
                @endforelse
            </ul>

            <div class="section-title">💳 Billing & Payment Summary</div>
            <div class="summary-row">
                <span>Plan Amount:</span>
                <span>{{ number_format($history->amount, 2) }} {{ strtoupper($history->currency) }}</span>
            </div>

            <div class="summary-row total">
                <span>Total Paid Today:</span>
                <span>{{ number_format($history->amount, 2) }} {{ strtoupper($history->currency) }}</span>
            </div>

            <p style="font-size: 12px; color: #999;">Transaction Date: {{ $history->created_at->format('M d, Y h:i A') }}</p>

            <div class="instruction-box">
                <strong>Next Billing Date:</strong> Your plan will automatically renew on <strong>{{ \Carbon\Carbon::parse($subscription->renewal_on)->format('F d, Y') }}</strong> for {{ number_format($history->amount, 2) }} {{ strtoupper($history->currency) }}. You can cancel or change your plan at any time from your account dashboard.
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ env('FRONTEND_URL', 'http://localhost:5173') }}/account/subscription" class="button text-light">Manage Subscription</a>
            </div>
        </div>

        <div class="footer">
            <p>Thanks for choosing us!</p>
            <strong>{{ config('app.name') }} Team</strong>
        </div>
    </div>
</body>

</html>
