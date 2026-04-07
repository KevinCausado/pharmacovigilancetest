<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'lot'        => 'required|string',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ?? now()->subDays(30)->toDateString();
        $endDate   = $request->end_date   ?? now()->toDateString();

        $orders = Order::with(['customer', 'items.medication'])
            ->whereHas('items.medication', function ($query) use ($request) {
                $query->where('lot_number', $request->lot);
            })
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->orderBy('purchase_date', 'desc')
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(string $id)
    {
        $order = Order::with(['customer', 'items.medication'])->findOrFail($id);

        return response()->json($order);
    }
}
