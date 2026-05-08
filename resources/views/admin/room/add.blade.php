@extends('layouts.adminlte')

@section('title', 'Add New Room')

@section('content')
<div class="container-fluid py-5">
    <div class="card border-0 shadow-sm m-auto" style="max-width: 700px; border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-4 px-4 text-center">
            <h3 class="fw-bold text-dark mb-0">Add Room Unit</h3>
            <p class="text-muted small">
                @if (isset($category))
                    Assigning rooms to <span class="text-primary fw-semibold">{{ $category->title }}</span>
                @else
                    Register new inventory units
                @endif
            </p>
        </div>

        <form action="{{ route('admin.rooms.store') }}" method="POST">
            @csrf
            <div class="card-body px-4">
                <div class="row g-4">
                    
                    <div class="col-md-12 text-center mb-2">
                        <label class="form-label fw-semibold small text-uppercase d-block mb-3">Entry Mode</label>
                        <div class="btn-group shadow-sm" role="group" style="border-radius: 10px; overflow: hidden;">
                            <input type="radio" name="room-add-option" class="btn-check" id="single-room"
                                onchange="changeRoomOption(this.value)" value="single-room" autocomplete="off" 
                                {{ old('room-add-option', 'single-room') == 'single-room' ? 'checked' : '' }}>
                            <label class="btn btn-outline-dark border-0 bg-light px-4 py-2" for="single-room">
                                <i class="bi bi-door-closed me-2"></i>Single Room
                            </label>

                            <input type="radio" name="room-add-option" class="btn-check" id="multiple-rooms"
                                onchange="changeRoomOption(this.value)" value="multiple-rooms" autocomplete="off"
                                {{ old('room-add-option') == 'multiple-rooms' ? 'checked' : '' }}>
                            <label class="btn btn-outline-dark border-0 bg-light px-4 py-2" for="multiple-rooms">
                                <i class="bi bi-grid-3x3-gap me-2"></i>Multiple Rooms
                            </label>
                        </div>
                    </div>

                    @if (isset($hotels) && isset($categories))
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Select Hotel</label>
                            <select class="form-select bg-light border-0 py-2 @error('hotel_id') is-invalid @enderror" 
                                id="hotel" name="hotel_id" onchange="fetchCategory(event.target.value)">
                                <option value="">Choose a property...</option>
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                                        {{ $hotel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hotel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Select Category</label>
                            <select class="form-select bg-light border-0 py-2 @error('room_detail_id') is-invalid @enderror" 
                                id="categoriesDropdown" name="room_detail_id">
                                <option value="" disabled selected>Select Hotel First</option>
                            </select>
                            @error('room_detail_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @else
                        {{-- Contextual Info if coming from Category Page --}}
                        <div class="col-md-12">
                            <div class="p-3 rounded-3 bg-light border-start border-primary border-4">
                                <span class="text-muted small text-uppercase d-block">Category</span>
                                <h5 class="fw-bold mb-0">{{ $category->title }}</h5>
                                <input type="hidden" name="hotel_id" value="{{ $category->hotel_id }}">
                                <input type="hidden" name="room_detail_id" value="{{ $category->id }}">
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12">
                        {{-- Single Room Input --}}
                        <div id="for-single-room" class="{{ old('room-add-option', 'single-room') == 'single-room' ? '' : 'd-none' }}">
                            <label class="form-label fw-semibold small text-uppercase">Room Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted">#</span>
                                <input type="text" class="form-control bg-light border-0 @error('room_number') is-invalid @enderror" 
                                    name="room_number" value="{{ old('room_number') }}" placeholder="e.g. 101">
                            </div>
                            @error('room_number') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- Multiple Rooms Input --}}
                        <div id="for-multiple-rooms" class="{{ old('room-add-option') == 'multiple-rooms' ? '' : 'd-none' }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-uppercase">Prefix (Optional)</label>
                                    <input type="text" class="form-control bg-light border-0" name="room_number_prefix"
                                        value="{{ old('room_number_prefix') }}" placeholder="e.g. Wing-A">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small text-uppercase">From</label>
                                    <input type="number" class="form-control bg-light border-0" name="room_number_from"
                                        value="{{ old('room_number_from') }}" placeholder="101">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold small text-uppercase">To</label>
                                    <input type="number" class="form-control bg-light border-0" name="room_number_to"
                                        value="{{ old('room_number_to') }}" placeholder="110">
                                </div>
                            </div>
                            @error('room_number_from') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light">
                            <div>
                                <label class="fw-semibold text-dark mb-0 d-block">Availability Status</label>
                                <small class="text-muted">Is this room currently ready for check-ins?</small>
                            </div>
                            <div class="form-check form-switch fs-4">
                                <input type="hidden" name="status" value="0">
                                <input class="form-check-input" type="checkbox" name="status" role="switch"
                                    id="statusSwitch" value="1" checked>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer bg-white border-0 pb-4 px-4 mt-2">
                <button class="btn btn-dark w-100 py-3 shadow-sm fw-bold" type="submit" style="border-radius: 12px;">
                    <i class="bi bi-plus-circle me-2"></i> REGISTER ROOM(S)
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-light { background-color: #f8f9fa !important; }
    .form-control:focus, .form-select:focus { background-color: #f2f2f2 !important; box-shadow: none; border: 1px solid #ddd; }
    .btn-check:checked + .btn-outline-dark { background-color: #212529 !important; color: #fff !important; font-weight: bold; }
    .form-check-input:checked { background-color: #198754; border-color: #198754; }
    .extra-small { font-size: 0.8rem; }
</style>
@endsection

@push('scripts')
<script>
    const categories = @json($categories ?? []);
    const single_room_div = document.getElementById('for-single-room');
    const multiple_rooms_div = document.getElementById('for-multiple-rooms');

    function fetchCategory(hotelId) {
        const hotelCategories = categories.filter(c => c.hotel_id == hotelId);
        const dropdown = document.getElementById('categoriesDropdown');
        dropdown.innerHTML = '<option value="" disabled selected>Select Category</option>';

        hotelCategories.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.innerHTML = c.title;
            dropdown.appendChild(opt);
        });
    }

    function changeRoomOption(value) {
        if (value === 'multiple-rooms') {
            single_room_div.classList.add('d-none');
            multiple_rooms_div.classList.remove('d-none');
        } else {
            multiple_rooms_div.classList.add('d-none');
            single_room_div.classList.remove('d-none');
        }
    }

    // Initialize state if validation fails
    @if(old('room-add-option') == 'multiple-rooms')
        changeRoomOption('multiple-rooms');
    @endif
</script>
@endpush