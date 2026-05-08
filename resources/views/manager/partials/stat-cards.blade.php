<div class="container-fluid py-4">
    <div class="row g-3">

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #22c55e, #16a34a); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Rooms Available</p>
                        <h2 class="display-4 fw-bold mb-0">{{ $totalRooms ?? 0 }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-door-open" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #f59e0b, #d97706); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Today's Bookings</p>
                        <h2 class="display-4 fw-bold mb-0">{{ $todaysBookings ?? 0 }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-calendar-check" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #6610f2, #6f42c1); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">All Time Bookings</p>
                        <h2 class="display-5 fw-bold mb-0">{{ $totalBookings ?? 0 }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-journal-check" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #0dcaf0, #0aa2c0); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Total Revenue</p>
                        <h2 class="display-5 fw-bold mb-0">
                            {{ $adminCountry['currency_symbol'] ?? '$' }}{{ number_format($totalRevenue ?? 0, 2) }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-currency-{{ $adminCountry['currency_code'] ?? 'USD' == 'INR' ? 'rupee' : 'dollar' }}"
                            style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #fd7e14, #f76707); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Upcoming Stays</p>
                        <h2 class="display-5 fw-bold mb-0">{{ $upcomingStays ?? 0 }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-clock-history" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
