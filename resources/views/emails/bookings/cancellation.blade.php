@component('mail::message')
#
{{ $booking->status === 5 ? '🏨 Booking Rejected' : ($booking->status === 4 ? '🚫 Booking Cancelled' : '⚠️ Payment Failed') }}

Hello {{ $booking->user->name }},

@if ($booking->status === 5)
We are very sorry to inform you that your booking for **{{ $booking->hotel->name }}** has been rejected by the
property due to unexpected unavailability or administrative reasons. We apologize for this inconvenience.

@else
@if ($booking->status === 4)
This is to confirm that you have successfully cancelled your booking for **{{ $booking->hotel->name }}**. We
hope to see you again soon!

@else
We noticed that your booking for **{{ $booking->hotel->name }}** could not be completed. This was likely due to
a payment timeout or technical error.
@endif
@endif

---

### 📋 Booking Details
**Reference:** `{{ $booking->reference_number }}`
**Status:** {{ $booking->status == 2 ? 'Failed' : ($booking->status == 4 ? 'Cancelled' : 'Rejected') }}

---

@if ($booking->status === 5 || $booking->status === 2)
@component('mail::panel')
**Important Refund Info:** If any money was deducted from your account, please don't worry. Our system initiates
refunds immediately, and banks usually process them within **5-7 business days**.
@endcomponent
@endif

@if ($booking->status !== 4)
If you believe this is an error or need immediate assistance, please contact us:
**Support Email:** support@hotelbooking.com
**Helpdesk:** +91-XXXXXXXXXX
@endif

---

@if ($booking->status === 2 || $booking->status === 5)
### 🛏️ Want to try again?
@if ($booking->status === 2)
You can retry the payment for your existing selection from your dashboard.
@else
Please feel free to explore other available rooms or hotels.
@endif

@component('mail::button', [
'url' => url('/user/bookings'),
'color' => $booking->status === 5 ? 'primary' : 'error',
])
{{ $booking->status === 2 ? 'Retry Payment' : 'Browse Other Hotels' }}
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }} Support Team
@endcomponent
