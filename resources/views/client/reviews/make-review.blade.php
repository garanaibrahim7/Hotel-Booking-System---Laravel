@extends('client.layouts.template')

@section('content')
    <div class="container py-5 mt-5">
        <div class="review-form-wrapper mx-auto" style="max-width: 700px; background: #fff; padding: 30px; border: 1px solid #eee;">
            <form action="{{ route('client.store-review') }}" method="POST">
                @csrf
                <input type="hidden" name="reference_number" value="{{ $reference_number }}">

                <div class="text-center mb-5">
                    <h3 style="letter-spacing: 2px; font-weight: bold; color: #2e2e2e;">GIVE YOUR VALUABLE REVIEW</h3>
                    <p class="text-muted small uppercase" style="letter-spacing: 1px;">Click to adjust your overall rating</p>

                    {{-- Main Rating (Ab ye change ho sakti hai) --}}
                    <div class="star-rating justify-content-center mb-4">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="main-{{ $i }}" name="rating" value="{{ $i }}" 
                                {{ $i == $rating ? 'checked' : '' }} required />
                            <label for="main-{{ $i }}" style="font-size: 3rem;">
                                <i class="bi bi-star-fill"></i>
                            </label>
                        @endfor
                    </div>
                </div>

                <div class="row border-top pt-4">
                    @php
                        $categories = [
                            'cleaning' => 'Cleanliness',
                            'services' => 'Services',
                            'food' => 'Food & Dining',
                            'hospitality' => 'Hospitality',
                        ];
                    @endphp

                    @foreach ($categories as $key => $label)
                        <div class="col-md-6 mb-4 d-flex justify-content-between align-items-center px-3">
                            <span class="category-label">{{ $label }}</span>
                            <div class="star-rating">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="{{ $key }}-{{ $i }}"
                                        name="{{ $key }}" value="{{ $i }}" />
                                    <label for="{{ $key }}-{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                @endfor
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="form-group mt-4">
                    <label class="category-label mb-2">Detailed Feedback</label>
                    <textarea name="comment" class="form-control" rows="5" 
                        style="border-color: #eee; border-radius: 0; resize: none; padding: 15px;"
                        placeholder="Please share details about your stay..."></textarea>
                </div>

                <div class="text-center mt-5">
                    <button type="submit" class="btn"
                        style="background: #bca47f; color: #fff; padding: 15px 60px; border-radius: 0; letter-spacing: 2px; font-weight: bold; border: none;">
                        SAVE REVIEW
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s ease-in-out, transform 0.2s ease;
            margin-left: 5px;
            font-size: 1.3rem; /* Default size for categories */
        }

        /* Hover & Checked State */
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #bca47f;
            transform: scale(1.1);
        }

        .category-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #444;
            font-weight: 600;
        }
    </style>
@endpush