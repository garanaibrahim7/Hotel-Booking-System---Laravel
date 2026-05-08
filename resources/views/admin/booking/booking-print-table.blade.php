<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Reference</th>
            <th>Guest Details</th>
            <th>Hotel & Room</th>
            <th>Stay Dates</th>
            <th>Status</th>
            <th>Amount</th>
            <th>Amount in {{ $userCountry['currency_code'] }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($bookings as $index => $booking)
            <tr>
                <td>
                    {{ $index + 1 }}
                </td>
                <td class="fw-bold text-uppercase">
                    {{ $booking->reference_number }}</td>
                <td>
                    <strong>{{ $booking->guest_name }}</strong><br>
                    {{ $booking->guest_email }}
                </td>
                <td>
                    {{ $booking->hotel->name }}<br>
                    @php
                        $grouped = $booking->items->groupBy(fn($item) => $item->room->details->title);
                    @endphp

                    @foreach ($grouped as $title => $items)
                        <small class="text-muted d-block">
                            {{ $title }} × {{ $items->count() }}
                        </small>
                    @endforeach
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($booking->items->first()->check_in)->format('d M') }} -
                    {{ \Carbon\Carbon::parse($booking->items->first()->check_out)->format('d M, Y') }}
                </td>
                <td>{{ $booking->payment->status == 1 ? 'PAID' : 'PENDING' }}</td>
                <td class="fw-bold">
                    {{ number_format($booking->total_amount, 2) . ' ' . $booking->currency }}
                </td>
                <td class="fw-bold">
                    {{ number_format(convertCurrency($booking->total_amount, $userCountry['currency_code'], $booking->currency), 2) . ' ' . $userCountry['currency_code'] }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
