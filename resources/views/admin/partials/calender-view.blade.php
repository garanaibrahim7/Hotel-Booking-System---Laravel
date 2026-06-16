@push('styles')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <style>
        .fc-event {
            cursor: pointer;
            border: none;
            margin-bottom: 2px;
        }

        .fc-daygrid-day-number {
            font-weight: bold;
            color: #495057;
        }

        .fc-day-today {
            background-color: #f8f9fa !important;
        }

        .fc a {
            text-decoration: none !important;
        }
    </style>
@endpush

<div class="container-fluid pt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold mb-0">
                <i class="bi bi-calendar-range me-2"></i> Availability & Operations Calendar
            </h3>
            <div class="d-flex gap-2">
                {{-- <input type="hidden" id="filter_room_detail_id" value="1"> <input type="hidden" id="filter_room_id" value=""> --}}
            </div>
        </div>
        <div class="card-body p-4">
            <div id="availability-calendar"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingsListModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Bookings for <span id="modal-booking-date"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-booking-content">
                <div class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div>Loading bookings...
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="detailModalTitle">Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalContent">
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('availability-calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                headerToolbar: {
                    right: 'today prev,next',
                    left: '',
                    center: 'title',
                },
                height: 750,


                events: function(fetchInfo, successCallback, failureCallback) {
                    let roomDetailId = document.getElementById('filter_room_detail_id').value;
                    let roomId = document.getElementById('filter_room_id').value;

                    $.ajax({
                        url: "{{ route('admin.calendar-data') }}",
                        type: 'GET',
                        data: {
                            start: fetchInfo.startStr,
                            end: fetchInfo.endStr,
                            room_detail_id: roomDetailId,
                            room_id: roomId
                        },
                        success: function(res) {
                            successCallback(res);
                        },
                        error: function() {
                            failureCallback();
                            alert('Error fetching calendar data.');
                        }
                    });
                },

                eventContent: function(arg) {
                    let italicEl = document.createElement('div');
                    italicEl.classList.add('px-2', 'py-1', 'rounded', 'text-truncate', 'small',
                        'fw-bold');

                    if (arg.event.extendedProps.type === 'info') {
                        italicEl.innerHTML =
                            `<i class="bi bi-check-circle me-1"></i> ${arg.event.title}`;
                        italicEl.style.color = arg.event.textColor;
                    } else {
                        italicEl.innerHTML = arg.event.title;
                        italicEl.style.backgroundColor = arg.event.backgroundColor;
                        italicEl.style.color = '#fff';
                    }

                    return {
                        domNodes: [italicEl]
                    };
                },

                eventClick: function(info) {
                    let props = info.event.extendedProps;

                    if (props.type === 'info') return;

                    if (props.url) {
                        window.location.href = props.url;
                    }
                }
            });

            calendar.render();
        });
    </script>
@endpush
