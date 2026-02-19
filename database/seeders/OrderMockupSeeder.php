<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderHasAddonOption;
use App\Models\OrderHasProduct;
use Carbon\Carbon;

class OrderMockupSeeder extends Seeder
{
    /**
     * Run the database seeds for monthly sale PDF testing.
     */
    public function run()
    {
        $roomTypes = [8, 9, 12, 13, 14, 15]; // Actual room type IDs from database
        $paymentMethods = ['เงินสด', 'เครดิต', 'qr_code'];
        $statusIds = [1, 2, 3, 4];
        $courseIds = [1, 2, 3, 6, 7, 8]; // Available course IDs

        // Create 20 mockup orders for different room types
        for ($i = 1; $i <= 20; $i++) {
            $bookingDate = Carbon::now()->subDays(rand(0, 30))->format('Y-m-d');
            $startTime = sprintf('%02d:00:00', rand(8, 18));
            $endTimeHour = (int)substr($startTime, 0, 2) + rand(1, 3);
            $endTime = sprintf('%02d:00:00', $endTimeHour > 23 ? 23 : $endTimeHour);

            $price = rand(500, 3000);
            $discount = rand(0, 500);
            $totalPrice = $price - $discount;

            $order = Order::create([
                'order_number' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'ref_branch_id' => 1,
                'ref_customer_id' => rand(1, 5),
                'ref_user_id' => rand(1, 3),
                'ref_account_id' => 1,
                'ref_room_id' => rand(1, 10),
                'ref_room_type_id' => $roomTypes[array_rand($roomTypes)],
                'ref_status_id' => $statusIds[array_rand($statusIds)],
                'service_laundry_cost' => $courseIds[array_rand($courseIds)], // Add course ID
                'booking_date' => $bookingDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $price,
                'discount' => $discount,
                'total_price' => $totalPrice,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
                'updated_at' => Carbon::now(),
            ]);

            // Add addon options (massage services)
            $addonCount = rand(1, 3);
            for ($j = 0; $j < $addonCount; $j++) {
                OrderHasAddonOption::create([
                    'ref_order_id' => $order->id,
                    'ref_option_id' => rand(1, 5),
                    'price' => rand(200, 800),
                    'coupon' => rand(0, 200),
                ]);
            }

            // Add products (drinks, snacks)
            $productCount = rand(0, 2);
            for ($k = 0; $k < $productCount; $k++) {
                OrderHasProduct::create([
                    'ref_order_id' => $order->id,
                    'ref_product_id' => rand(1, 10),
                    'quantity' => rand(1, 3),
                    'price' => rand(50, 200),
                ]);
            }
        }

        $this->command->info('Created 20 mockup orders with addons and products for monthly sale report testing.');
    }
}
