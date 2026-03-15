<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExcavationController extends Controller
{
    public function index()
    {
        return view('calculator');
    }

    private function convertToMm($value, $unit)
    {
        $conversionRates = [
            'mm' => 1,
            'cm' => 10,
            'm'  => 1000,
            'in' => 25.4,
            'ft' => 304.8,
        ];
        return (float)$value * ($conversionRates[strtolower($unit)] ?? 1);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'v_depth' => 'required|numeric|min:0',
            'dist_edge' => 'required|numeric|min:0',
            'v_unit' => 'required|in:mm,cm,m,in,ft',
            'dist_unit' => 'required|in:mm,cm,m,in,ft',
        ]);

        $vDepthMm = $this->convertToMm($request->v_depth, $request->v_unit);
        $distRailMm = $this->convertToMm($request->dist_edge, $request->dist_unit);

        // Offset from Rail to Theoretical Ballast Toe (Approx 37 inches)
        // 19" (Rail to Tie edge) + 18" (Tie edge to Toe) = 37"
        $hOffsetMm = 37 * 25.4; 
        
        $zone = 1;

        if ($distRailMm <= $hOffsetMm) {
            $zone = 3;
        } else {
            $H = $distRailMm - $hOffsetMm;
            $V = $vDepthMm; // Using full depth from rail level as per Excel logic

            $ratio = $V / $H;

            if ($ratio >= 1.0) {
                $zone = 3; // Steeper than 1:1
            } elseif ($ratio >= 0.666) {
                $zone = 2; // Between 1.5:1 and 1:1
            } else {
                $zone = 1; // Flatter than 1.5:1
            }
        }

        $results = [
            3 => ["ZONE 3", "WARNING: EXCAVATION NOT ALLOWED. STOP WORK!", "zone-3"],
            2 => ["ZONE 2", "CAUTION: MONITOR EXCAVATION. SUPPORT REQUIRED.", "zone-2"],
            1 => ["ZONE 1", "SAFE: Excavation is within safe limits.", "zone-1"]
        ];

        return back()->with([
            'zone' => $zone,
            'status' => $results[$zone][0],
            'message' => $results[$zone][1],
            'color' => $results[$zone][2],
            'mm_value' => round($vDepthMm, 2),
            'dist_mm' => round($distRailMm, 2)
        ]);
    }
}