<x-mail::message>
{{-- Header Graphic/Line --}}
<div style="width: 100%; height: 2px; background-color: #bca47f; margin-bottom: 30px;"></div>

{{-- Greeting --}}
@if (! empty($greeting))
<h1 style="color: #1a1a1a; text-transform: uppercase; letter-spacing: 2px; font-size: 24px; text-align: center;">{{ $greeting }}</h1>
@else
@if ($level === 'error')
<h1 style="color: #dc3545; text-transform: uppercase; letter-spacing: 2px; text-align: center;">@lang('Whoops!')</h1>
@else
<h1 style="color: #1a1a1a; text-transform: uppercase; letter-spacing: 2px; font-size: 24px; text-align: center;">@lang('Hello!')</h1>
@endif
@endif

<div style="text-align: center; color: #555; font-size: 16px; line-height: 1.6; margin-top: 20px;">
{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach
</div>

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success' => 'success',
        'error' => 'error',
        default => 'primary',
    };
?>
<div style="text-align: center; margin: 40px 0;">
<x-mail::button :url="$actionUrl" :color="$color">
{{ strtoupper($actionText) }}
</x-mail::button>
</div>
@endisset

{{-- Outro Lines --}}
<div style="text-align: center; color: #555; font-size: 15px; margin-bottom: 30px;">
@foreach ($outroLines as $line)
{{ $line }}
@endforeach
</div>

{{-- Salutation --}}
<div style="text-align: center; border-top: 1px solid #eee; pt-4; padding-top: 20px;">
@if (! empty($salutation))
<span style="font-style: italic; color: #888;">{{ $salutation }}</span>
@else
<span style="font-style: italic; color: #888;">@lang('Warm Regards,')</span><br>
<strong style="color: #1a1a1a; text-transform: uppercase; letter-spacing: 1px;">{{ config('app.name') }} Concierge</strong>
@endif
</div>

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
<div style="font-size: 11px; color: #aaa; text-align: center;">
@lang("If you're having trouble clicking the \":actionText\" button, please use the link below:", ['actionText' => $actionText])
<br>
<a href="{{ $actionUrl }}" style="color: #bca47f;">{{ $displayableActionUrl }}</a>
</div>
</x-slot:subcopy>
@endisset
</x-mail::message>