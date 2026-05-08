<x-mail::message>
# Hi {{ $userName }},

Thank you for taking the time to write a review for ** {{ $hotelName }} **
We are writing to inform you that your review has not been published to our platform. Our administration team
recently reviewed the content and found that it does not align with our community guidelines

<x-mail::panel>
**Details of the removed review:** **Rating:** {{ $review->rating }} / 5 Stars
@if ($review->comment)
**Comment:** "{{ $review->comment }}"
@endif
</x-mail::panel>

Reviews are typically withheld if they contain inappropriate language, spam, promotional material, or content
completely unrelated to the hotel experience.

Maintaining a safe and helpful environment for all travelers is our top priority. If you believe this action was
taken in error, or if you would like to submit a revised review that meets our guidelines, please feel free to reach
out to our support team.

Thanks,
{{ config('app.name') }}
</x-mail::message>
