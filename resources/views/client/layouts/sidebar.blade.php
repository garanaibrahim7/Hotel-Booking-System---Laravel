<div class="filter-sidebar bg-white m-1 p-3 p-md-4 shadow-sm border rounded-0">
    <div class="d-flex justify-content-between align-items-center mb-0 mb-md-4 row">
        <div class="col-10" data-bs-toggle="collapse" data-bs-target="#filterContent" role="button">
            <h5 class="fw-bold m-0 headingfonts">
                <i class="bi bi-sliders2-vertical me-2 d-md-none"></i>Filters
            </h5>
        </div>
        <div class="d-flex align-items-center text-end col-2">
            <a href="{{ route('client.rooms') }}" class="text-decoration-none small text-danger fw-bold me-3">Clear</a>
        </div>
    </div>

    <div class="collapse d-md-block mt-3 mt-md-0" id="filterContent">
        <form action="{{ route('client.rooms') }}" method="GET" id="hiddenDateForm">

            <div class="filter-group mb-4">
                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Destination</label>
                <input type="hidden" name="city_id" id="selected_city_id" value="{{ request('city_id') }}">

                <div class="position-relative">
                    <input type="text" id="city_search" class="form-control border-0 bg-light p-3 shadow-none small"
                        placeholder="Search City..." list="city_list"
                        value="{{ $cities->firstWhere('id', request('city_id'))->full_name ?? '' }}" autocomplete="off">

                    <datalist id="city_list">
                        @foreach ($cities as $city)
                            <option value="{{ $city->full_name }}"></option>
                        @endforeach
                    </datalist>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25 d-none d-md-block">

            <div class="row mb-4">
                <input type="hidden" name="check_in" id="final_check_in"
                    value="{{ request('check_in', session('booking_check_in')) }}">
                <input type="hidden" name="check_out" id="final_check_out"
                    value="{{ request('check_out', session('booking_check_out')) }}">

                <div class="col-12">
                    <div class="position-relative p-2 bg-light d-flex align-items-center" id="dateEditTrigger"
                        style="height: 50px; cursor: pointer; border-radius: 5px;">
                        <i class="bi bi-calendar3 me-3" style="color: #bca47f; font-size: 1.2rem;"></i>

                        <div class="d-flex flex-column">
                            <label class="small fw-bold text-uppercase text-secondary mb-0"
                                style="font-size: 0.7rem;">Stay Duration</label>
                            <span class="small primaryfont text-dark fw-bold">
                                @php
                                    $in = request('check_in') ?: session('booking_check_in');
                                    $out = request('check_out') ?: session('booking_check_out');
                                @endphp
                                @if ($in && $out)
                                    {{ \Carbon\Carbon::parse($in)->format('d M') }} —
                                    {{ \Carbon\Carbon::parse($out)->format('d M') }}
                                @else
                                    Select Dates
                                @endif
                            </span>
                        </div>

                        <input type="text" id="flatpickr_input"
                            style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;">
                    </div>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25 d-none d-md-block">

            <div class="filter-group mb-4">
                <label class="small fw-bold text-uppercase text-secondary mb-3 d-block">Occupancy (per room)</label>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted small"><i
                                    class="bi bi-person-fill"></i></span>
                            <input type="number" name="adults" min="1" max="10"
                                onchange="this.form.submit()" class="form-control bg-light border-0 py-2 small"
                                placeholder="Adults" title="Adults" value="{{ request('adults', 1) }}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted small"><i
                                    class="bi bi-person-arms-up"></i></span>
                            <input type="number" name="children" min="0" max="10"
                                onchange="this.form.submit()" class="form-control bg-light border-0 py-2 small"
                                placeholder="Child" title="Children" value="{{ request('children', 0) }}">
                        </div>
                    </div>
                </div>
                <small class="text-muted mt-2 d-block" style="font-size: 0.65rem;">Max occupancy per room basis.</small>
            </div>

            <hr class="my-4 text-muted opacity-25 d-none d-md-block">

            <div class="filter-group mb-4">
                <label class="small fw-bold text-uppercase text-secondary mb-3 d-block">Price Per Night</label>
                <div class="d-flex gap-2">
                    <input type="number" name="min_price" onchange="this.form.submit()"
                        class="form-control form-control-sm bg-light border-0 py-2" placeholder="Min"
                        value="{{ request('min_price') }}">
                    <input type="number" name="max_price" onchange="this.form.submit()"
                        class="form-control form-control-sm bg-light border-0 py-2" placeholder="Max"
                        value="{{ request('max_price') }}">
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25 d-none d-md-block">

            <div class="row">
                <div class="col-6 col-md-12 mb-4">
                    <label class="small fw-bold text-uppercase text-secondary mb-3 d-block">Category</label>
                    @foreach (['Standard', 'Luxury', 'Suite', 'Deluxe', 'Premium'] as $cat)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="categories[]"
                                onchange="this.form.submit()" value="{{ strtolower($cat) }}"
                                id="cat_{{ $cat }}"
                                {{ in_array(strtolower($cat), (array) request('categories')) ? 'checked' : '' }}>
                            <label class="form-check-label small"
                                for="cat_{{ $cat }}">{{ $cat }}</label>
                        </div>
                    @endforeach
                </div>

                <div class="col-6 col-md-12 mb-4">
                    <label class="small fw-bold text-uppercase text-secondary mb-3 d-block">Room Type</label>
                    @foreach (['Single', 'Double', 'Twin', 'Family'] as $type)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="room_types[]"
                                onchange="this.form.submit()" value="{{ strtolower($type) }}"
                                id="type_{{ $type }}"
                                {{ in_array(strtolower($type), (array) request('room_types')) ? 'checked' : '' }}>
                            <label class="form-check-label small"
                                for="type_{{ $type }}">{{ $type }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-classic-dark w-100 py-2 mt-2 shadow-sm d-md-none">APPLY
                FILTERS</button>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cityInput = document.getElementById('city_search');
            const hiddenInput = document.getElementById('selected_city_id');
            const form = document.getElementById('hiddenDateForm');

            const cityMap = {
                @foreach ($cities as $city)
                    "{{ $city->full_name }}": "{{ $city->id }}",
                @endforeach
            }

            cityInput.addEventListener('input', function(e) {
                const val = this.value;

                if (cityMap[val]) {
                    hiddenInput.value = cityMap[val];
                    form.submit();
                } else if (val === "") {
                    hiddenInput.value = "";
                    form.submit();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            const input = document.getElementById('flatpickr_input');
            const checkIn = document.getElementById('final_check_in');
            const checkOut = document.getElementById('final_check_out');

            if (!input) return;

            flatpickr(input, {
                mode: "range",
                dateFormat: "Y-m-d",
                minDate: "today",

                onChange: function(selectedDates, dateStr, instance) {

                    if (selectedDates.length === 1) {
                        const inDate = selectedDates[0];

                        // allow only future checkout
                        instance.set('minDate', inDate.fp_incr(1));

                        checkIn.value = instance.formatDate(inDate, "Y-m-d");
                        checkOut.value = "";
                    }

                    if (selectedDates.length === 2) {
                        const inDate = selectedDates[0];
                        const outDate = selectedDates[1];

                        if (outDate <= inDate) {
                            alert("Invalid dates");
                            instance.clear();
                            return;
                        }

                        checkIn.value = instance.formatDate(inDate, "Y-m-d");
                        checkOut.value = instance.formatDate(outDate, "Y-m-d");
                    }
                }
            });

        });
    </script>
@endpush
