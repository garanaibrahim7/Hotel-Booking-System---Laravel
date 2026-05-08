@extends('client.layouts.template')

@section('title', 'Booking Details')

@section('content')
    @php
        // $completed = \Carbon\Carbon::parse($payment->booking->leaved)->isPast();
        $completed = \Carbon\Carbon::parse($payment->booking->items->first()->check_out)->isPast();
    @endphp
    <div class="container py-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 0;">

                    <div class="card-header bg-dark py-4 px-5 d-flex align-items-center justify-content-between"
                        style="border-bottom: 4px solid #bca47f;">
                        <div>
                            <h4 class="text-white text-uppercase fw-bold mb-0" style="letter-spacing: 2px;">Booking Archive
                            </h4>
                            <p class="text-light mb-0 small opacity-75">Reference: #{{ $payment->booking->reference_number }}
                            </p>
                        </div>
                        <div class="text-end">
                            @switch($payment->booking->status)
                                @case(0)
                                    <span class="badge px-3 py-2 text-uppercase bg-warning text-muted" style="border-radius: 0;">
                                        Pending
                                    </span>
                                @break

                                @case(1)
                                    <span class="badge px-3 py-2 text-uppercase bg-success" style="border-radius: 0;">
                                        {{ $completed ? 'Stay Complete' : 'Confirmed' }}
                                    </span>
                                @break

                                @case(2)
                                    <span class="badge px-3 py-2 text-uppercase bg-danger" style="border-radius: 0;">
                                        Failed/Cancelled
                                    </span>
                                @break

                                @case(3)
                                    <span class="badge px-3 py-2 text-uppercase bg-warning text-muted" style="border-radius: 0;">
                                        Processing
                                    </span>
                                @break

                                @case(5)
                                    <span class="badge px-3 py-2 text-uppercase bg-danger" style="border-radius: 0;">
                                        Rejected
                                    </span>
                                @break

                                @default
                                    <span class="badge px-3 py-2 text-uppercase bg-danger" style="border-radius: 0;">
                                        Cancelled
                                    </span>
                                @break
                            @endswitch
                            {{-- <span class="badge px-3 py-2 text-uppercase"
                                style="background-color: #bca47f; color: #fff; border-radius: 0;">
                                {{ $payment->status == 1 ? 'Completed' : 'Cancelled/Failed' }}
                            </span> --}}
                        </div>
                    </div>

                    <div class="card-body p-5 bg-white">
                        <div class="row mb-5 pb-4 border-bottom">
                            <div class="col-sm-6">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">Property Details</h6>
                                <h5 class="fw-bold text-uppercase mb-1">{{ $payment->booking->hotel->name }}</h5>
                                <p class="text-muted small mb-0">{{ $payment->booking->hotel->address }}</p>
                                <p class="text-muted small">{{ $payment->booking->hotel->pincode }}</p>
                            </div>
                            <div class="col-sm-6 text-sm-end mt-4 mt-sm-0">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">Guest Information</h6>
                                <p class="mb-0 fw-bold">{{ $payment->booking->user->name }}</p>
                                <p class="text-muted mb-0">{{ $payment->booking->user->email }}</p>
                                <p class="text-muted">{{ $payment->booking->user->mobile }}</p>
                            </div>
                        </div>

                        <div class="table-responsive mb-5">
                            <table class="table border">
                                <thead class="bg-light">
                                    <tr class="text-uppercase small fw-bold">
                                        <th class="ps-4">Room & Stay Duration</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $nights =
                                            \Carbon\Carbon::parse(
                                                $payment->booking->items->first()->check_in,
                                            )->diffInDays($payment->booking->items->first()->check_out) ?:
                                            1;
                                        $calculatedSubtotal = 0;
                                    @endphp

                                    @foreach ($payment->booking->items as $item)
                                        @php
                                            $itemTotal = $item->price_at_booking * $payment->exchange_rate * $nights;
                                            $calculatedSubtotal += $itemTotal;
                                        @endphp
                                        <tr class="align-middle">
                                            <td class="ps-4 py-4">
                                                <h6 class="fw-bold mb-1 text-uppercase">
                                                    {{ $item->room->details->category }}
                                                    {{ $item->room->details->type ?? '' }}
                                                </h6>
                                                <p class="small text-muted mb-0">
                                                    Room: {{ $item->room->room_number }} |
                                                    {{ $nights }} {{ Str::plural('Night', $nights) }}
                                                </p>
                                                <p class="extra-small text-muted mb-0" style="font-size: 0.75rem;">
                                                    {{ \Carbon\Carbon::parse($item->check_in)->format('d M, Y') }} —
                                                    {{ \Carbon\Carbon::parse($item->check_out)->format('d M, Y') }}
                                                </p>
                                            </td>
                                            <td class="text-center fw-bold">
                                                {{ number_format($itemTotal, 2) }}
                                                {{ strtoupper($payment->paid_currency) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr class="border-top">
                                        <td class="ps-4 py-2 text-end text-uppercase small fw-bold">Subtotal</td>
                                        <td class="text-center py-2 fw-bold small">
                                            {{ number_format($calculatedSubtotal, 2) }}
                                            {{ strtoupper($payment->paid_currency) }}
                                        </td>
                                    </tr>

                                    @php
                                        $savings = $calculatedSubtotal - $payment->converted_amount;
                                    @endphp

                                    @if ($savings > 0.5)
                                        <tr class="text-success">
                                            <td class="ps-4 py-2 text-end text-uppercase small fw-bold">
                                                Discount Applied
                                            </td>
                                            <td class="text-center py-2 fw-bold small">
                                                - {{ number_format($savings, 2) }}
                                                {{ strtoupper($payment->paid_currency) }}
                                            </td>
                                        </tr>
                                    @endif

                                    <tr class="bg-light">
                                        <td class="ps-4 py-3 text-end fw-bold text-uppercase small">Total</td>
                                        <td class="text-center py-3 fw-bold fs-5" style="color: #bca47f;">
                                            {{ number_format($payment->converted_amount, 2) }}
                                            {{ strtoupper($payment->paid_currency) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap gap-2 no-print">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-dark px-4 py-3 fw-bold flex-grow-1">
                                <i class="bi bi-arrow-left me-2"></i> BACK TO BOOKINGS
                            </a>
                            @if (!$completed && $payment->booking->status == 1)
                                <a href="{{ route('booking.cancel', $payment->booking->reference_number) }}"
                                    target="_blank" class="btn btn-brand px-4 py-3 fw-bold flex-grow-1"
                                    style="background-color: rgb(187, 67, 67)">
                                    <i class="bi bi-printer me-2"></i> Request for Cancellation
                                </a>
                            @endif
                            @if (!$completed && $payment->booking->status == 5)
                                <a href="#" target="_blank" class="btn btn-brand px-4 py-3 fw-bold flex-grow-1"
                                    style="background-color: rgb(187, 67, 67)">
                                    <i class="bi bi-printer me-2"></i> Request for Refund
                                </a>
                            @endif
                            @if ($completed && $payment->status == 1)
                                <a href="{{ route('booking.download_invoice', $payment->booking->reference_number) }}"
                                    target="_blank" class="btn btn-brand px-4 py-3 fw-bold flex-grow-1">
                                    <i class="bi bi-printer me-2"></i> DOWNLOAD INVOICE
                                </a>
                            @endif
                        </div>


                        @if ($payment->booking->status == 5)
                            <p class="text-center mt-3 small text-danger">
                                You will get Your Refund in 5-7 Working Days
                            </p>
                        @endif

                        @if ($completed && $payment->status == 1)
                            <div class="text-center mt-5 pt-5 border-top">
                                @if ($payment->booking->review)
                                    <h4 style="letter-spacing: 2px;" class="mb-3">YOUR FEEDBACK</h4>

                                    <div class="d-inline-block p-4 rounded-4 shadow-sm bg-light mb-4">
                                        <div class="text-warning fs-3 mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="bi bi-star{{ $i <= $payment->booking->review->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>

                                        @if ($payment->booking->review->comment)
                                            <p class="fst-italic text-dark px-md-5 mb-3" style="max-width: 500px;">
                                                "{{ $payment->booking->review->comment }}"
                                            </p>
                                        @endif

                                        <div class="d-flex justify-content-center gap-3 mt-3 border-top pt-3">
                                            <div class="small text-muted">
                                                Food <span
                                                    class="fw-bold text-dark">{{ $payment->booking->review->food }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                Services <span
                                                    class="fw-bold text-dark">{{ $payment->booking->review->services }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                Hospitality <span
                                                    class="fw-bold text-dark">{{ $payment->booking->review->hospitality }}</span>
                                            </div>
                                            <div class="small text-muted">
                                                Cleanliness <span
                                                    class="fw-bold text-dark">{{ $payment->booking->review->cleaning }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="small text-muted mt-2">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        Review submitted on {{ $payment->booking->review->created_at->format('d M, Y') }}
                                        <a class="fs-5 ms-1" style="cursor: pointer;" title="Remove Review"
                                            onclick="confirmAction('{{ route('client.remove-review', $payment->booking->review->id) }}', 'Delete Review?', 'Are you sure you want to permanently remove your feedback?', 'btn-danger')">
                                            <i class="bi bi-trash text-danger"></i>
                                        </a>
                                    </p>
                                @else
                                    <h4 style="letter-spacing: 2px;">SHARE YOUR FEEDBACK</h4>
                                    <p class="text-muted">Rate your stay at {{ $payment->booking->hotel->name }}</p>
                                    <div class="star-submit-container">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <a href="{{ route('client.make-review', [$payment->booking->reference_number, $i]) }}"
                                                class="star-submit-btn">
                                                <i class="bi bi-star"></i>
                                            </a>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-brand {
            background-color: #bca47f;
            color: white;
            border: none;
            border-radius: 0;
            transition: 0.3s;
        }

        .btn-brand:hover {
            background-color: #1a1a1a;
            color: #bca47f;
        }

        .btn-outline-dark {
            border-radius: 0;
        }

        .star-submit-container {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }

        .star-submit-btn {
            background: none;
            border: none;
            padding: 0 5px;
            cursor: pointer;
            font-size: 2.5rem;
            color: #ddd;
            transition: 0.3s;
        }

        .star-submit-btn:hover,
        .star-submit-btn:hover~.star-submit-btn {
            color: #bca47f;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush
