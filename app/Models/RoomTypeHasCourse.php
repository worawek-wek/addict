<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypeHasCourse extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name'
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'room_type_has_courses';

    public function room_type()
    {
        return $this->hasOne('App\Models\RoomType', 'id', 'ref_room_type_id');
    }
    public function course()
    {
        return $this->hasOne('App\Models\course', 'id', 'ref_course_id');
    }
}
