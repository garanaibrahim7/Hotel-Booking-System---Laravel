@extends('layouts.adminlte')

@php
    // Determine if we are in Edit Mode or Create Mode
    $isEdit = isset($plan);
    $title = $isEdit ? 'Edit Subscription Plan' : 'Create Subscription Plan';
    $subtitle = $isEdit
        ? "Update configuration for plan #{$plan->id}"
        : 'Configure premium tier details and automated recurring parameters';

    // Set up form parameters dynamically
    $actionUrl = $isEdit ? route('admin.subscription.update', $plan->id) : route('admin.subscription.store');
    $submitButtonText = $isEdit ? 'UPDATE PLAN' : 'CREATE PLAN';
@endphp

@section('title', $title)

@section('content')
    <div class="container-fluid py-5">
        <div class="card border-0 shadow-sm m-auto" style="max-width: 900px; border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                <h3 class="fw-bold text-dark mb-0">{{ $title }}</h3>
                <p class="text-muted small">{{ $subtitle }}</p>
            </div>

            <form action="{{ $actionUrl }}" method="POST">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="card-body px-4 py-4 bg-white">
                    <h6 class="fw-bold text-uppercase text-secondary mb-3 mt-2" style="letter-spacing: 1px;">Plan Details
                    </h6>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Plan Name (Label)</label>
                            <input type="text"
                                class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $isEdit ? $plan->name : '') }}"
                                placeholder="e.g., Gold Elite" required>
                            @error('name')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Billing Cycle Type</label>
                            <select name="type"
                                class="form-select bg-light border-0 py-2 @error('type') is-invalid @enderror" required>
                                <option value="">Select Billing Frequency</option>
                                @php
                                    $currentType = old('type', $isEdit ? $plan->type : '');
                                @endphp
                                <option value="monthly" {{ $currentType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="3 months" {{ $currentType == '3 months' ? 'selected' : '' }}>3 Months
                                </option>
                                <option value="6 months" {{ $currentType == '6 months' ? 'selected' : '' }}>6 Months
                                </option>
                                <option value="yearly" {{ $currentType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="lifetime" {{ $currentType == 'lifetime' ? 'selected' : '' }}>Lifetime
                                    (One-Time)</option>
                            </select>
                            @error('type')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-uppercase text-secondary mb-3 mt-4" style="letter-spacing: 1px;">Financial
                        Configuration</h6>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Price Amount</label>
                            <input type="number" step="0.01"
                                class="form-control bg-light border-0 py-2 @error('price') is-invalid @enderror"
                                name="price" value="{{ old('price', $isEdit ? $plan->price : '') }}" placeholder="0.0000"
                                required>
                            @error('price')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-uppercase">Select Currency</label>
                            <select id="currency_selector" class="form-select bg-light border-0 py-2" required
                                onchange="updateCurrencyFields()">
                                <option value="" data-symbol="">Choose currency...</option>
                                @php
                                    $currentCurrency = old('currency', $isEdit ? $plan->currency : 'USD');
                                @endphp
                                @foreach ($currencies as $code => $symbol)
                                    <option value="{{ $code }}" data-symbol="{{ $symbol }}"
                                        {{ $currentCurrency == $code ? 'selected' : '' }}>
                                        {{ strtoupper($code) }} ({{ $symbol }})
                                    </option>
                                @endforeach
                            </select>

                            <input type="hidden" name="currency" id="hidden_currency"
                                value="{{ old('currency', $isEdit ? $plan->currency : 'USD') }}">
                            <input type="hidden" name="currency_symbol" id="hidden_currency_symbol"
                                value="{{ old('currency_symbol', $isEdit ? $plan->currency_symbol : '$') }}">

                            @error('currency')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <h6 class="fw-bold text-uppercase text-secondary mb-3 mt-5" style="letter-spacing: 1px;">Plan Facilities
                        & Features</h6>

                    <div class="p-3 rounded-3 bg-light border-0">
                        <label class="form-label fw-semibold small text-uppercase mb-2">Add Facilities</label>
                        <div class="input-group mb-3 shadow-sm rounded-3 overflow-hidden">
                            <input type="text" id="facility_input" class="form-control border-0 py-2 bg-white"
                                placeholder="e.g., Free Lounge Access, 10% Discount on Luxury Suites">
                            <button class="btn btn-dark px-4 fw-bold" type="button" onclick="addFacilityRow()">
                                <i class="bi bi-plus-lg me-1"></i> Add
                            </button>
                        </div>

                        <div id="facilities_wrapper" class="d-flex flex-column gap-2 mt-2">
                            @php
                                // Handle data persistence from either validation recovery OR old database record array
                                $oldFacilities = old(
                                    'facilities',
                                    $isEdit
                                        ? (is_array($plan->facilities)
                                            ? $plan->facilities
                                            : json_decode($plan->facilities, true) ?? [])
                                        : [],
                                );
                            @endphp

                            @if (!empty($oldFacilities))
                                @foreach ($oldFacilities as $facility)
                                    <div
                                        class="d-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border-0 shadow-sm transition-all animate-fade-in">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-check-circle-fill text-success small"></i>
                                            <span class="text-dark small fw-semibold">{{ $facility }}</span>
                                            <input type="hidden" name="facilities[]" value="{{ $facility }}">
                                        </div>
                                        <button type="button"
                                            class="btn btn-link text-danger p-0 border-0 text-decoration-none"
                                            onclick="this.parentElement.remove(); checkHintVisibility();">
                                            <i class="bi bi-x-circle fs-5"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div id="no_facilities_hint"
                            class="text-center text-muted small py-2 {{ !empty($oldFacilities) ? 'd-none' : '' }}">
                            No custom features configured yet. Included perks will show here.
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 pb-4 px-4 mt-3 d-flex gap-3">
                    <a href="{{ route('admin.subscription.index') }}"
                        class="btn btn-light px-4 py-3 rounded-pill fw-bold w-25">Cancel</a>
                    <button class="btn btn-dark w-75 py-3 shadow-sm fw-bold rounded-pill" type="submit">
                        <i class="bi bi-check-circle me-2"></i> {{ $submitButtonText }}
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

        .animate-fade-in {
            animation: fadeInItem 0.25s ease-out forwards;
        }

        @keyframes fadeInItem {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        function updateCurrencyFields() {
            const selector = document.getElementById('currency_selector');
            const selectedOption = selector.options[selector.selectedIndex];

            const code = selectedOption.value;
            const symbol = selectedOption.getAttribute('data-symbol') || '';

            document.getElementById('hidden_currency').value = code;
            document.getElementById('hidden_currency_symbol').value = symbol;
        }

        function addFacilityRow() {
            const input = document.getElementById('facility_input');
            const wrapper = document.getElementById('facilities_wrapper');
            const hint = document.getElementById('no_facilities_hint');
            const text = input.value.trim();

            if (!text) return;

            const row = document.createElement('div');
            row.className =
                'd-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border-0 shadow-sm animate-fade-in';
            row.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success small"></i>
                    <span class="text-dark small fw-semibold">${text}</span>
                    <input type="hidden" name="facilities[]" value="${text}">
                </div>
                <button type="button" class="btn btn-link text-danger p-0 border-0 text-decoration-none" onclick="removeFacilityRow(this)">
                    <i class="bi bi-x-circle fs-5"></i>
                </button>
            `;

            wrapper.appendChild(row);
            input.value = '';
            hint.classList.add('d-none');
        }

        function removeFacilityRow(button) {
            button.parentElement.remove();
            checkHintVisibility();
        }

        function checkHintVisibility() {
            const wrapper = document.getElementById('facilities_wrapper');
            const hint = document.getElementById('no_facilities_hint');
            if (wrapper.children.length === 0) {
                hint.classList.remove('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Only pull values dynamically on load if hidden parameters are not populated
            if (!document.getElementById('hidden_currency').value) {
                updateCurrencyFields();
            }
        });
    </script>
@endpush
