<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasOptionCommission extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name'
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'user_has_option_commissions';

    // public function room()
    // {
    //     return $this->hasOne('App\Models\Room', 'id', 'ref_room_id');
    // }
    // public function branch()
    // {
    //     return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    // }
}
