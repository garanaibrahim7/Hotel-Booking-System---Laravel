@extends('layouts.adminlte')

@section('title', 'Manage Promotions')

@section('content')
    <div class="container-fluid py-5">
        <div class="card border-0 shadow-sm m-auto" style="max-width: 900px; border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                <h3 class="fw-bold text-dark mb-0">Create Promotion</h3>
                <p class="text-muted small">Configure standard coupons or automated seasonal highlights</p>

                {{-- Mode Switcher --}}
                <div class="btn-group shadow-sm mt-3" role="group" style="border-radius: 10px; overflow: hidden;">
                    <input type="radio" class="btn-check" name="promo_type" id="type_coupon" autocomplete="off" 
                        onchange="togglePromoMode('coupon')" {{ !request('seasonal') && old('required_code', '1') == '1' ? 'checked' : '' }}>
                    <label class="btn btn-outline-dark border-0 bg-light px-4 py-2" for="type_coupon">
                        <i class="bi bi-ticket-perforated me-2"></i>Coupon Code
                    </label>

                    <input type="radio" class="btn-check" name="promo_type" id="type_seasonal" autocomplete="off"
                        onchange="togglePromoMode('seasonal')" {{ request('seasonal') || old('required_code') == '0' ? 'checked' : '' }}>
                    <label class="btn btn-outline-dark border-0 bg-light px-4 py-2" for="type_seasonal">
                        <i class="bi bi-calendar-check me-2"></i>Seasonal Offer
                    </label>
                </div>
            </div>

            <form action="{{ route('admin.discounts.store') }}" method="POST">
                @csrf
                <input type="hidden" name="required_code" id="required_code" value="{{ (request('seasonal') || old('required_code') == '0') ? '0' : '1' }}">

                <div class="card-body px-4">
                    <div class="row g-4">

                        {{-- Coupon Code (Unique Identifier) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Identification Code</label>
                            <input type="text" class="form-control bg-light border-0 py-2 text-uppercase"
                                name="coupen_code" id="coupen_code" value="{{ old('coupen_code') }}"
                                placeholder="e.g. WELCOME20">
                            <div class="extra-small text-muted mt-1" id="code_hint text-lowercase">Used to identify this offer in the database.</div>
                            @error('coupen_code')
                                <div class="text-danger extra-small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Luxury Toggle Switch for Status --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase d-block">Publication Status</label>
                            <div class="d-flex align-items-center bg-light rounded-3 p-2 px-3 border-0" style="height: 42px;">
                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center w-100 justify-content-between">
                                    <span class="small fw-semibold text-muted" id="status_label">Active</span>
                                    <input type="hidden" name="active_status" value="0">
                                    <input class="form-check-input ms-0" type="checkbox" name="active_status" value="1" 
                                           id="statusSwitch" style="width: 2.5em; height: 1.25em; cursor: pointer;"
                                           {{ old('active_status', '1') == '1' ? 'checked' : '' }}
                                           onchange="document.getElementById('status_label').innerText = this.checked ? 'Active' : 'Inactive'">
                                </div>
                            </div>
                        </div>

                        {{-- Seasonal Specific Content --}}
                        <div class="col-md-12 promo-seasonal d-none">
                            <label class="form-label fw-semibold small text-uppercase">Offer Highlight Message</label>
                            <input type="text" class="form-control bg-light border-0 py-2" name="message"
                                value="{{ old('message') }}" placeholder="e.g. Save 20% on all Beach Resorts this Summer">
                            @error('message') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 promo-seasonal d-none">
                            <label class="form-label fw-semibold small text-uppercase">Hotel Exclusive (Optional)</label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control bg-light border-0 py-2 ps-5" name="hotel_search"
                                    placeholder="Search hotel name..." list="hotel_list" value="{{ old('hotel_search') }}">
                                <datalist id="hotel_list">
                                    @foreach ($hotels as $hotel)
                                        <option data-id="{{ $hotel->id }}" value="{{ $hotel->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        {{-- Value Configurations --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Discount Type</label>
                            <select class="form-select bg-light border-0 py-2" name="type">
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Value</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-currency-exchange"></i></span>
                                <input type="number" step="0.01" class="form-control bg-light border-0" name="value"
                                    value="{{ old('value') }}" placeholder="10">
                            </div>
                            @error('value') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Max Discount Amount</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0" name="max_discount"
                                value="{{ old('max_discount') }}" placeholder="Optional limit">
                        </div>

                        <hr class="my-2 opacity-25">

                        {{-- Usage Limits --}}
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border-0">
                                <label class="form-label fw-semibold small text-uppercase">Global Usage Limit</label>
                                <input type="number" class="form-control border-0 bg-transparent" name="usage_limit"
                                    value="{{ old('usage_limit') }}" placeholder="Unlimited">
                                <div class="extra-small text-muted mt-2">Total times this can be used globally.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-3 bg-light border-0">
                                <label class="form-label fw-semibold small text-uppercase">Limit Per User</label>
                                <input type="number" class="form-control border-0 bg-transparent" name="user_limit"
                                    value="{{ old('user_limit') }}" placeholder="Unlimited">
                                <div class="extra-small text-muted mt-2">Maximum uses per individual guest.</div>
                            </div>
                        </div>

                        <hr class="my-2 opacity-25">

                        {{-- Dates & Restrictions --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Valid From</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="starts_from"
                                value="{{ old('starts_from', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Valid Until</label>
                            <input type="date" class="form-control bg-light border-0 py-2" name="ends_at"
                                value="{{ old('ends_at') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase">Target Country</label>
                            <select class="form-select bg-light border-0 py-2" name="country_id">
                                <option value="">All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Min Nights Required</label>
                            <input type="number" class="form-control bg-light border-0 py-2" name="min_nights"
                                value="{{ old('min_nights', 1) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Min Booking Amount</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0 py-2"
                                name="min_amount" value="{{ old('min_amount', '1.00') }}">
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 pb-4 px-4 mt-3">
                    <button class="btn btn-dark w-100 py-3 shadow-sm fw-bold" type="submit"
                        style="border-radius: 12px;">
                        <i class="bi bi-cloud-arrow-up me-2"></i> CREATE PROMOTION
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .bg-light { background-color: #f8f9fa !important; }
        .form-control:focus, .form-select:focus { background-color: #f2f2f2 !important; box-shadow: none; border: 1px solid #ddd; }
        .btn-check:checked+.btn-outline-dark { background-color: #212529 !important; color: #fff !important; font-weight: bold; }
        .form-check-input:checked { background-color: #198754; border-color: #198754; }
        .extra-small { font-size: 0.75rem; }
        .promo-seasonal.d-none { display: none !important; }
    </style>
@endsection

@push('scripts')
    <script>
        function togglePromoMode(mode) {
            const seasonalElements = document.querySelectorAll('.promo-seasonal');
            const codeInput = document.getElementById('coupen_code');
            const requiredCodeHidden = document.getElementById('required_code');
            const codeHint = document.getElementById('code_hint');

            if (mode === 'seasonal') {
                seasonalElements.forEach(el => el.classList.remove('d-none'));
                if(codeInput.value === '') {
                    codeInput.value = 'OFFER-' + Math.random().toString(36).substr(2, 5).toUpperCase();
                }
                if(codeHint) codeHint.innerText = "Internal identifier for this seasonal highlight.";
                requiredCodeHidden.value = '0';
            } else {
                seasonalElements.forEach(el => el.classList.add('d-none'));
                if(codeInput.value.startsWith('OFFER-')) {
                    codeInput.value = '';
                }
                if(codeHint) codeHint.innerText = "Guest must enter this code at checkout.";
                requiredCodeHidden.value = '1';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const requiredCode = document.getElementById('required_code').value;
            togglePromoMode(requiredCode === '0' ? 'seasonal' : 'coupon');
            
            const sw = document.getElementById('statusSwitch');
            const sl = document.getElementById('status_label');
            if(sw && sl) {
                sl.innerText = sw.checked ? 'Active' : 'Inactive';
            }
        });
    </script>
@endpush