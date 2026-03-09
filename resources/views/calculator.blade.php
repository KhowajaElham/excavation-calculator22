<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excavation Zone Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { background-color: #f4f7f6; padding: 20px; font-family: 'Segoe UI', sans-serif; }
        .main-card { max-width: 600px; margin: auto; background: white; border: 2px solid #000; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card-header { background: #212529; color: white; text-align: center; padding: 12px; font-weight: bold; font-size: 1.1rem; }
        
       
        .result-box { padding: 15px; text-align: center; border-top: 2px solid #000; color: white; }
        .zone-1 { background-color: #28a745; }
        .zone-2 { background-color: #ffc107; color: #000; }
        .zone-3 { background-color: #dc3545; }

       
        .graph-wrapper { 
            padding: 10px; 
            background: #fff; 
            border-top: 1px solid #ddd; 
            height: 250px; 
        }
        canvas { 
            max-height: 100% !important; 
            width: 100% !important;
        }
    </style>
</head>
<body>

<div class="main-card">
    <div class="card-header text-uppercase">Excavation Zone Calculator</div>
    
    <div class="p-4">
        <form action="/calculate" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col-6 mb-2">
                    <label class="small fw-bold">Vertical Depth</label>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="v_depth" class="form-control" required>
                        <select name="v_unit" class="form-select">
                            <option value="in">in</option>
                            <option value="ft">ft</option>
                            <option value="mm">mm</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 mb-2">
                    <label class="small fw-bold">Dist. from EOT</label>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="dist_edge" class="form-control" required>
                        <select name="dist_unit" class="form-select">
                            <option value="in">in</option>
                            <option value="ft">ft</option>
                            <option value="mm">mm</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-dark w-100 btn-sm fw-bold mt-2">CALCULATE</button>
        </form>
    </div>

    @if(session('zone'))
        <div class="result-box {{ session('color') }}">
            <h5 class="fw-bold m-0">{{ session('status') }}</h5>
            <small style="font-size: 0.8rem;">{{ session('message') }}</small>
        </div>

        <div class="graph-wrapper">
            <canvas id="excavationChart"></canvas>
        </div>
    @endif
</div>

<script>
@if(session('zone'))
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('excavationChart').getContext('2d');
        
        const d = @json(session('dist_mm'));
        const v = @json(session('mm_value'));

        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Excavation',
                        data: [{x: d, y: -v}],
                        backgroundColor: 'black',
                        pointRadius: 6,
                    },
                    {
                        label: 'Zone 3',
                        data: [{x: 0, y: 0}, {x: 2500, y: -1800}, {x: 0, y: -1800}],
                        showLine: true,
                        fill: true,
                        backgroundColor: 'rgba(220, 53, 69, 0.15)',
                        borderColor: 'red',
                        borderWidth: 1.5,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    x: { 
                        min: 0, 
                        max: Math.max(d + 500, 2500),
                        ticks: { font: { size: 9 } }
                    },
                    y: { 
                        max: 0, 
                        min: Math.min(-v - 500, -1800),
                        ticks: { font: { size: 9 } }
                    }
                }
            }
        });
    });
@endif
</script>

</body>
</html>