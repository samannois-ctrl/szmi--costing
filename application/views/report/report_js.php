<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script type="text/javascript">

// Year/Month change handler
function change_select_year_month(el) {
	var select_year = $('#select-year').val();
	var select_month = $('#select-month').val();
	var url = '<?php echo base_url('cost/report') ?>' + '?year=' + select_year + '&month=' + select_month;
	window.location.href = url;
}

<?php if ($is_file_completed && $is_calc_fc) { ?>

// Prepare data from PHP
var trendData = <?php echo json_encode($trend); ?>;
var machineData = <?php echo json_encode($by_machine); ?>;

// Initialize charts when document is ready
$(document).ready(function() {
    initTrendChart();
    initMachineChart();
});

// Trend Line Chart
function initTrendChart() {
    var ctx = document.getElementById('trendChart').getContext('2d');
    
    var labels = [];
    var data = [];
    
    trendData.forEach(function(item) {
        labels.push(item.month_label);
        data.push(item.avg_margin);
    });
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Margin %',
                data: data,
                borderColor: '#0533b5',
                backgroundColor: 'rgba(5, 51, 181, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: '#0533b5',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Margin: ' + context.parsed.y.toFixed(2) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Machine Bar Chart
function initMachineChart() {
    var ctx = document.getElementById('machineChart').getContext('2d');
    
    var labels = [];
    var data = [];
    var backgroundColors = [
        'rgba(5, 51, 181, 0.8)',
        'rgba(37, 99, 235, 0.8)',
        'rgba(59, 130, 246, 0.8)',
        'rgba(96, 165, 250, 0.8)',
        'rgba(147, 197, 253, 0.8)'
    ];
    var borderColors = [
        '#0533b5',
        '#2563eb',
        '#3b82f6',
        '#60a5fa',
        '#93c5fd'
    ];
    
    machineData.forEach(function(item) {
        labels.push(item.machine_name);
        data.push(item.avg_margin);
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Margin %',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Margin: ' + context.parsed.y.toFixed(2) + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

<?php } ?>

</script>