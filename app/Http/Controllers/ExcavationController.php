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

        $unit = strtolower($unit);
      
        return (float)$value * ($conversionRates[$unit] ?? 1);
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
        $distEdgeMm = $this->convertToMm($request->dist_edge, $request->dist_unit);

        
        $zone = 1;
        $status = "ZONE 1";
        $message = "SAFE: Excavation is within safe limits.";
        $colorClass = "zone-1";

       if ($distEdgeMm == 0 && $vDepthMm > 0) {
            $zone = 3;
        } else {
          
        if ($vDepthMm > ($distEdgeMm * 0.7)) {
                $zone = 3;
            } elseif ($vDepthMm > ($distEdgeMm * 0.4)) {
                $zone = 2;
            }
        }

        if ($zone === 3) {
            $status = "ZONE 3";
            $message = "WARNING: EXCAVATION IS NOT ALLOWED UNDER TRAIN LOAD. STOP WORK IMMEDIATELY!";
            $colorClass = "zone-3";
        } elseif ($zone === 2) {
            $status = "ZONE 2";
            $message = "CAUTION: MONITOR EXCAVATION. SUPPORT MAY BE REQUIRED.";
            $colorClass = "zone-2";
        }

       
        return back()->with([
            'zone' => $zone,
            'status' => $status,
            'message' => $message,
            'color' => $colorClass,
            'mm_value' => round($vDepthMm, 2), 
            'dist_mm' => round($distEdgeMm, 2)
        ]);
    }
}