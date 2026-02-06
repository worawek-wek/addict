<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
        'room_group_id'
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'rooms';

    public function order()
    {
        return $this->hasOne('App\Models\Order', 'ref_room_id', 'id')->orderBy('id', 'DESC');
    }
    public function room_group()
    {
        return $this->belongsTo(RoomGroupModel::class, 'room_group_id', 'id');
    }
    public function room_type()
    {
        return $this->hasMany('App\Models\RoomType', 'ref_room_id', 'id');
    }
    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }

}
