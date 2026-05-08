@extends('layouts.adminlte')

@section('title', 'Edit Promotion')

@section('content')
    <div class="container-fluid py-5">
        <div class="card border-0 shadow-sm m-auto" style="max-width: 900px; border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                {{-- Header with Mode Badge --}}
                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                    <h3 class="fw-bold text-dark mb-0">Edit Promotion</h3>
                    @if($discount->required_code)
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3">
                            <i class="bi bi-ticket-perforated me-1"></i> Coupon
                        </span>
                    @else
                        <span class="badge bg-soft-warning text-warning rounded-pill px-3" style="background-color: #fff4e5;">
                            <i class="bi bi-calendar-check me-1"></i> Seasonal
                        </span>
                    @endif
                </div>
                <p class="text-muted small">Update configuration for <strong>{{ $discount->coupen_code }}</strong></p>
            </div>

            <form action="{{ route('admin.discounts.update', $discount->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="required_code" value="{{ $discount->required_code }}">

                <div class="card-body px-4">
                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Identification Code</label>
                            <input type="text" class="form-control bg-light border-0 py-2 text-uppercase"
                                name="coupen_code" id="coupen_code" value="{{ old('coupen_code', $discount->coupen_code) }}"
                                placeholder="e.g. WELCOME20">
                            @error('coupen_code')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase d-block">Publication Status</label>
                            <div class="d-flex align-items-center bg-light rounded-3 p-2 px-3 border-0" style="height: 42px;">
                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center w-100 justify-content-between">
                                    <span class="small fw-semibold text-muted" id="status_label">
                                        {{ $discount->active_status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                    <input type="hidden" name="active_status" value="0">
                                    <input class="form-check-input ms-0" type="checkbox" name="active_status" value="1" 
                                           id="statusSwitch" style="width: 2.5em; height: 1.25em; cursor: pointer;"
                                           {{ old('active_status', $discount->active_status) == 1 ? 'checked' : '' }}
                                           onchange="document.getElementById('status_label').innerText = this.checked ? 'Active' : 'Inactive'">
                                </div>
                            </div>
                        </div>

                        @if(!$discount->required_code)
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small text-uppercase">Offer Highlight Message</label>
                                <input type="text" class="form-control bg-light border-0 py-2" name="message"
                                    value="{{ old('message', $discount->message) }}" placeholder="e.g. Save 20% this Summer">
                                @error('message') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <hr class="my-2 opacity-25">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Discount Type</label>
                            <select class="form-select bg-light border-0 py-2" name="type">
                                <option value="percentage" {{ old('type', $discount->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type', $discount->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Value</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-currency-exchange"></i></span>
                                <input type="number" step="0.01" class="form-control bg-light border-0" name="value"
                                    value="{{ old('value', $discount->value) }}">
                            </div>
                            @error('value') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Max Discount Amount</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0" name="max_discount"
                                value="{{ old('max_discount', $discount->max_discount) }}" placeholder="Optional">
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border-0 h-100">
                                <label class="form-label fw-semibold small text-uppercase">Times Used</label>
                                <div class="h4 fw-bold text-dark mb-0">{{ $discount->used_count }}</div>
                                <div class="extra-small text-muted mt-1">Read-only system count</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border-0">
                                <label class="form-label fw-semibold small text-uppercase">Global Limit</label>
                                <input type="number" class="form-control border-0 bg-transparent p-0 fw-bold" name="usage_limit"
                                    value="{{ old('usage_limit', $discount->usage_limit) }}" placeholder="Unlimited">
                                <div class="extra-small text-muted mt-2">Total global uses</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border-0">
                                <label class="form-label fw-semibold small text-uppercase">User Limit</label>
                                <input type="number" class="form-control border-0 bg-transparent p-0 fw-bold" name="user_limit"
                                    value="{{ old('user_limit', $discount->user_limit) }}" placeholder="Unlimited">
                                <div class="extra-small text-muted mt-2">Max uses per guest</div>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Valid From</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="starts_from"
                                value="{{ old('starts_from', $discount->starts_from ? $discount->starts_from->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Valid Until</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="ends_at"
                                value="{{ old('ends_at', $discount->ends_at ? $discount->ends_at->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Target Country</label>
                            <select class="form-select bg-light border-0 py-2" name="country_id">
                                <option value="">All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $discount->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Min Nights Required</label>
                            <input type="number" class="form-control bg-light border-0 py-2" name="min_nights"
                                value="{{ old('min_nights', $discount->min_nights) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Min Booking Amount</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0 py-2"
                                name="min_amount" value="{{ old('min_amount', $discount->min_amount) }}">
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 pb-4 px-4 mt-3 d-flex gap-3">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-light px-4 py-3 rounded-pill fw-bold w-25">Cancel</a>
                    <button class="btn btn-dark w-75 py-3 shadow-sm fw-bold rounded-pill" type="submit">
                        <i class="bi bi-check-circle me-2"></i> UPDATE PROMOTION
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .bg-light { background-color: #f8f9fa !important; }
        .bg-soft-primary { background-color: #e7f1ff; color: #0d6efd; }
        .form-control:focus, .form-select:focus { background-color: #f2f2f2 !important; box-shadow: none; border: 1px solid #ddd; }
        .form-check-input:checked { background-color: #198754; border-color: #198754; }
        .extra-small { font-size: 0.75rem; }
    </style>
@endsection