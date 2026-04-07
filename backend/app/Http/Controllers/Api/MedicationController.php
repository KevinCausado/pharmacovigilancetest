<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    /**
     * GET /api/medications/search?lot=951357&start_date=&end_date=
     * Returns medications matching the lot number.
     */
    public function search(Request $request)
    {
        $request->validate([
            'lot' => 'required|string',
        ]);

        $medications = Medication::where('lot_number', $request->lot)->get();

        if ($medications->isEmpty()) {
            return response()->json(['message' => 'No medications found for this lot number'], 404);
        }

        return response()->json($medications);
    }
}
