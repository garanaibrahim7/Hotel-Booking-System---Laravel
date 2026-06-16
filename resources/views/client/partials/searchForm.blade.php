<div class="container" style="margin-top: 50px; position: relative; z-index: 20;">
    <div class="bg-white p-4 shadow-lg border-0">
        <form action="{{ route('client.rooms') }}" method="GET" class="row g-3 align-items-end text-dark">

            <div class="col-md-3">
                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Location</label>
                <input type="text" id="main_city_search" class="form-control border-0 bg-light p-3"
                    placeholder="Where are you going?" list="main_city_list"
                    value="{{ $cities->firstWhere('id', session('user_location.city_id'))->full_name ?? '' }}"
                    autocomplete="off">

                <input type="hidden" name="city_id" id="main_selected_city_id"
                    value="{{ session('user_location.city_id') }}">

                <datalist id="main_city_list">
                    @foreach ($cities as $city)
                        <option value="{{ $city->full_name }}"></option>
                    @endforeach
                </datalist>
            </div>


            <div class="col-md-3">
                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Stay Duration</label>
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

            <div class="col-md-3">
                <label class="small fw-bold text-uppercase text-secondary mb-2 d-block">Guests (per room)</label>
                <div class="d-flex gap-2">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light small text-muted">
                            <i class="bi bi-person-fill"></i></span>
                        <input type="number" name="adults" class="form-control border-0 bg-light p-3" min="1"
                            placeholder="Adults" value="{{ request('adults', 1) }}" title="Adults">
                    </div>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light small text-muted">
                            <i class="bi bi-person-arms-up"></i></span>
                        <input type="number" name="children" class="form-control border-0 bg-light p-3" min="0"
                            placeholder="Child" value="{{ request('children', 0) }}" title="Children">
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn-classic btn-classic-dark w-100 py-3" style="height: 56px;">
                    SEARCH
                </button>
            </div>
        </form>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cityInput = document.getElementById('main_city_search');
            const hiddenIdInput = document.getElementById('main_selected_city_id');

            const cities = {
                @foreach ($cities as $city)
                    "{{ $city->full_name }}": "{{ $city->id }}",
                @endforeach
            };

            cityInput.addEventListener('input', function() {
                const selectedValue = this.value;
                if (cities[selectedValue]) {
                    hiddenIdInput.value = cities[selectedValue];
                } else if (selectedValue === "") {
                    hiddenIdInput.value = "";
                }
            });
        });
    </script>
@endpush
