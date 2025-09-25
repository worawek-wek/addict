<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheerCharge extends Model
{
    protected $table = 'cheer_charge';

    protected $fillable = [
        'ref_branch_id',
        'addon_options_id',
        'type', // 'percent' or 'baht'
        'amount',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'ref_branch_id');
    }

    public function addonOption()
    {
        return $this->belongsTo(AddonOption::class, 'addon_options_id');
    }
}
