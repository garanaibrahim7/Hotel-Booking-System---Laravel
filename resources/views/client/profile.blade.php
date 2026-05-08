@extends('client.layouts.template')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 0; border-top: 4px solid #bca47f;">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                @php
                                    $avatar =
                                        $user->profile && $user->profile->pic
                                            ? asset('/storage/' . $user->profile->pic->path)
                                            : asset('/storage/assets/profile_pic_default.png');
                                @endphp
                                <img src="{{ $avatar }}" class="img-fluid border p-1 rounded-pill"
                                    style="width: 160px; height: 160px; object-fit: cover; border-color: #bca47f !important;">
                            </div>
                            <div class="col-md-6 text-center text-md-start">
                                <h2 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">{{ $user->name }}
                                </h2>
                                <p class="text-muted mb-1 mt-3"><i class="bi bi-envelope me-2"></i>{{ $user->email }}</p>
                                <p class="text-muted mb-3"><i class="bi bi-telephone me-2"></i>{{ $user->phone }}</p>
                                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                                    {{-- <span class="badge bg-light text-dark border px-3 py-2"> --}}
                                    {{-- <i class="bi bi-telephone me-2 text-primary"></i>{{ $user->phone }} --}}
                                    {{-- </span> --}}
                                </div>
                            </div>
                            <div class="col-md-3 text-center text-md-end mt-4 mt-md-0">
                                <button class="btn btn-brand w-100 mb-2 py-2 fw-bold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#editProfileForm">
                                    <i class="bi bi-pencil-square me-2"></i> EDIT PROFILE
                                </button>
                                <a href="{{ route('password.change') }}" class="btn btn-outline-dark w-100 py-2 fw-bold"
                                    style="border-radius: 0;">
                                    <i class="bi bi-shield-lock me-2"></i> CHANGE PASSWORD
                                </a>
                                
                                <a href="{{ route('client.delete-account') }}"
                                    class="btn btn-outline-dark text-light w-100 py-2 my-2 fw-bold"
                                    style="border-radius: 0; background-color: #ba342ade">
                                    <i class="bi bi-trash3 me-2"></i> DELETE PROFILE
                                </a>

                                <a href="{{ route('logout') }}"
                                    class="btn btn-outline-dark text-light w-100 py-2 my-2 fw-bold"
                                    style="border-radius: 0; background-color: #ba342ade">
                                    <i class="bi bi-box-arrow-left me-2"></i> LOG OUT
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collapse {{ !$user->profile ? 'show' : '' }}" id="editProfileForm">
                    <div class="card border-0 shadow-lg" style="border-radius: 0;">
                        <div class="card-header bg-dark text-white p-4">
                            <h5 class="mb-0 text-uppercase" style="letter-spacing: 2px; color: #bca47f;">
                                @if ($user->profile)
                                    Update Your Details
                                @else
                                    Complete Your Profile <small>(It will help you to Smooth Check-in Experience)</small>
                                @endif
                            </h5>
                        </div>


                        <div class="card-body p-5 bg-white">
                            <form action="{{ route('client.profile.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-4">Account Information</h6>
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-uppercase text-muted">Full Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-uppercase text-muted">Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', $user->phone) }}" required>
                                        @error('phone')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small fw-bold text-uppercase text-muted">Email Address</label>
                                        <input type="email" class="form-control bg-light" value="{{ $user->email }}"
                                            disabled>
                                        <small class="text-muted italic">*Email cannot be changed for security
                                            reasons.</small>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-4">Personal & Address Details</h6>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <label class="small fw-bold text-uppercase text-muted">Profile Photo (Choose to
                                            Update)</label>
                                        <input type="file" name="profile_pic" class="form-control">
                                        @error('profile_pic')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold text-uppercase text-muted">Gender</label>
                                        <select name="gender" class="form-control">
                                            <option value="">Select Gender</option>
                                            <option value="male"
                                                {{ old('gender', $user->profile?->gender) == 'male' ? 'selected' : '' }}>
                                                Male
                                            </option>
                                            <option value="female"
                                                {{ old('gender', $user->profile?->gender) == 'female' ? 'selected' : '' }}>
                                                Female
                                            </option>
                                            <option value="other"
                                                {{ old('gender', $user->profile?->gender) == 'other' ? 'selected' : '' }}>
                                                Other
                                            </option>
                                        </select>
                                        @error('gender')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold text-uppercase text-muted">Date of Birth</label>
                                        <input type="date" name="dob" class="form-control" max="{{ date('Y-m-d') }}"
                                            value="{{ old('dob', $user->profile?->dob) }}">
                                        @error('dob')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small fw-bold text-uppercase text-muted">ID Type</label>
                                        <select name="id_type" class="form-control">
                                            <option value="">Select Id Type</option>
                                            <option value="aadhar"
                                                {{ old('id_type', $user->profile?->id_type) == 'aadhar' ? 'selected' : '' }}>
                                                Aadhar Card</option>
                                            <option value="passport"
                                                {{ old('id_type', $user->profile?->id_type) == 'passport' ? 'selected' : '' }}>
                                                Passport</option>
                                            <option value="driving_license"
                                                {{ old('id_type', $user->profile?->id_type) == 'driving_license' ? 'selected' : '' }}>
                                                Driving License</option>
                                        </select>
                                        @error('id_type')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small fw-bold text-uppercase text-muted">ID Number</label>
                                        <input type="text" name="id_number" class="form-control"
                                            value="{{ old('id_number', $user->profile?->id_number) }}">
                                        @error('id_number')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="small fw-bold text-uppercase text-muted">Residential Address</label>
                                        <textarea name="address" class="form-control" rows="2">{{ old('address', $user->profile?->address) }}</textarea>
                                        @error('address')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="city_id" class="form-label">City</label>
                                        <select class="form-control select2" id="city_id" name="city_id"
                                            onfocus="fetchCities()" style="width: 100%;">
                                            <option value="">Search and Select Your City</option>
                                        </select>
                                        @error('city_id')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold text-uppercase text-muted">Pincode</label>
                                        <input type="text" name="pincode" class="form-control"
                                            value="{{ old('pincode', $user->profile?->pincode) }}">
                                        @error('pincode')
                                            <label class="text-danger">{{ $message }}</label>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-5">
                                    <button type="submit" class="btn btn-brand flex-grow-1 py-3 fw-bold">SAVE
                                        CHANGES</button>
                                    <button type="button" class="btn btn-light border px-4" data-bs-toggle="collapse"
                                        data-bs-target="#editProfileForm">CANCEL</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .btn-brand {
            background-color: #bca47f;
            color: white;
            border: none;
            border-radius: 0;
        }

        .btn-brand:hover {
            background-color: #a8926d;
            color: white;
        }

        .form-control {
            border-radius: 0;
            border: 1px solid #e0e0e0;
            padding: 12px;
        }

        .form-control:focus {
            border-color: #bca47f;
            box-shadow: none;
        }

        .text-primary {
            color: #bca47f !important;
        }
    </style>
@endsection

@push('scripts')
    <script>
        let url = @json(url('/')) + '/cities';
        let selectedCity = @json(old('city_id', $user->profile?->city_id));
        let cities;


        const fetchCities = async () => {
            if (cities)
                return;

            try {
                let res = await fetch(url);
                cities = await res.json();

                let dropdown = document.getElementById('city_id');
                cities.forEach(city => {
                    let option = document.createElement('option');
                    option.value = city.id;
                    option.innerHTML = city.full_name;
                    option.selected = city.id == selectedCity;
                    dropdown.appendChild(option);
                });
                // console.log(cities);
            } catch (error) {
                alert('There is Server Problem to Fetch Cities');
            }

        }
        if (selectedCity)
            fetchCities()
    </script>
@endpush
