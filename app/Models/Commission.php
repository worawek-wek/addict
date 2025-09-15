<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';

    protected $fillable = [
        'ref_user_id',
        'ref_position_id',
        'service_name',
        'service_duration',
        'commission_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'ref_user_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'ref_position_id');
    }
}
