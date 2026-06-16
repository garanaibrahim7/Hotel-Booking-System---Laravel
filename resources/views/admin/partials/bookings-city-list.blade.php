    <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 475px;">

        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Booking Count From Cities</h5>
            {{-- <div class="dropdown">
                <button class="btn btn-link btn-sm text-muted text-decoration-none dropdown-toggle" type="button"
                    data-bs-toggle="dropdown">
                    Today
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Yesterday</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                </ul>
            </div> --}}
        </div>

        <div class="card-body p-0 overflow-auto" style="max-height: 400px; padding-right: 4px;">

            <table class="table table-hover align-middle mb-0">
                {{-- <thead class="bg-light"> --}}
                <thead class="bg-light sticky-top">
                    <tr>
                        <th class="px-4 py-3 text-muted fw-semibold" style="font-size: 0.75rem;">#</th>
                        <th class="py-3 text-muted fw-semibold" style="font-size: 0.75rem;">CITY</th>
                        <th class="px-4 py-3 text-end text-muted fw-semibold" style="font-size: 0.75rem;">
                            BOOKINGS
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($topCities as $key => $city)
                        <tr>
                            <td class="px-4 border-0 text-muted">{{ $key + 1 }}</td>
                            <td class="border-0 fw-medium">{{ $city->name }}</td>
                            <td class="px-4 border-0 text-end fw-bold">{{ $city->bookings_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 border-0 text-muted text-center" colspan="3">No Bookings Found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
