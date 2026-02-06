<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name'
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'room_types';

    public function room()
    {
        return $this->hasOne('App\Models\Room', 'id', 'ref_room_id');
    }
    public function room_type_has_course()
    {
        return $this->hasMany(
                \App\Models\RoomTypeHasCourse::class,
                'ref_room_type_id',
                'id'
            )
            ->join('courses', 'courses.id', '=', 'room_type_has_courses.ref_course_id')
            ->orderBy('courses.sort')
            ->select('room_type_has_courses.*');
    }
    public function user_has_room_type_commission()
    {
        return $this->hasMany('App\Models\UserHasRoomTypeCommission', 'ref_room_type_id', 'id');
    }

    public function user_has_room_type_commission_forty_minutes()
    {
        return $this->hasOne('App\Models\UserHasRoomTypeCommission', 'ref_room_type_id', 'id')->where('course', 40);
    }
    public function user_has_room_type_commission_sixty_minutes()
    {
        return $this->hasOne('App\Models\UserHasRoomTypeCommission', 'ref_room_type_id', 'id')->where('course', 60);
    }
    public function user_has_room_type_commission_ninety_minutes()
    {
        return $this->hasOne('App\Models\UserHasRoomTypeCommission', 'ref_room_type_id', 'id')->where('course', 90);
    }
}
