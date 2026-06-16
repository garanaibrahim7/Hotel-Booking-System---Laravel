@extends('layouts.adminlte')

@section('title', 'Hotel Details - ' . $hotel->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark mb-0">{{ $hotel->name }}</h2>
            <div class="gap-2 d-flex">
                <a href="{{ route('admin.hotels.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
                <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit Hotel
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-0 overflow-hidden" style="border-radius: 0.5rem 0.5rem 0 0;">
                        {{-- Main Featured Image --}}
                        <img src="{{ $hotel->images->first() ? asset('storage/' . $hotel->images->first()->path) : asset('images/placeholder.jpg') }}"
                            class="w-100 object-fit-cover" style="height: 400px;" alt="Main Image">
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">About the Hotel</h5>
                        <p class="text-muted" style="line-height: 1.6;">{{ $hotel->description }}</p>

                        <hr>

                        <h5 class="fw-bold mb-3">Image Gallery</h5>
                        <div class="row g-2">
                            @foreach ($hotel->images as $image)
                                <div class="col-md-3 col-6">
                                    <a href="{{ asset('storage/' . $image->path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $image->path) }}"
                                            class="img-fluid rounded shadow-sm transition-zoom"
                                            style="height: 100px; width: 100%; object-fit: cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Essential Info Card --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white fw-bold">Quick Information</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold small text-uppercase text-muted">Location</div>
                                    {{ $hotel->address }}, {{ $hotel->city->name }}<br>
                                    {{ $hotel->city->state->name }}, {{ $hotel->city->state->country->name }}
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold small text-uppercase text-muted">Pincode</span>
                                <span class="badge bg-light text-dark border">{{ $hotel->pincode }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold small text-uppercase text-muted">Cancel Charge</span>
                                <span
                                    class="text-danger fw-bold">{{ number_format($hotel->cancellation_charge, 2) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Amenities Card --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold d-flex align-items-center">
                        <i class="bi bi-stars me-2 text-warning"></i> Amenities Provided
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($hotel->amenities as $amenity)
                                <div class="badge border text-dark p-2 d-flex align-items-center gap-2 fw-normal"
                                    style="background: #f8f9fa;">
                                    <i class="{{ $amenity->icon }} text-primary"></i>
                                    {{ $amenity->title }}
                                </div>
                            @empty
                                <p class="text-muted small">No amenities listed for this hotel.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .transition-zoom {
            transition: transform 0.3s ease;
        }

        .transition-zoom:hover {
            transform: scale(1.05);
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>
@endsection
