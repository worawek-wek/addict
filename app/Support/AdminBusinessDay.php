<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminBusinessDay
{
    public const START_TIME = '10:01';
    public const END_TIME = '10:00';
    public const DEFAULT_PER_PAGE = 100;

    public static function defaultPerPage($value = null): int
    {
        return (int) ($value ?: self::DEFAULT_PER_PAGE);
    }

    public static function currentRange(?Carbon $now = null): array
    {
        $now = $now ? $now->copy() : Carbon::now();
        $endToday = $now->copy()->setTime(10, 0, 59);

        if ($now->lessThanOrEqualTo($endToday)) {
            return [
                $now->copy()->subDay()->setTime(10, 1, 0),
                $endToday,
            ];
        }

        return [
            $now->copy()->setTime(10, 1, 0),
            $now->copy()->addDay()->setTime(10, 0, 59),
        ];
    }

    public static function rangeFromRequest(
        Request $request,
        bool $defaultToCurrent = true,
        string $startDateKey = 'start_date',
        string $endDateKey = 'end_date',
        string $startTimeKey = 'start_time_filter',
        string $endTimeKey = 'end_time_filter'
    ): array {
        if (!$request->filled($startDateKey)) {
            return $defaultToCurrent ? self::currentRange() : [null, null];
        }

        $startDate = self::parseDate($request->input($startDateKey));
        $endDate = self::parseDate($request->input($endDateKey) ?: $request->input($startDateKey));

        [$startHour, $startMinute] = self::parseTime($request->input($startTimeKey, self::START_TIME));
        [$endHour, $endMinute] = self::parseTime($request->input($endTimeKey, self::END_TIME));

        $startDate->setTime($startHour, $startMinute, 0);
        $endDate->setTime($endHour, $endMinute, 59);

        if (!$request->filled($endTimeKey)) {
            $endDate->addDay();
        }

        if ($endDate->lessThanOrEqualTo($startDate)) {
            $endDate->addDay();
        }

        return [$startDate, $endDate];
    }

    public static function singleDateRange($date): array
    {
        $start = self::parseDate($date)->setTime(10, 1, 0);
        $end = $start->copy()->addDay()->setTime(10, 0, 59);

        return [$start, $end];
    }

    public static function rangeForPresetDays(int $days): array
    {
        [$currentStart, $currentEnd] = self::currentRange();

        return [
            $currentStart->copy()->subDays(max(0, $days - 1)),
            $currentEnd,
        ];
    }

    public static function isOrderInCurrentRange(Order $order, bool $allowUnpaid = false): bool
    {
        if ($allowUnpaid && (int) $order->payment_status === 0) {
            return true;
        }

        $orderDateTime = self::orderDateTime($order);
        if (!$orderDateTime) {
            return false;
        }

        [$start, $end] = self::currentRange();

        return $orderDateTime->between($start, $end, true);
    }

    public static function orderDateTime(Order $order): ?Carbon
    {
        if ($order->paid_at) {
            return Carbon::parse($order->paid_at);
        }

        if ($order->booking_date && $order->start_time) {
            return Carbon::parse($order->booking_date . ' ' . $order->start_time);
        }

        if ($order->created_at) {
            return Carbon::parse($order->created_at);
        }

        return null;
    }

    public static function sqlRange(array $range): array
    {
        return [
            $range[0]->format('Y-m-d H:i:s'),
            $range[1]->format('Y-m-d H:i:s'),
        ];
    }

    private static function parseDate($value): Carbon
    {
        $value = (string) $value;
        $format = str_contains($value, '/') ? 'd/m/Y' : 'Y-m-d';

        return Carbon::createFromFormat($format, $value)->startOfDay();
    }

    private static function parseTime($value): array
    {
        [$hour, $minute] = array_pad(explode(':', (string) $value), 2, 0);

        return [(int) $hour, (int) $minute];
    }
}
