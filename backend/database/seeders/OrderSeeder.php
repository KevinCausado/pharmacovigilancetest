<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Medication;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $targetMedication = Medication::where('lot_number', '951357')->first();
        $otherMedication  = Medication::where('lot_number', '123456')->first();

        $recentOrders = [
            ['customer_id' => 1, 'purchase_date' => now()->subDays(1)->toDateString()],
            ['customer_id' => 2, 'purchase_date' => now()->subDays(2)->toDateString()],
            ['customer_id' => 3, 'purchase_date' => now()->subDays(3)->toDateString()],
            ['customer_id' => 4, 'purchase_date' => now()->subDays(5)->toDateString()],
            ['customer_id' => 5, 'purchase_date' => now()->subDays(7)->toDateString()],
            ['customer_id' => 1, 'purchase_date' => now()->subDays(9)->toDateString()],
            ['customer_id' => 2, 'purchase_date' => now()->subDays(11)->toDateString()],
            ['customer_id' => 3, 'purchase_date' => now()->subDays(13)->toDateString()],
            ['customer_id' => 4, 'purchase_date' => now()->subDays(15)->toDateString()],
            ['customer_id' => 5, 'purchase_date' => now()->subDays(18)->toDateString()],
            ['customer_id' => 1, 'purchase_date' => now()->subDays(22)->toDateString()],
            ['customer_id' => 2, 'purchase_date' => now()->subDays(27)->toDateString()],
        ];

        foreach ($recentOrders as $orderData) {
            $order = Order::create([
                'customer_id'   => $orderData['customer_id'],
                'purchase_date' => $orderData['purchase_date'],
                'status'        => 'completed',
            ]);

            OrderItem::create([
                'order_id'      => $order->id,
                'medication_id' => $targetMedication->id,
                'quantity'      => rand(1, 3),
            ]);
        }

        $oldOrder = Order::create([
            'customer_id'   => 5,
            'purchase_date' => now()->subDays(60)->toDateString(),
            'status'        => 'completed',
        ]);
        OrderItem::create([
            'order_id'      => $oldOrder->id,
            'medication_id' => $targetMedication->id,
            'quantity'      => 1,
        ]);

        $otherOrder = Order::create([
            'customer_id'   => 5,
            'purchase_date' => now()->subDays(3)->toDateString(),
            'status'        => 'completed',
        ]);
        OrderItem::create([
            'order_id'      => $otherOrder->id,
            'medication_id' => $otherMedication->id,
            'quantity'      => 2,
        ]);
    }
}
