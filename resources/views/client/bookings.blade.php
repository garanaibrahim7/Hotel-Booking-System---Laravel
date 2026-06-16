@extends('client.layouts.template')

@section('title', 'My Bookings')

@section('content')
    <div class="container py-5 my-5">
        <div class="row">
            <div class="col-12">
                <h2 class="fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">Your Bookings</h2>
                <div style="width: 60px; height: 3px; background: #bca47f;" class="mb-5"></div>

                <div class="card border-0 shadow-sm" style="border-radius: 0;">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead style="background-color: #f8f5f0;">
                                <tr class="text-uppercase small fw-bold" style="letter-spacing: 1px;">
                                    <th class="ps-4 py-3">Booking Details</th>
                                    <th class="py-3">Room Type</th>
                                    <th class="py-3">Amount</th>
                                    <th class="py-3">Status</th>
                                    <th class="pe-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr class="border-bottom">
                                        <td class="ps-4 py-4">
                                            <div class="fw-bold text-dark">#{{ $booking->reference_number }}</div>
                                            <small class="text-muted">{{ $booking->created_at->format('d M, Y') }}</small>
                                        </td>
                                        <td class="py-4">
                                            <div class="fw-semibold">
                                                {{ $booking->items->groupBy(function ($item) {
                                                        return $item->room->details?->title;
                                                    })->map(function ($group, $title) {
                                                        return $title . ' x ' . $group->count();
                                                    })->implode(', ') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($booking->items->first()->check_in)->format('d M') }}
                                                -
                                                {{ \Carbon\Carbon::parse($booking->items->first()->check_out)->format('d M') }}
                                            </small>
                                        </td>
                                        <td class="py-4 fw-bold">
                                            {{ number_format($booking->payment->converted_amount, 2) }} <span
                                                class="small text-muted">{{ $booking->payment->paid_currency }}</span>
                                        </td>
                                        <td class="py-4">
                                            @if ($booking->status == 1)
                                                @php
                                                    $completed = \Carbon\Carbon::parse(
                                                        $booking->items->first()->check_out,
                                                    )->isPast();
                                                @endphp
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 text-uppercase fw-bold"
                                                    style="border-radius: 0; font-size: 10px;">{{ $completed ? 'Stay Complete' : 'Confirmed' }}</span>
                                            @elseif($booking->status == 0)
                                                <span
                                                    class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 text-uppercase fw-bold"
                                                    style="border-radius: 0; font-size: 10px;">Pending</span>
                                            @elseif($booking->status == 3)
                                                <span
                                                    class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 text-uppercase fw-bold"
                                                    style="border-radius: 0; font-size: 10px;">Processing</span>
                                            @else
                                                @if ($booking->refund && $booking->refund->status == 1)
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 text-uppercase fw-bold"
                                                        style="border-radius: 0; font-size: 10px;">Refunded</span>
                                                @elseif ($booking->refund && $booking->refund->status == 0)
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 text-uppercase fw-bold"
                                                        style="border-radius: 0; font-size: 10px;">Refund Initialized</span>
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 text-uppercase fw-bold"
                                                        style="border-radius: 0; font-size: 10px;">Cancelled</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="pe-4 py-4 text-center">
                                            <div class="btn-group w-50">
                                                <a href="{{ route('booking.view', ['referenceNumber' => $booking->reference_number ?? 'none']) }}"
                                                    class="btn btn-sm btn-outline-dark rounded-0 px-3">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if ($booking->status == 1)
                                                    <a href="{{ route('booking.print_invoice', $booking->reference_number) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-dark rounded-0 px-3 border-start-0">
                                                        <i class="bi bi-printer"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-calendar-x d-block mb-3 fs-1 text-muted"></i>
                                            <p class="text-muted">No reservations found yet.</p>
                                            <a href="{{ route('client.rooms') }}" class="btn btn-brand btn-sm px-4">Book
                                                Now</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-brand {
            background-color: #bca47f;
            color: white;
            border-radius: 0;
        }

        .btn-brand:hover {
            background-color: #1a1a1a;
            color: #bca47f;
        }

        .table thead th {
            border-bottom: none;
        }

        .btn-outline-dark:hover {
            background-color: #bca47f;
            border-color: #bca47f;
            color: white;
        }

        /* Pagination Styling for Luxury Theme */
        .pagination .page-item.active .page-link {
            background-color: #bca47f;
            border-color: #bca47f;
        }

        .pagination .page-link {
            color: #1a1a1a;
            border-radius: 0;
        }
    </style>
@endsection
