@extends('client.layouts.template')

@section('title', 'Payment Status')

@section('content')
    @php
        // Mapping status to UI configurations
        $statusConfig = [
            0 => [
                'color' => '#e9c04e',
                'icon' => 'bi-shield-x',
                'title' => 'Payment Pending',
                'badge' => 'Pending',
            ],
            1 => [
                'color' => '#64de6f',
                'icon' => 'bi-shield-check',
                'title' => 'Payment Received',
                'badge' => 'Completed',
            ],
            2 => [
                'color' => '#fb4e4e',
                'icon' => 'bi-shield-slash',
                'title' => 'Payment Failed',
                'badge' => 'Failed',
            ],
            3 => [
                'color' => '#fa8f3d',
                'icon' => 'bi-shield-exclamation',
                'title' => 'Payment Processing',
                'badge' => 'Pending',
            ],
        ];
        $config = $statusConfig[$payment->status] ?? $statusConfig[0];
    @endphp

    <div class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg text-center" style="border-radius: 0;">

                    <div class="card-header bg-white py-5 border-0">
                        <div class="mb-4">
                            <i class="bi {{ $config['icon'] }}" style="font-size: 4rem; color: {{ $config['color'] }};"></i>
                        </div>
                        <h2 class="fw-bold text-uppercase mb-2" style="letter-spacing: 4px; color: #1a1a1a;">
                            {{ $config['title'] }}</h2>
                        <p class="text-muted text-uppercase small" style="letter-spacing: 2px;">Transaction Reference:
                            #{{ $payment->booking->reference_number }}</p>
                    </div>

                    <div class="card-body px-5 pb-5">
                        <div class="mb-5">

                            @if ($payment->status == 0)
                                <p class="lead text-secondary">Your transaction is Not Initialized yet</p>
                                <p class="text-muted small">We will update your booking status once the confirmation is
                                    received.</p>
                            @elseif ($payment->status == 1)
                                <p class="lead text-secondary">
                                    Dear {{ $payment->booking->user->name }}, your payment of
                                    <span class="fw-bold text-dark">{{ number_format($payment->converted_amount, 2) }}
                                        {{ strtoupper($payment->paid_currency) }}</span>
                                    has been successfully processed.
                                </p>
                                <p class="text-muted small">A formal invoice has been mailed to
                                    <strong>{{ $payment->booking->user->email }}</strong>.
                                </p>
                            @elseif($payment->status == 3)
                                <p class="lead text-secondary">Your transaction is currently being processed by
                                    {{ $payment->gateway }}.</p>
                                <p class="text-muted small">We will update your booking status once the confirmation is
                                    received.</p>
                            @else
                                <p class="lead text-danger">The transaction could not be completed.</p>
                                <p class="text-muted small">If any amount was deducted, it will be refunded within 5-7
                                    working days.</p>
                            @endif
                        </div>

                        <div class="p-4 mb-5" style="background-color: #f8f9fa; border: 1px solid #eee;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-uppercase small fw-bold text-muted">Amount</span>
                                <span class="fw-bold">{{ number_format($payment->converted_amount, 2) }}
                                    {{ strtoupper($payment->paid_currency) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-uppercase small fw-bold text-muted">Date</span>
                                <span>{{ $payment->updated_at->format('d M, Y | h:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-uppercase small fw-bold text-muted">Status</span>
                                <span class="fw-bold text-uppercase small"
                                    style="color: {{ $config['color'] }};">{{ $config['badge'] }}</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            @if ($payment->status == 1)
                                <div class="col-sm-12">
                                    <a href="{{ route('client.home') }}" style="background-color: #bca47f; color:#fff"
                                        class="btn btn-brand w-100 py-3 fw-bold text-uppercase small">
                                        Dashboard
                                    </a>
                                </div>
                            @else
                                <div class="col-6">
                                    <a href="{{ route('client.home') }}"
                                        class="btn btn-dark w-100 py-3 fw-bold text-uppercase small">
                                        Return to Home
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('booking.stay.summary') }}"
                                        class="btn btn-dark w-100 py-3 fw-bold text-uppercase small">
                                        Try Again
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
