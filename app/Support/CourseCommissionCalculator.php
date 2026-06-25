<?php

namespace App\Support;

use App\Models\Order;
use App\Models\RoomTypeHasCourse;
use App\Models\UserHasRoomTypeCommission;

class CourseCommissionCalculator
{
    public static function commissionForOrder(Order $order): float
    {
        if (!$order->ref_user_id || !$order->ref_room_type_id || !$order->service_laundry_cost) {
            return 0;
        }

        $courseId = filter_var($order->service_laundry_cost, FILTER_VALIDATE_INT);
        if (!$courseId) {
            return 0;
        }

        $userCommission = UserHasRoomTypeCommission::where('ref_user_id', $order->ref_user_id)
            ->where('ref_room_type_id', $order->ref_room_type_id)
            ->where('ref_course_id', $courseId)
            ->first();

        if ($userCommission && (float) $userCommission->price > 0) {
            return (float) $userCommission->price;
        }

        return (float) (RoomTypeHasCourse::where('ref_room_type_id', $order->ref_room_type_id)
            ->where('ref_course_id', $courseId)
            ->value('commission') ?? 0);
    }
}
