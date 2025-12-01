<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class RoomGroupModel extends Model
{
    use HasFactory;
    protected $table = 'tb_room_group';
    protected $fillable = ['name'];
    protected $primaryKey = 'id';

    public $timestamps = true;


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($roomGroup) {
            // If your column is room_group_id, change accordingly
            \App\Models\Room::where('room_group_id', $roomGroup->id)->update(['room_group_id' => null]);
        });
    }

    public function RoomChildren()
    {
        return $this->hasMany(Room::class, 'room_group_id', 'id');
    }


    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
