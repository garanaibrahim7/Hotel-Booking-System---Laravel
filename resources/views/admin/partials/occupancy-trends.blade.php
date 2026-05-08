<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">Occupancy Trend</h5>
        <small class="text-muted">Percentage of rooms occupied daily</small>
    </div>
    <div class="card-body">
        <div id="occupancy-trend-chart"></div>
    </div>
</div>

@push('afterLoadScripts')
    var occupancyOptions = {
    series: [{
    name: 'Occupancy %',
    data: @json($occupancyData)
    }],
    chart: {
    height: 300,
    type: 'line',
    toolbar: {
    show: false
    }
    },
    stroke: {
    width: 4,
    curve: 'smooth'
    },
    colors: ['#6610f2'],
    fill: {
    type: 'gradient',
    gradient: {
    shade: 'dark',
    gradientToColors: ['#3b82f6'],
    shadeIntensity: 1,
    type: 'horizontal',
    opacityFrom: 1,
    opacityTo: 1,
    stops: [0, 100, 100, 100]
    },
    },
    xaxis: {
    categories: @json($occupancyLabels),
    },
    yaxis: {
    max: 100,
    labels: {
    formatter: (val) => val + "%"
    }
    }
    };

    new ApexCharts(document.querySelector("#occupancy-trend-chart"), occupancyOptions).render();
@endpush
