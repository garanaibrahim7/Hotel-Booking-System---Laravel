@component('mail::message')
# 🚨 Alert: Payment Failure

An automated system check has detected a failed payment attempt. Please review the details below to ensure there are no issues with the payment gateway or guest experience.

@component('mail::panel')
**Payment ID:** #{{ $payment->id }}
**Reference:** {{ $payment->booking->reference_number ?? 'N/A' }}
**Amount:** {{ $payment->converted_amount }} {{ $payment->paid_currency }}
**Guest:** {{ $payment->booking->user->name ?? 'Unknown' }}
@endcomponent

### 🛠️ Suggested Action
Check the payment logs in the dashboard to see if this was a user cancellation or a gateway rejection.

@component('mail::button', ['url' => route('admin.bookings.index', ['search' => $payment->booking->reference_number]), 'color' => 'error'])
View Booking Details
@endcomponent

*This is an automated alert sent to the system administrator.*

Thanks,
{{ config('app.name') }} System Bot
@endcomponent
