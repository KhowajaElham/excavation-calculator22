<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExcavationController extends Controller
{
    public function index()
    {
        return view('calculator');
    }

    private function convertToInches($value, $unit)
    {
        $rates = [
            'in' => 1,
            'ft' => 12,
            'mm' => 1 / 25.4,
            'cm' => 1 / 25.4, // در صورت نیاز به سانتی‌متر می‌توانید از 0.3937 استفاده کنید
            'm'  => 39.3701,
        ];
        return (float)$value * ($rates[strtolower($unit)] ?? 1);
    }
   public function calculate(Request $request)
{
    $request->validate([
        'v_depth' => 'required|numeric|min:0',
        'dist_edge' => 'required|numeric|min:0',
        'v_unit' => 'required|in:mm,cm,m,in,ft',
        'dist_unit' => 'required|in:mm,cm,m,in,ft',
    ]);

    $V = $this->convertToInches($request->v_depth, $request->v_unit);
    $dist = $this->convertToInches($request->dist_edge, $request->dist_unit);


   
    if ($dist <= 18.0) {
        return $this->response(3);
    }

    $eff = $dist - 18.0;

    if ($dist <= 50.0) {
        $redLimit = $eff * 0.53;
        $yellowLimit = $eff * 0.50;
    } 
    elseif ($dist <= 100.0) {
        $redLimit = $eff * 0.55;
        $yellowLimit = $eff * 0.52;
    } 
    else {
        $redLimit = $eff * 0.86;
        $yellowLimit = $eff * 0.62;
    }

    if ($V >= $redLimit) {
        return $this->response(3);
    } 
    
    if ($V >= $yellowLimit) {
        return $this->response(2);
    }

    return $this->response(1);
}

private function response($zone) {
    $results = [
        1 => ['status' => 'ZONE 1', 'color' => 'safe-green'],
        2 => ['status' => 'ZONE 2', 'color' => 'caution-yellow'],
        3 => ['status' => 'ZONE 3', 'color' => 'danger-red'],
    ];
    return back()->with($results[$zone]);
}
}