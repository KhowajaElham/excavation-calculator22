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

      $dist_eff = $dist - 20;
    $ratio = ($dist_eff > 0) ? ($V / $dist_eff) : 0;

    if ($dist < 20) {
        $zone = 3; 
    } 
    else {
        if ($dist >= 75) {
            if ($ratio >= 0.70) $zone = 3; 
            elseif ($ratio >= 0.54) $zone = 2; 
            else $zone = 1; 
        } 
        elseif ($dist >= 62.9) {
            if ($ratio >= 0.48) $zone = 3; 
            else $zone = 1; 
        } 
        else {
            if (round($dist, 1) == 30.0) {
                // اصلاح نهایی: نسبت ۰.۵۹۰ هنوز زرد است، پس مرز قرمز >= 0.60 است
                if ($ratio >= 0.60) $zone = 3; 
                else $zone = 2; // حذف زون سبز در فاصله ۳۰ اینچ
            } 
            else {
                if ($ratio >= 0.54) $zone = 3; 
                elseif ($ratio >= 0) $zone = 2; 
                else $zone = 1; 
            }
        }
    }

    $results = [
        1 => ['status' => 'ZONE 1', 'color' => 'safe-green'],
        2 => ['status' => 'ZONE 2', 'color' => 'caution-yellow'],
        3 => ['status' => 'ZONE 3', 'color' => 'danger-red'],
    ];

    return back()->with([
        'status' => $results[$zone]['status'],
        'color' => $results[$zone]['color']
    ]);
}
}