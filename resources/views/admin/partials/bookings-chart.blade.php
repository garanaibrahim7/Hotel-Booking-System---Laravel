<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0">Bookings Comparison</h5>
                <small class="text-muted" id="chart-subtitle">Monthly performance vs Last Year</small>
            </div>
            <!-- View Selector Dropdown -->
            <select id="chartViewSelector" class="form-select form-select-sm w-auto border-0 bg-light fw-bold">
                <option value="monthly">Monthly</option>
                <option value="weekly">Weekly</option>
                <option value="daily">Daily</option>
            </select>
        </div>
    </div>
    <div class="card-body px-2 pb-2">
        <div id="bookings-comparison-chart"></div>
    </div>
</div>


@push('afterLoadScripts')
   var chartOptions = {
        series: [{ name: 'Current Period', data: [] }, { name: 'Last Period', data: [] }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            animations: { enabled: true }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#3b82f6', '#22c55e'],
        fill: {
            type: 'gradient',
            gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
        },
        xaxis: {
            categories: [],
            labels: { rotate: -45, style: { fontSize: '10px' } }
        },
        legend: { position: 'top', horizontalAlign: 'right' },
        noData: {
            text: 'Loading Ledger Data...',
            style: { color: '#64748b', fontSize: '14px' }
        }
    };

    var chart = new ApexCharts(document.querySelector("#bookings-comparison-chart"), chartOptions);
    chart.render();

    // 2. The AJAX Function
    function updateChart(filterType) {
        document.querySelector("#chart-subtitle").innerText = "Syncing...";

        $.ajax({
            url: "{{ route('admin.booking-chart') }}", // Double check this route!
            type: 'GET',
            data: { filter: filterType },
            success: function(res) {
                if (res) {
                    document.querySelector("#chart-subtitle").innerText = res.subtitle;

                    // Update both categories and series at once
                    chart.updateOptions({
                        xaxis: { categories: res.categories },
                        series: [
                            { name: 'Current Period', data: res.current },
                            { name: 'Last Period', data: res.last }
                        ]
                    });
                }
            },
            error: function(err) {
                console.error("AJAX Error:", err);
                document.querySelector("#chart-subtitle").innerText = "Error loading data.";
            }
        });
    }

    // 3. Initial Load (Daily)
    $(document).ready(function() {
        updateChart('daily');

        // 4. Dropdown Change Listener
        $('#chartViewSelector').on('change', function() {
            updateChart($(this).val());
        });
    });
@endpush
