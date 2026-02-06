<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonOption extends Model
{
    use HasFactory;
     protected $table = 'addon_options';

    protected $fillable = [
        'name',
        'price',
        'branch',
        'commission',
        'coupon',
    ];
    
    public function user_has_option_commission()
    {
        return $this->hasOne('App\Models\UserHasOptionCommission', 'ref_option_id', 'id');
    }
    public function addon_option_has_course()
    {
        return $this->hasMany(
                \App\Models\AddonOptionHasCourse::class,
                'ref_addon_option_id',
                'id'
            )
            ->join('courses', 'courses.id', '=', 'addon_option_has_courses.ref_course_id')
            ->orderBy('courses.sort')
            ->select('addon_option_has_courses.*');
    }
    public function orderAddons()
    {
        return $this->hasMany(OrderHasAddonOption::class, 'ref_option_id');
    }
}
