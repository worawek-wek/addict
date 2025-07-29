<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'rooms';

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'ref_floor_id');
    }
    public function room_for_rent()
    {
        return $this->hasOne('App\Models\RoomForRents', 'ref_room_id', 'id')->where('status', 0);
    }
    public function room_has_service()
    {
        return $this->hasMany('App\Models\RoomHasService', 'ref_room_id', 'id');
    }
    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
}
