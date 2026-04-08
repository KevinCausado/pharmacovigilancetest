<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Order;
use App\Mail\PharmaceuticalAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AlertController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
            'lot_number'  => 'required|string',
        ]);

        $results = [];

        foreach ($request->order_ids as $orderId) {
            $order = Order::with('customer')->findOrFail($orderId);

            Mail::to($order->customer->email)
                ->send(new PharmaceuticalAlertMail($order->customer, $request->lot_number));

            Alert::create([
                'customer_id'       => $order->customer_id,
                'order_id'          => $order->id,
                'user_id'           => $request->user()->id,
                'lot_number'        => $request->lot_number,
                'notification_type' => 'email',
                'sent_at'           => now(),
            ]);

            $results[] = [
                'order_id' => $order->id,
                'customer' => $order->customer->name,
                'email'    => $order->customer->email,
                'status'   => 'sent',
            ];
        }

        return response()->json([
            'message' => count($results) . ' alert(s) sent successfully',
            'results' => $results,
        ]);
    }

    public function index()
    {
        $alerts = Alert::with(['customer', 'order', 'user'])
            ->orderBy('sent_at', 'desc')
            ->paginate(10);

        return response()->json($alerts);
    }
}
