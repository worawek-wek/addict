<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MassageCommission extends Model
{
    protected $table = 'massage_commissions';
    protected $fillable = [
        'ref_user_id',
        'ref_branch_id',
        'addon_options_id',
        'service_name',
        'service_duration',
        'commission_amount',
        'commission_percent',
        'created_at',
        'updated_at',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'ref_user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'ref_branch_id');
    }

    // public function position()
    // {
    //     return $this->user ? $this->user->position() : null;
    // }
    public function position()
    {
        return $this->belongsTo(Position::class, 'ref_position_id');
    }
}
