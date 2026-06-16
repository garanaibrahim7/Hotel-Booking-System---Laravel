@extends('layouts.adminlte')

@section('title', __('hotel.create_title'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 w-75 m-auto">
            <h3 class="fw-bold text-dark mb-0">{{ __('hotel.create_title') }}</h3>
            <a href="{{ route('admin.hotels.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> {{ __('hotel.back_to_list') }}
            </a>
        </div>

        <div class="card border-0 shadow-sm w-75 m-auto">
            <form action="{{ route('admin.hotels.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body p-4">
                    <div class="row g-4">

                        <div class="col-md-12">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted">{{ __('hotel.name') }}</label>
                            <input type="text" class="form-control border-0 bg-light" name="name"
                                value="{{ old('name') }}" placeholder="{{ __('hotel.placeholders.hotel_name') }}">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted">{{ __('hotel.description') }}</label>
                            <textarea class="form-control border-0 bg-light" name="description" rows="3"
                                placeholder="{{ __('hotel.placeholders.desc_example') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="col-md-8">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted">{{ __('hotel.address') }}</label>
                            <input type="text" class="form-control border-0 bg-light" name="address"
                                value="{{ old('address') }}" placeholder="Street, Landmark">
                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted">{{ __('hotel.pincode') }}</label>
                            <input type="text" class="form-control border-0 bg-light" name="pincode"
                                value="{{ old('pincode') }}" placeholder="360001">
                            @error('pincode')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted">{{ __('hotel.city') }}</label>
                            <select class="form-control border-0 bg-light" name="city_id">
                                <option value="">Select City</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}"
                                        {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Cancellation
                                Charge</label>
                            <input type="number" class="form-control border-0 bg-light" name="cancellation_charge"
                                value="{{ old('cancellation_charge', 0) }}">
                            @error('cancellation_charge')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="col-md-12">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted mb-3">{{ __('hotel.amenities') }}</label>
                            <div class="row g-3">
                                @foreach ($amenities as $key => $amenity)
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <input type="checkbox" class="btn-check" id="amenity-{{ $amenity['id'] }}"
                                            name="amenities[]" value="{{ $key }}"
                                            {{ is_array(old('amenities')) && in_array($amenity['id'], old('amenities')) ? 'checked' : '' }}>
                                        <label
                                            class="btn btn-outline-light border text-dark w-100 py-3 d-flex align-items-center gap-3 amenity-card"
                                            for="amenity-{{ $amenity['id'] }}">
                                            <i class="{{ $amenity['icon'] }} fs-4 text-primary"></i>
                                            <span class="small fw-medium">{{ $amenity['title'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('amenities')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="col-md-12">
                            <label
                                class="form-label fw-semibold small text-uppercase text-muted mb-3">{{ __('hotel.images') }}</label>
                            <div class="upload-main-wrapper bg-light p-4 rounded-3 border-2 border-dashed">
                                <div class="upload-container" id="uploadContainer">

                                    <div class="upload-box" onclick="this.querySelector('input').click()">
                                        <i class="bi bi-plus-lg plus"></i>
                                        <input type="file" name="images[]" class="d-none"
                                            onchange="previewNewImage(this)" accept="image/*">
                                        <div class="delete-btn" onclick="removeBox(this, event)" style="display:none;">
                                            <i class="bi bi-x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white p-4 border-top-0">
                    <button class="btn btn-primary px-5 py-2 fw-bold shadow-sm" type="submit">Create Hotel</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .upload-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .upload-box {
            width: 100px;
            height: 100px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
        }

        .upload-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 9px;
        }

        .plus {
            font-size: 24px;
            color: #adb5bd;
            pointer-events: none;
        }

        .delete-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #dc3545;
            color: #fff;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border: 2px solid #fff;
            cursor: pointer;
        }

        .amenity-card {
            transition: all 0.2s;
            border-radius: 10px;
            text-align: left;
            cursor: pointer;
            background: #fff;
        }

        .btn-check:checked+.amenity-card {
            border-color: #0d6efd !important;
            background: #f0f7ff !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function previewNewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const box = input.parentElement;

                    // Create or update img tag
                    let img = box.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        box.appendChild(img);
                    }
                    img.src = e.target.result;

                    box.querySelector('.plus').style.display = 'none';
                    box.querySelector('.delete-btn').style.display = 'flex';

                    const container = document.getElementById('uploadContainer');
                    if (box === container.lastElementChild) {
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
            <input type="file" name="images[]" class="d-none" onchange="previewNewImage(this)" accept="image/*">
            <div class="delete-btn" onclick="removeBox(this, event)" style="display:none;"><i class="bi bi-x"></i></div>
        `;
            container.appendChild(div);
        }

        function removeBox(btn, e) {
            e.stopPropagation();
            const box = btn.closest('.upload-box');
            const container = document.getElementById('uploadContainer');

            if (container.querySelectorAll('.upload-box').length > 1) {
                box.remove();
            } else {
                box.querySelector('img')?.remove();
                box.querySelector('input').value = "";
                box.querySelector('.plus').style.display = 'block';
                box.querySelector('.delete-btn').style.display = 'none';
            }
        }
    </script>
@endpush
