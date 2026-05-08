@component('mail::message')
# 📊 Daily Booking Summary

Hello Admin,

Please find the attached PDF report containing all booking activities from the last 24 hours.

**Report Summary:**
- **Period:** {{ now()->subDay()->format('d M, Y H:i') }} to {{ now()->format('d M, Y H:i') }}
- **Generated At:** {{ now()->format('H:i A') }}

Check the attachment for detailed guest information, payment statuses, and hotel breakdowns.

Thanks,
{{ config('app.name') }} Automation
@endcomponent
