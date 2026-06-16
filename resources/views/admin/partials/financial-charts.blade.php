<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 text-dark">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Financial Performance</h5>
                <small class="text-muted" id="fin-subtitle">Loading financial data...</small>
            </div>
            <select id="finViewSelector" class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-dark">
                <option value="daily" selected>Last 30 Days</option>
                <option value="weekly">Last 12 Weeks</option>
                <option value="monthly">Last 12 Months</option>
            </select>
        </div>
    </div>
    <div class="card-body px-2 pb-2">
        <div id="financial-performance-chart"></div>
    </div>
</div>


@push('afterLoadScripts')
    var finChart;

    var finOptions = {
    series: [],
    chart: {
    type: 'bar',
    height: 350,
    toolbar: { show: false }
    },
    plotOptions: {
    bar: {
    columnWidth: '70%',
    borderRadius: 4,
    dataLabels: {
    position: 'top', // Put the amount on top of the bar
    },
    }
    },
    dataLabels: {
    enabled: true,
    formatter: function (val) {
    // Shorten numbers on top of bars (e.g., 1.2M)
    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
    if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
    return val > 0 ? val : '';
    },
    offsetY: -20,
    style: { fontSize: '10px', colors: ["#304758"] }
    },
    xaxis: {
    categories: [],
    labels: {
    show: true,
    rotate: -45, // Rotate to prevent overlapping
    style: { fontSize: '11px' }
    },
    axisBorder: { show: false },
    },
    yaxis: {
    labels: {
    formatter: function (val) {
    // Shorten Y-axis labels
    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
    if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
    return val;
    }
    }
    },
    tooltip: {
    y: {
    formatter: function(val) {
    return val.toLocaleString() + " INR";
    }
    }
    }
    };

    finChart = new ApexCharts(document.querySelector("#financial-performance-chart"), finOptions);
    finChart.render();

    function updateFinChart(filterType) {
    document.querySelector("#fin-subtitle").innerText = "Updating...";

    $.ajax({
        url: "{{ route('admin.financial-chart') }}",
        type: 'GET',
        data: { filter: filterType },
        success: function(res) {
            document.querySelector("#fin-subtitle").innerText = res.subtitle;

            // USE updateOptions to refresh everything at once
            finChart.updateOptions({
                colors: ['#3b82f6', '#f43f5e'],
                xaxis: {
                    categories: res.categories,
                    labels: { show: true, rotate: -45 }
                },
                series: [
                    { name: 'Revenue', data: res.revenue },
                    { name: 'Refunds', data: res.refunds }
                ],
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                        if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
                        return val > 0 ? val : '';
                    },
                    offsetY: -20
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
                            return val;
                        }
                    }
                }
            });
        }
    });
}

    // Initial Load
    $(document).ready(function() {
    updateFinChart('daily');

    $('#finViewSelector').on('change', function() {
    updateFinChart($(this).val());
    });
    });
@endpush
