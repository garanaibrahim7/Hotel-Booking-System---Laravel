@extends('layouts.adminlte')

@section('title', 'Reviews Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-0">Guest Reviews</h3>
                <p class="text-muted small">Monitor and moderate property feedback</p>
            </div>
        </div>

        @if ($reviews->isNotEmpty())
            <div class="row">
                @foreach ($reviews as $review)
                    <div class="col-12 mb-3">
                        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column flex-md-row">
                                    {{-- Left Section: Main Rating & Image --}}
                                    <div class="p-4 text-center border-end bg-light d-flex flex-column justify-content-center"
                                        style="min-width: 200px;">
                                        <h1 class="fw-bold text-primary mb-0">{{ number_format($review->rating, 1) }}</h1>
                                        <div class="text-warning mb-2">
                                            {!! str_repeat('<i class="bi bi-star-fill"></i>', floor($review->rating)) !!}
                                            {!! str_repeat('<i class="bi bi-star"></i>', floor(5 - $review->rating)) !!}
                                        </div>
                                        <span
                                            class="badge rounded-pill {{ $review->status ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} px-3 py-2">
                                            {{ $review->status ? 'Visible' : 'Hidden' }}
                                        </span>
                                    </div>

                                    <div class="p-4 flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="fw-bold text-dark mb-1">{{ $review->user->name }}</h5>
                                                <p class="text-muted extra-small mb-0">
                                                    <i class="bi bi-building me-1"></i> {{ $review->hotel->name }}
                                                    <span class="mx-2">|</span>
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $review->created_at->format('d M, Y') }}
                                                </p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-light btn-sm rounded-pill px-3" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#reviewDetail{{ $review->id }}">
                                                    <i class="bi bi-arrows-expand me-1"></i> Expand
                                                </button>

                                                @php
                                                    $isTrashed = $review->trashed();
                                                @endphp

                                                <button type="button"
                                                    class="btn btn-{{ $isTrashed ? 'outline-success' : 'outline-danger' }} btn-sm rounded-pill px-3"
                                                    onclick="confirmAction('{{ route('admin.reviews.toggle', $review->id) }}', 
                                                        '{{ $isTrashed ? 'Restore Review?' : 'Hide Public Review?' }}', 
                                                        '{{ $isTrashed ? 'This will make the review visible to the public again.' : 'This will remove the review from the public site (Soft Delete).' }}', 
                                                        '{{ $isTrashed ? 'btn-success' : 'btn-danger' }}')">

                                                    <i class="bi bi-{{ $isTrashed ? 'eye' : 'eye-slash' }} me-1"></i>
                                                    {{ $isTrashed ? 'Restore Review' : 'Hide Review' }}
                                                </button>
                                            </div>
                                        </div>

                                        <p class="text-dark mb-0 fst-italic">
                                            "{{ Str::limit($review->comment, 120) }}"
                                        </p>

                                        @if ($review->image)
                                            <div class="mt-3">
                                                <img src="{{ asset('storage/' . $review->image) }}"
                                                    class="rounded shadow-sm"
                                                    style="height: 60px; width: 60px; object-fit: cover;">
                                            </div>
                                        @endif


                                        <div class="collapse mt-4 pt-3 border-top" id="reviewDetail{{ $review->id }}">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold small text-uppercase text-muted mb-3">Sub-Ratings
                                                    </h6>
                                                    <div class="d-flex flex-wrap gap-3">
                                                        <div class="bg-light p-2 px-3 rounded-pill small">
                                                            Food: <span class="fw-bold">{{ $review->food }}</span>
                                                        </div>
                                                        <div class="bg-light p-2 px-3 rounded-pill small">
                                                            Cleanliness: <span
                                                                class="fw-bold">{{ $review->cleaning }}</span>
                                                        </div>
                                                        <div class="bg-light p-2 px-3 rounded-pill small">
                                                            Service: <span class="fw-bold">{{ $review->services }}</span>
                                                        </div>
                                                        <div class="bg-light p-2 px-3 rounded-pill small">
                                                            Hospitality: <span
                                                                class="fw-bold">{{ $review->hospitality }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="fw-bold small text-uppercase text-muted mb-2">Full Feedback
                                                    </h6>
                                                    <p class="small text-dark">{{ $review->comment }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-chat-left-dots display-1 text-light"></i>
                <h4 class="text-muted mt-3">No reviews found yet.</h4>
            </div>
        @endif
    </div>

    <style>
        .bg-soft-success {
            background-color: #e8fadf;
            color: #28a745;
        }

        .bg-soft-danger {
            background-color: #ffe5e5;
            color: #dc3545;
        }

        .extra-small {
            font-size: 0.75rem;
        }
    </style>
@endsection
