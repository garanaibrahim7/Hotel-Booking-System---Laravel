@extends('layouts.adminlte')

@section('title', 'Add Room Category')

@section('content')
    <div class="container-fluid py-5">
        <div class="card border-0 shadow-sm m-auto" style="max-width: 850px; border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                <h3 class="fw-bold text-dark mb-0">Create Room Category</h3>
                <p class="text-muted small">Property: <span class="text-primary fw-semibold" id="hotel_name_display">Select a
                        Hotel</span></p>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body px-4">
                    <div class="row g-4">

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Select Hotel</label>
                            <select class="form-select bg-light border-0 py-2 @error('hotel_id') is-invalid @enderror"
                                id="hotel_select" name="hotel_id" onchange="hotelChange(this.value)"
                                {{ empty(request('hotel')) ? '' : 'disabled' }}>
                                <option value="">Choose a property...</option>
                                @foreach ($hotels as $hotel)
                                    <option value="{{ $hotel->id }}"
                                        {{ old('hotel_id', request('hotel')) == $hotel->id ? 'selected' : '' }}>
                                        {{ $hotel->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if (!empty(request('hotel')))
                                <input type="hidden" name="hotel_id" value="{{ request('hotel') }}">
                            @endif
                            @error('hotel_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Room Type</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($types as $type)
                                    <input type="radio" class="btn-check" name="type" id="type_{{ $type }}"
                                        value="{{ $type }}" {{ old('type') == $type ? 'checked' : '' }}>
                                    <label class="btn btn-outline-light text-dark border-0 bg-light px-4 py-2"
                                        for="type_{{ $type }}">
                                        {{ Str::title($type) }}
                                    </label>
                                @endforeach
                            </div>
                            @error('type')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Category Level</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($categories as $cat)
                                    <input type="radio" class="btn-check" name="category" id="cat_{{ $cat }}"
                                        value="{{ $cat }}" {{ old('category') == $cat ? 'checked' : '' }}>
                                    <label class="btn btn-outline-light text-dark border-0 bg-light px-4 py-2"
                                        for="cat_{{ $cat }}">
                                        {{ Str::title($cat) }}
                                    </label>
                                @endforeach
                            </div>
                            @error('category')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Description</label>
                            <textarea class="form-control bg-light border-0 @error('description') is-invalid @enderror" name="description"
                                placeholder="e.g. Ocean view, king size bed..." rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Allowed Guests</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                <input type="number" name="max_adults"
                                    class="form-control bg-light border-0 me-2 @error('max_adults') is-invalid @enderror"
                                    value="{{ old('max_adults', 1) }}" placeholder="Adults">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person-arms-up"></i></span>
                                <input type="number" name="max_children"
                                    class="form-control bg-light border-0 @error('max_children') is-invalid @enderror"
                                    value="{{ old('max_children', 0) }}" placeholder="Kids">
                            </div>
                            @error('max_adults')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                            @enderror
                            @error('max_children')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Rent / Night (<span
                                    id="currency_code">USD</span>)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-tag"></i></span>
                                <input type="number"
                                    class="form-control bg-light border-0 @error('price') is-invalid @enderror"
                                    name="price" value="{{ old('price', 0) }}" placeholder="0.00">
                            </div>
                            @error('price')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Total Units</label>
                            <input type="number" class="form-control bg-light border-0 @error('qty') is-invalid @enderror"
                                id="qty_input" name="qty" value="{{ old('qty', 0) }}" placeholder="e.g. 10"
                                oninput="roomInputChanged(this.value)" />
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <div id="for-single-room" class="{{ old('qty') == 1 ? '' : 'd-none' }}">
                                <label class="form-label fw-semibold small text-uppercase">Room Number</label>
                                <input type="text"
                                    class="form-control bg-light border-0 @error('room_number') is-invalid @enderror"
                                    name="room_number" value="{{ old('room_number') }}" placeholder="e.g. 101">
                                @error('room_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="for-multiple-rooms" class="{{ old('qty') > 1 ? '' : 'd-none' }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-uppercase">Prefix</label>
                                        <input type="text" class="form-control bg-light border-0"
                                            name="room_number_prefix" value="{{ old('room_number_prefix') }}"
                                            placeholder="A">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold small text-uppercase">Start From</label>
                                        <input type="text" class="form-control bg-light border-0"
                                            name="room_number_from" value="{{ old('room_number_from') }}"
                                            placeholder="101">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase">Gallery Images</label>
                            <div class="upload-grid" id="uploadContainer">
                                <div class="upload-box shadow-sm" onclick="this.querySelector('input').click()">
                                    <i class="bi bi-plus-lg fs-3 text-muted"></i>
                                    <input type="file" name="images[]" accept="image/*"
                                        onchange="handlePreview(this)">
                                </div>
                            </div>
                            @error('images')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}
                                </div>
                            @enderror
                            @error('images.*')
                                <div class="text-danger extra-small mt-1" style="font-size: 0.8rem;">{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 pb-4 px-4 mt-3">
                    <button class="btn btn-dark w-100 py-3 shadow-sm fw-bold" type="submit"
                        style="border-radius: 12px;">
                        <i class="bi bi-cloud-arrow-up me-2"></i> CREATE CATEGORY
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .bg-light {
            background-color: #f8f9fa !important;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #f2f2f2 !important;
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .btn-check:checked+.btn-outline-light {
            background-color: #212529 !important;
            color: #fff !important;
            font-weight: bold;
        }

        .upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 110px));
            gap: 15px;
        }

        .upload-box {
            width: 110px;
            height: 110px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border: 2px dashed #e0e0e0;
            transition: 0.2s;
        }

        .upload-box:hover {
            border-color: #bbb;
            background: #f1f1f1;
        }

        .upload-box input {
            display: none;
        }

        .upload-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .remove-img-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: white;
            border: none;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc3545;
            z-index: 10;
            border: 1px solid #eee;
        }
    </style>
@endsection

@push('scripts')
    <script>
        function roomInputChanged(val) {
            const single = document.getElementById('for-single-room');
            const multi = document.getElementById('for-multiple-rooms');
            single.classList.toggle('d-none', val != 1);
            multi.classList.toggle('d-none', val <= 1);
        }

        function handlePreview(input) {
            if (input.files && input.files[0]) {
                const box = input.closest('.upload-box');
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Remove previous UI elements (icons/buttons) but KEEP the input
                    const existingImg = box.querySelector('img');
                    const existingBtn = box.querySelector('button');
                    if (existingImg) existingImg.remove();
                    if (existingBtn) existingBtn.remove();
                    box.querySelector('.bi-plus-lg').style.display = 'none';

                    // Create Image
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    box.appendChild(img);

                    // Create Delete Button
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'remove-img-btn shadow-sm';
                    btn.innerHTML = '<i class="bi bi-trash3-fill" style="font-size: 12px;"></i>';
                    btn.onclick = (event) => {
                        event.stopPropagation();
                        box.remove();
                    };
                    box.appendChild(btn);

                    // Add a new empty box only if this was the "last" box
                    if (box === document.getElementById('uploadContainer').lastElementChild) {
                        addNewBox();
                    }
                };
                reader.readAsDataURL(input.files[0]);
                box.onclick = null; // Disable re-click triggering on the same box
            }
        }

        function addNewBox() {
            const container = document.getElementById('uploadContainer');
            const div = document.createElement('div');
            div.className = 'upload-box shadow-sm';
            div.onclick = function() {
                this.querySelector('input').click();
            };
            div.innerHTML = `
            <i class="bi bi-plus-lg fs-3 text-muted"></i>
            <input type="file" name="images[]" accept="image/*" onchange="handlePreview(this)">
        `;
            container.appendChild(div);
        }

        const hotels = @json($hotels);

        function hotelChange(id) {
            const h = hotels.find(x => x.id == id);
            if (h) {
                document.getElementById('hotel_name_display').innerText = h.name;
                document.getElementById('currency_code').innerText = h.currency_code;
            }
        }

        // Init call
        @if (old('hotel_id', request('hotel')))
            hotelChange({{ old('hotel_id', request('hotel')) }});
        @endif
    </script>
@endpush
