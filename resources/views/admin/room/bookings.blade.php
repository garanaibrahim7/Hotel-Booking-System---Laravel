@extends('layouts.adminlte')

@section('title', 'Room Booking History')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header Info Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white rounded-3 d-flex align-items-center justify-content-center me-4"
                        style="width: 70px; height: 70px;">
                        <i class="bi bi-door-open fs-2"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">Room #{{ $room->room_number }}</h3>
                        <p class="text-muted mb-0">
                            {{ $room->details->title }} — <span class="text-primary">{{ $room->hotel->name }}</span>
                        </p>
                    </div>
                    <div class="ms-auto text-end">
                        <a href="{{ route('admin.rooms.index', ['room_detail_id' => $room->room_detail_id]) }}"
                            class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <ul class="nav nav-pills nav-fill bg-light p-1" style="border-radius: 12px;" id="bookingTabs"
                    role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active border-0 py-2 fw-semibold" id="upcoming-tab" data-bs-toggle="tab"
                            data-bs-target="#upcoming" type="button" role="tab">
                            <i class="bi bi-calendar-event me-2"></i>Upcoming & Active
                            <span class="badge bg-white text-dark ms-2 shadow-sm">{{ $upcoming->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link border-0 py-2 fw-semibold" id="past-tab" data-bs-toggle="tab"
                            data-bs-target="#past" type="button" role="tab">
                            <i class="bi bi-clock-history me-2"></i>Past History
                            <span class="badge bg-white text-dark ms-2 shadow-sm">{{ $past->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content" id="bookingTabsContent">

                    <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                        @include('admin.booking.booking-table', ['bookings' => $upcoming])
                    </div>

                    <div class="tab-pane fade" id="past" role="tabpanel">
                        @include('admin.booking.booking-table', ['bookings' => $past])
                    </div>

                </div>
            </div>
            @if ($room && $room->status)
                <input type="hidden" id="filter_room_detail_id" value="">
                <input type="hidden" id="filter_room_id" value="{{ $room->id ?? '' }}">
                @include('admin.partials.calender-view')
            @endif
        </div>
    </div>


    <style>
        .nav-pills .nav-link.active {
            background-color: #fff !important;
            color: #212529 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .nav-pills .nav-link {
            color: #6c757d;
            transition: 0.3s;
        }

        .bg-soft-primary {
            background-color: #e7f1ff;
            color: #0d6efd;
        }

        .extra-small {
            font-size: 0.75rem;
        }

        .table thead th {
            font-size: 0.7rem;
            letter-spacing: 0.05em;
        }
    </style>
@endsection
