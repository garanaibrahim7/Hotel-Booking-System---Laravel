<div class="container-fluid py-4">
    <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #3b82f6, #2563eb); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Users</p>
                        <h2 class="display-4 fw-bold mb-0">{{ $totalUsers ?? 0 }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-people" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #d6336c, #e83e8c); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Hotels</p>
                        <h2 class="display-4 fw-bold mb-0">{{ $totalHotels ?? 0 }}</h2>
                    </div>
                    <div class="position-absolute" style="right: -10px; bottom: -15px; opacity: 0.15; z-index: 1;">
                        <i class="bi bi-building" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm text-white h-100"
                style="background: linear-gradient(45deg, #22c55e, #16a34a); border-radius: 12px;">
                <div class="card-body p-4 position-relative overflow-hidden">
                    <div class="position-relative" style="z-index: 2;">
                        <p class="text-uppercase fw-semibold opacity-75 mb-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">Rooms</p>
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
    </div>
</div>
