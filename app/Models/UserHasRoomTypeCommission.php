<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasRoomTypeCommission extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name'
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'user_has_room_type_commissions';

    public function course()
    {
        return $this->hasOne('App\Models\course', 'id', 'ref_course_id');
    }
    // public function room()
    // {
    //     return $this->hasOne('App\Models\Room', 'id', 'ref_room_id');
    // }
    // public function branch()
    // {
    //     return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    // }
}
