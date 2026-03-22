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
        
        /* اصلاح کلاس‌های رنگی برای هماهنگی با کنترلر */
        .result-box { padding: 15px; text-align: center; border-top: 2px solid #000; color: white; }
        .danger-red { background-color: #dc3545 !important; }
        .caution-yellow { background-color: #ffc107 !important; color: #000 !important; }
        .safe-green { background-color: #28a745 !important; }

        .graph-wrapper { padding: 10px; background: #fff; border-top: 1px solid #ddd; height: 300px; }
        canvas { max-height: 100% !important; width: 100% !important; }
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
                            <option value="cm">cm</option>
                            <option value="m">m</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 mb-2">
                    <label class="small fw-bold">Dist. from Edge of Tie</label>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="dist_edge" class="form-control" required>
                        <select name="dist_unit" class="form-select">
                            <option value="in">in</option>
                            <option value="ft">ft</option>
                             <option value="cm">cm</option>
                            <option value="m">m</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-dark w-100 btn-sm fw-bold mt-2">CALCULATE</button>
        </form>
    </div>

    @if(session('status'))
        <div class="result-box {{ session('color') }}">
            <h5 class="fw-bold m-0">{{ session('status') }}</h5>
            <p class="m-0" style="font-size: 0.8rem;">{{ session('message') }}</p>
            <div class="mt-1 small">Ratio: {{ session('ratio') }} | V: {{ session('v_inch') }}" | Dist: {{ session('h_inch') }}"</div>
        </div>

        <div class="graph-wrapper">
            <canvas id="excavationChart"></canvas>
        </div>
    @endif
</div>

<script>
@if(session('status'))
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('excavationChart').getContext('2d');
        
        // استفاده از مقادیر اینچ برای رسم نمودار دقیق
        const d = {{ session('h_inch') }};
        const v = {{ session('v_inch') }};

        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Excavation Point',
                        data: [{x: d, y: -v}],
                        backgroundColor: 'black',
                        pointRadius: 8,
                        zIndex: 10
                    },
                    {
                        label: 'Zone 3 (1:1)',
                        data: [{x: 18, y: 0}, {x: 100, y: -82}], // نمونه خط شیب
                        showLine: true,
                        borderColor: 'red',
                        borderDash: [5, 5],
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Inches from Tie' }, min: 0 },
                    y: { title: { display: true, text: 'Depth (Inches)' }, max: 0 }
                }
            }
        });
    });
@endif
</script>
</body>
</html>