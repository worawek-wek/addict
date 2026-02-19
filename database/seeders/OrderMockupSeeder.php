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

        $roomTypes = [8, 9, 12, 13, 14, 15];
        $paymentMethods = ['เงินสด', 'เครดิต', 'qr_code'];
        $statusIds = [1, 2, 3, 4];
        $courseIds = [1, 2, 3, 6, 7, 8];

        // Load all room_type_has_courses into a lookup array
        $rtcLookup = [];
        $rtcRows = \DB::table('room_type_has_courses')->get();
        foreach ($rtcRows as $rtc) {
            $rtcLookup[$rtc->ref_room_type_id][$rtc->ref_course_id] = [
                'price' => (float)$rtc->price,
                'coupon' => (float)$rtc->coupon
            ];
        }

        $created = 0;
        $maxOrders = 20;
        while ($created < $maxOrders) {
            $roomTypeId = $roomTypes[array_rand($roomTypes)];
            $courseId = $courseIds[array_rand($courseIds)];
            // Only use pairs that exist in lookup and have price+coupon > 0
            if (!isset($rtcLookup[$roomTypeId][$courseId])) continue;
            $stdPrice = $rtcLookup[$roomTypeId][$courseId]['price'];
            $stdCoupon = $rtcLookup[$roomTypeId][$courseId]['coupon'];
            $maxTotal = $stdPrice + $stdCoupon;
            if ($maxTotal <= 0) continue;

            $bookingDate = Carbon::now()->subDays(rand(0, 30))->format('Y-m-d');
            $startTime = sprintf('%02d:00:00', rand(8, 18));
            $endTimeHour = (int)substr($startTime, 0, 2) + rand(1, 3);
            $endTime = sprintf('%02d:00:00', $endTimeHour > 23 ? 23 : $endTimeHour);

            // total_price is 70-100% of maxTotal
            $totalPrice = rand((int)($maxTotal * 0.7), (int)$maxTotal);
            $price = $totalPrice + rand(0, 200); // base price >= total_price
            $discount = $price - $totalPrice;

            $order = Order::create([
                'order_number' => 'ORD-' . str_pad($created + 1, 6, '0', STR_PAD_LEFT),
                'ref_branch_id' => 1,
                'ref_customer_id' => rand(1, 5),
                'ref_user_id' => rand(1, 3),
                'ref_account_id' => 1,
                'ref_room_id' => rand(1, 10),
                'ref_room_type_id' => $roomTypeId,
                'ref_status_id' => $statusIds[array_rand($statusIds)],
                'service_laundry_cost' => $courseId,
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
            $created++;
        }

        $this->command->info('Created 20 mockup orders with addons and products for monthly sale report testing.');
    }
}
