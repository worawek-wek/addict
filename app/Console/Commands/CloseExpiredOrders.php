<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseExpiredOrders extends Command
{
    protected $signature   = 'orders:close-expired';
    protected $description = 'Set ref_status_id = 3 for room orders whose end_time has passed';

    public function handle(): int
    {
        // $now  = Carbon::now();
        // $date = $now->toDateString();
        // $time = $now->format('H:i:s');

        // $updated = Order::where('ref_status_id', 2)
        //     ->whereDate('booking_date', $date)
        //     ->whereTime('end_time', '<', $time)
        //     ->whereNotNull('end_time')
        //     ->update(['ref_status_id' => 3]);

        // $this->info("Closed {$updated} expired order(s).");

        // return self::SUCCESS;
    }
}
