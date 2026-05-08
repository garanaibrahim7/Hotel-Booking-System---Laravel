@extends('layouts.adminlte')

@section('title', 'Manage Rooms')

@section('content')
    <div class="container-fluid py-4">

        @if ($rooms->count() > 0)
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="row g-0">
                    <div class="col-md-3">
                        <img src="/storage/{{ $details->images->first()->path }}" class="img-fluid h-100"
                            style="object-fit: cover; min-height: 150px;" alt="Room Category">
                    </div>
                    <div class="col-md-9">
                        <div class="card-body d-flex justify-content-between align-items-center h-100 px-4">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">{{ $details->title }}</h4>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $details->hotel->name }},
                                    {{ $details->hotel->city->name }}
                                </p>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-light text-dark border py-2 px-3 fs-6"
                                        style="border-radius: 8px;">
                                        <span class="text-muted fw-normal">Base:</span> {{ $details->price }}
                                    </span>
                                    <span class="badge bg-soft-primary text-primary py-2 px-3 fs-6"
                                        style="border-radius: 8px; background-color: #e7f1ff;">
                                        <span class=" fw-normal">Local:</span>
                                        {{ $details->hotel->currency_symbol }}{{ $details->converted_price }}
                                    </span>
                                </div>
                            </div>
                            <a class="btn btn-dark px-4 py-2" style="border-radius: 10px;"
                                href="{{ route('admin.rooms.create', ['category' => $details->id]) }}">
                                <i class="bi bi-plus-lg me-2"></i> Add Room Unit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rooms Table --}}
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 text-uppercase small fw-bold text-muted" style="width: 80px">#
                                    </th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted">Room Number</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted">Today Availability</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted">Technical Status</th>
                                    <th class="border-0 text-uppercase small fw-bold text-muted text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rooms as $key => $room)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-dark fs-5">#{{ $room->room_number }}</span>
                                        </td>
                                        <td>
                                            @if ($room->is_booked_today)
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background-color: #ffe5e5; color: #d9534f;">
                                                    <i class="bi bi-door-closed-fill me-1"></i> Occupied
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-3 py-2"
                                                    style="background-color: #e8fadf; color: #28a745;">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Vacant
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href='{{ route('admin.room.change-status', $room->id) }}'
                                                class="text-decoration-none badge rounded-pill px-3 py-2 {{ $room->status == 1 ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}"
                                                style="background-color: {{ $room->status == 1 ? '#e8fadf' : '#f0f0f0' }};">
                                                <i class="bi bi-circle-fill me-1"
                                                    style="font-size: 0.5rem; vertical-align: middle;"></i>
                                                {{ $room->status == 1 ? 'Active' : 'Under Maintenance' }}
                                            </a>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                {{-- <a href="{{ route('admin.rooms.edit', $room->id) }}"
                                                    class="btn btn-white btn-sm border-end">
                                                    <i class="bi bi-pencil-square text-warning"></i>
                                                </a> --}}


                                                {{-- <a href="{{ route('admin.room.bookings', $room->id) }}"
                                                    class="btn btn-white btn-sm border-end text-success">
                                                    <i class="bi bi-file-earmark-plus"></i> Book Now
                                                </a> --}}

                                                {{-- <button class="btn btn-white btn-sm border-end"
                                                    onclick="openBookingModal({{ $room->load('details') }})"
                                                    title="Direct Booking">
                                                    <i class="bi bi-plus-circle-dotted text-success"></i>
                                                </button> --}}

                                                <a href="{{ route('admin.room.bookings', $room->id) }}"
                                                    class="btn btn-white btn-sm border-end text-primary">
                                                    <i class="bi bi-eye"></i> View Bookings
                                                </a>
                                                <button class="btn btn-white btn-sm text-danger"
                                                    onclick="deleteAlert({{ $room }})">
                                                    <i class="bi bi-trash3"></i> Remove
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-0">
                    {{-- {{ $rooms->links() }} --}}
                </div>
            </div>
            <input type="hidden" id="filter_room_detail_id" value="{{ $room->room_detail_id ?? '' }}"> <input
                type="hidden" id="filter_room_id" value="">
            @include('admin.partials.calender-view')
        @else
            <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 15px;">
                <i class="bi bi-door-closed text-light" style="font-size: 5rem;"></i>
                <h3 class="mt-3 fw-bold">No Rooms Registered</h3>
                <p class="text-muted">You haven't added individual room units for this category yet.</p>
                <a class="btn btn-dark px-5 py-2 mt-2" style="border-radius: 10px;"
                    href="{{ route('admin.rooms.create') }}">
                    Add First Room
                </a>
            </div>
        @endif
    </div>

    {{-- Standard Luxury Modal --}}
    <x-alert-model id="staticBackdrop" title="Remove Room Unit">
        <div class="text-center p-3">
            <i class="bi bi-exclamation-octagon text-danger display-4 mb-3"></i>
            <p class="mb-1">Are you sure you want to delete Room <span id="roomNumber" class="fw-bold"></span>?</p>
            <p class="small text-muted">This will remove this specific unit from the inventory.</p>
        </div>
        <x-slot:action>
            <form id="deleteRoomForm" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger px-4" style="border-radius: 8px;">Delete Room</button>
            </form>
        </x-slot>
    </x-alert-model>



