<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">Latest Reviews</h5>
        <small class="text-muted">Most recent feedback from your guests</small>
    </div>
    <div class="card-body px-0">
        <div class="list-group list-group-flush">


            @forelse ($latestReviews as $review)
                <div class="list-group-item border-0 px-4 py-3">
                    <div class="d-flex align-items-start">

                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                style="width: 45px; height: 45px;">

                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}
                            </div>
                        </div>

                        <div class="flex-grow-1 ms-3">

                            <!-- Name + Rating -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 fw-bold">
                                    {{ $review->user->name ?? 'Unknown User' }}
                                </h6>
                                @php
                                    $reviewColor =
                                        $review->rating > 3
                                            ? 'bg-success text-light'
                                            : ($review->rating > 1
                                                ? 'bg-warning text-dark'
                                                : 'bg-danger');
                                @endphp
                                <span class="badge rounded-pill {{ $reviewColor }}">
                                    <i class="bi bi-star-fill me-1"></i>
                                    {{ number_format($review->rating, 1) }}
                                </span>
                            </div>

                            <!-- Comment -->
                            <p class="text-muted small mb-2">
                                "{{ $review->comment ?? 'No comment provided.' }}"
                            </p>

                            <!-- Sub ratings -->
                            <div class="d-flex flex-wrap gap-2">

                                @if ($review->cleaning)
                                    <span class="badge bg-light text-muted border py-1 px-2 fw-normal"
                                        style="font-size: 0.7rem;">
                                        Cleaning: {{ $review->cleaning }}
                                    </span>
                                @endif

                                @if ($review->food)
                                    <span class="badge bg-light text-muted border py-1 px-2 fw-normal"
                                        style="font-size: 0.7rem;">
                                        Food: {{ $review->food }}
                                    </span>
                                @endif

                                @if ($review->services)
                                    <span class="badge bg-light text-muted border py-1 px-2 fw-normal"
                                        style="font-size: 0.7rem;">
                                        Services: {{ $review->services }}
                                    </span>
                                @endif

                                @if ($review->hospitality)
                                    <span class="badge bg-light text-muted border py-1 px-2 fw-normal"
                                        style="font-size: 0.7rem;">
                                        Hospitality: {{ $review->hospitality }}
                                    </span>
                                @endif

                            </div>

                            <!-- Footer -->
                            <div class="mt-2">
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $review->created_at->diffForHumans() }} •
                                    Booking #{{ $review->booking_id }}
                                </small>
                            </div>

                        </div>
                    </div>
                </div>

            @empty
                <div class="text-center py-4 text-muted">
                    No reviews found
                </div>
            @endforelse


        </div>
    </div>
    <div class="card-footer bg-white border-0 text-center pb-4">
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">View All Reviews</a>
    </div>
</div>
