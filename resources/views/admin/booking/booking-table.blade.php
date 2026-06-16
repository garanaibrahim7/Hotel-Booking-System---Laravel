@php
    // We expect a variable called $bookings
    $hasBookings = isset($bookings) && $bookings->isNotEmpty();
@endphp

@if ($hasBookings)
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 border-0 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Guest &
                        ID</th>
                    <th class="border-0 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Booking Date
                    </th>
                    <th class="border-0 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Booking
                        Details
                    </th>
                    <th class="border-0 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Stay Dates
                    </th>
                    <th class="border-0 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Duration
                    </th>
                    <th class="border-0 text-uppercase small fw-bold text-muted text-center" style="font-size: 0.7rem;">
                        Status</th>
                    <th class="border-0 text-uppercase small fw-bold text-muted text-end pe-4"
                        style="font-size: 0.7rem;">Total Paid</th>
                    <th class="border-0 text-uppercase small fw-bold text-muted text-end pe-4"
                        style="font-size: 0.7rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $item)
                    <tr>
                        {{-- Guest Info --}}
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                {{-- <div class="bg-soft-dark rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 38px; height: 38px; background-color: #f0f2f5; color: #444;">
                                    <i class="bi bi-person"></i>
                                </div> --}}
                                <div>
                                    <div class="fw-bold text-dark small">
                                        {{ $item->booking->guest_name ?? 'Guest User' }}</div>
                                    <div class="text-muted extra-small" style="font-size: 0.7rem;">
                                        #{{ $item->booking->reference_number }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="extra-small fw-semibold text-dark">
                                    <i class="bi bi-calendar-plus me-1 text-dark"></i>
                                    {{ \Carbon\Carbon::parse($item->booking->created_at)->format('d M, Y') }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="fw-bold text-dark small">
                                    {{ $room->details->title ?? ($item->room->details->title ?? 'Unknown') }} Room</div>
                                <div class="text-muted extra-small" style="font-size: 0.7rem;">
                                    at {{ $room->hotel->name ?? ($item->room->hotel->name ?? 'Unknown') }}</div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="extra-small fw-semibold text-dark">
                                    <i class="bi bi-calendar-plus me-1 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($item->check_in)->format('d M, Y') }}
                                </span>
                                <span class="extra-small fw-semibold text-dark">
                                    <i class="bi bi-calendar-minus me-1 text-danger"></i>
                                    {{ \Carbon\Carbon::parse($item->check_out)->format('d M, Y') }}
                                </span>
                            </div>
                        </td>

                        {{-- Nights --}}
                        <td>
                            <span class="badge bg-light text-dark border-0 fw-normal">
                                {{ \Carbon\Carbon::parse($item->check_in)->diffInDays($item->check_out) }} Nights
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @php
                                $status = $item->booking->status;
                                switch ($status) {
                                    case 1:
                                        $color = '#e8fadf';
                                        $textColor = '#28a745';
                                        $label = 'Confirmed';
                                        break;

                                    case 0:
                                        $color = '#fff3cd';
                                        $textColor = '#856404';
                                        $label = 'Pending';
                                        break;

                                    case 2:
                                        $color = '#ffe5e5';
                                        $textColor = '#d9534f';
                                        $label = 'Failed';
                                        break;

                                    case 3:
                                        $color = '#e2f0ff';
                                        $textColor = '#0d6efd';
                                        $label = 'Processing';
                                        break;

                                    case 4:
                                        $color = '#f8d7da';
                                        $textColor = '#842029';
                                        $label = 'Cancelled';
                                        break;

                                    case 5:
                                        $color = '#f8d7da';
                                        $textColor = '#842029';
                                        $label = 'Rejected';
                                        break;

                                    default:
                                        $color = '#f0f0f0';
                                        $textColor = '#6c757d';
                                        $label = 'Unknown';
                                }
                            @endphp
                            <span class="badge rounded-pill px-3 py-2"
                                style="background-color: {{ $color }}; color: {{ $textColor }}; font-size: 0.7rem;">
                                {{ $label }}
                            </span>
                        </td>

                        {{-- Pricing --}}
                        <td class="pe-4 text-end">
                            <div class="fw-bold text-dark">{{ number_format($item->booking->total_amount, 2) }}
                                {{ $item->booking->currency }}</div>
                            <div class="text-muted" style="font-size: 0.65rem; text-transform: uppercase;">
                                via {{ $item->booking->payment->gateway ?? 'Cash' }}
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            @can('manager-access')
                                <a href="{{ route('manager.bookings.show', $item->booking_id) }}"
                                    class="btn btn-white btn-sm border-end" title="View Details">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                            @endcan
                            @can('admin-access')
                                <a href="{{ route('admin.bookings.show', $item->booking_id) }}"
                                    class="btn btn-white btn-sm border-end" title="View Details">
                                    <i class="bi bi-eye text-primary"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-calendar-x text-light display-1"></i>
        <p class="text-muted mt-3 mb-0">No booking records found for {{ request('bookingsOf') ?? 'Today' }}.</p>
    </div>
@endif
