<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentBill extends Model
{
    // use HasFactory;
    protected $fillable = [
        'invoice_number',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'rent_bills';
    
    public function room_for_rent()
    {
        return $this->hasOne('App\Models\RoomForRents', 'id', 'ref_room_for_rent_id');
    }
    public function additional_costs()
    {
        return $this->hasMany('App\Models\AdditionalCosts', 'ref_rent_bill_id', 'id');
    }
    public function status()
    {
        return $this->hasOne('App\Models\StatusRentBill', 'id', 'ref_status_id');
    }
}
