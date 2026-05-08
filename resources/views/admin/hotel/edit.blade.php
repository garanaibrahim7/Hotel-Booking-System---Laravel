@extends('layouts.adminlte')

@section('title', 'Update Hotel - ' . $hotel->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 w-75 m-auto">
            <h3 class="text-dark mb-0">Update <span class="fw-bold">{{ $hotel->name }}</span>'s Details</h3>
            <a href="{{ route('admin.hotels.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm w-75 m-auto">
            <form action="{{ route('admin.hotels.update', $hotel->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- <div class="col-md-12">
                            <label for="name"
                                class="form-label fw-semibold small text-uppercase text-muted">Name</label>
                            <input type="text" class="form-control form-control-lg border-0 bg-light text-muted" id="name"
                            style="cursor: not-allowed"
                                name="name" value="{{ old('name', $hotel->name) }}" placeholder="Enter Hotel Name" disabled />
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div> --}}
                        <input type="hidden" name="name" value="{{ old('name', $hotel->name) }}" />

                        <div class="col-md-12">
                            <label for="description"
                                class="form-label fw-semibold small text-uppercase text-muted">Description</label>
                            <textarea class="form-control border-0 bg-light" id="description" name="description" rows="3"
                                placeholder="Enter Description">{{ old('description', $hotel->description) }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="address"
                                class="form-label fw-semibold small text-uppercase text-muted">Address</label>
                            <input type="text" class="form-control border-0 bg-light" id="address" name="address"
                                value="{{ old('address', $hotel->address) }}" placeholder="Enter Address" />
                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="pincode"
                                class="form-label fw-semibold small text-uppercase text-muted">Pincode</label>
                            <input type="text" class="form-control border-0 bg-light" id="pincode" name="pincode"
                                value="{{ old('pincode', $hotel->pincode) }}" placeholder="Pincode" />
                            @error('pincode')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="city_id"
                                class="form-label fw-semibold small text-uppercase text-muted">City</label>
                            <select class="form-control select2 border-0 bg-light" id="city_id" name="city_id">
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('city_id', $hotel->city_id) == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }} ({{ $city->state->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="cancellation_charge"
                                class="form-label fw-semibold small text-uppercase text-muted">Cancellation Charge</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text border-0 bg-light">{{ $hotel->city->state->country->currency_symbol }}</span>
                                <input type="number" class="form-control border-0 bg-light" id="cancellation_charge"
                                    name="cancellation_charge"
                                    value="{{ old('cancellation_charge', $hotel->cancellation_charge) }}" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase text-muted mb-3">Amenities</label>
                            <div class="row g-3">
                                @php
                                    $selected = collect(old('amenities', $hotel->amenities->pluck('title')))->toArray();
                                @endphp

                                {{-- @json($selected) --}}

                                @foreach ($amenities as $key => $amenity)
                                    {{-- "{{ $amenity['title'] }}", --}}
                                    {{-- {{ in_array($amenity['title'], $selected) ? 'checked' : 'not checked' }} --}}
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <input type="checkbox" class="btn-check" id="amenity-{{ $amenity['id'] }}"
                                            name="amenities[]" value="{{ $key }}"
                                            {{ in_array($amenity['title'], $selected) ? 'checked' : '' }}>

                                        <label
                                            class="btn btn-outline-light border text-dark w-100 py-3 d-flex align-items-center gap-3 amenity-card"
                                            for="amenity-{{ $amenity['id'] }}">
                                            <i class="{{ $amenity['icon'] }} fs-4 text-primary"></i>
                                            <span class="small fw-medium">{{ $amenity['title'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-uppercase text-muted mb-3">Hotel Images</label>
                            <div class="upload-container shadow-none" id="uploadContainer">
                                @foreach ($hotel->images as $image)
                                    <div class="upload-box existing-image">
                                        <img src="{{ asset('storage/' . $image->path) }}">
                                        <div class="delete-btn" onclick="removeExistingImage(this, {{ $image->id }})">
                                            <i class="bi bi-x"></i>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="upload-box" onclick="this.querySelector('input').click()">
                                    <i class="bi bi-plus-lg plus"></i>
                                    <input type="file" name="images[]" class="d-none" onchange="previewImage(this)"
                                        accept="image/*">
                                    <div class="delete-btn" onclick="removeNewImage(this, event)" style="display:none;">
                                        <i class="bi bi-x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="removedImagesInput"></div>

                <div class="card-footer bg-white p-4 border-top-0">
                    <button class="btn btn-warning px-5 py-2 fw-bold shadow-sm" type="submit">Update Hotel</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-control:focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.1);
        }

        .btn-check:checked+.amenity-card {
            background-color: #fffbeb !important;
            border-color: #ffc107 !important;
        }

        .amenity-card {
            transition: all 0.2s ease;
            border-radius: 10px;
            text-align: left;
            cursor: pointer;
        }

        .upload-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border: 2px dashed #dee2e6;
        }

        .upload-box {
            width: 100px;
            height: 100px;
            background: #fff;
            border: 1px solid #ced4da;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
        }

        .upload-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 7px;
        }

        .plus {
            font-size: 30px;
            color: #adb5bd;
            position: absolute;
        }

        .delete-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            z-index: 10;
            cursor: pointer;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const box = input.parentElement;
                    let img = box.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        box.appendChild(img);
                    }
                    img.src = e.target.result;
                    box.querySelector('.plus').style.display = 'none';
                    box.querySelector('.delete-btn').style.display = 'flex';

                    if (box === document.querySelector('#uploadContainer .upload-box:last-child')) {
                        addNewBox();
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addNewBox() {
            const container = document.getElementById('uploadContainer');
            const div = document.createElement('div');
            div.className = 'upload-box';
            div.onclick = function() {
                this.querySelector('input').click();
            };
            div.innerHTML = `
                <i class="bi bi-plus-lg plus"></i>
                <input type="file" name="images[]" class="d-none" onchange="previewImage(this)" accept="image/*">
                <div class="delete-btn" onclick="removeNewImage(this, event)" style="display:none;"><i class="bi bi-x"></i></div>
            `;
            container.appendChild(div);
        }

        function removeNewImage(btn, e) {
            e.stopPropagation();
            btn.closest('.upload-box').remove();
        }

        function removeExistingImage(btn, imageId) {
            if (confirm('Are you sure you want to remove this image?')) {
                const container = document.getElementById('removedImagesInput');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'removed_images[]';
                input.value = imageId;
                container.appendChild(input);
                btn.closest('.upload-box').remove();
            }
        }
    </script>
@endpush