@endsection

@push('scripts')
    <script>
        function deleteAlert(room) {
            document.getElementById('roomNumber').innerHTML = room.room_number;
            document.getElementById('deleteRoomForm').action = `/admin/rooms/${room.id}`;
            let modal = new bootstrap.Modal(document.getElementById('staticBackdrop'))
            modal.show()
        }

        // Auto-hide alerts
        setTimeout(() => {
            const alert = document.getElementById('flashAlert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 3000);
    </script>
@endpush

@push('styles')
    <style>
        .bg-soft-primary {
            background-color: #e7f1ff;
            color: #0d6efd;
        }

        .btn-white {
            background: #fff;
            border: 1px solid #f0f0f0;
        }

        .btn-white:hover {
            background: #f8f9fa;
        }

        .table thead th {
            letter-spacing: 0.05em;
            font-size: 0.75rem;
        }

        .badge {
            font-weight: 600;
        }
    </style>
@endpush



{{-- <div class="modal fade" id="directBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Direct Booking: Room #<span id="display_room_number"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.bookings.book') }}" method="POST">
                @csrf
                <input type="hidden" name="room_id" id="modal_room_id">

                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-uppercase text-muted">Check-In</label>
                            <input type="date" name="check_in" id="book_check_in"
                                class="form-control bg-light border-0" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-uppercase text-muted">Check-Out</label>
                            <input type="date" name="check_out" id="book_check_out"
                                class="form-control bg-light border-0"
                                value="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-bold text-uppercase text-muted">Guest Name</label>
                            <input type="text" name="name" class="form-control bg-light border-0"
                                placeholder="Full Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-uppercase text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-light border-0"
                                placeholder="+1..." required>
                        </div>
                        <div class="col-md-12">
                            <label class="small fw-bold text-uppercase text-muted">Email Address (Optional)</label>
                            <input type="email" name="email" class="form-control bg-light border-0"
                                placeholder="guest@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="small fw-bold text-uppercase text-muted">Payment Method</label>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach (['cash', 'card', 'upi', 'stripe'] as $method)
                                    <input type="radio" class="btn-check" name="payment_method"
                                        id="pay_{{ $method }}" value="{{ $method }}"
                                        {{ $method == 'cash' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-dark border-0 bg-light px-3 py-2 small"
                                        for="pay_{{ $method }}">
                                        {{ strtoupper($method) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6 text-end d-flex flex-column justify-content-end">
                            <h6 class="text-muted small text-uppercase mb-1">Total Stay Price</h6>
                            <h3 class="fw-bold text-primary mb-0">₹<span id="display_total_price">0</span></h3>
                            <input type="hidden" name="total_price" id="input_total_price">
                        </div>

                        <div class="col-md-12">
                            <label class="small fw-bold text-uppercase text-muted">Special Instructions</label>
                            <textarea name="instructions" class="form-control bg-light border-0" rows="2"
                                placeholder="e.g. Late check-in, extra bed..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold">
                        <i class="bi bi-calendar-check me-2"></i> CONFIRM WALK-IN BOOKING
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openBookingModal(room) {
        // 1. Set Room Data
        document.getElementById('modal_room_id').value = room.id;
        document.getElementById('display_room_number').innerText = room.room_number;

        // 2. Base Price from Room Detail (Category)
        const dailyPrice = room.details.price;

        const calculatePrice = () => {
            const start = new Date(document.getElementById('book_check_in').value);
            const end = new Date(document.getElementById('book_check_out').value);

            if (end > start) {
                const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                const total = nights * dailyPrice;
                document.getElementById('display_total_price').innerText = total.toLocaleString();
                document.getElementById('input_total_price').value = total;
            }
        };

        // Initial calculation
        calculatePrice();

        // Listen for date changes
        document.getElementById('book_check_in').addEventListener('change', calculatePrice);
        document.getElementById('book_check_out').addEventListener('change', calculatePrice);

        // 3. Show Modal
        let modal = new bootstrap.Modal(document.getElementById('directBookingModal'));
        modal.show();
    }
</script> --}}
