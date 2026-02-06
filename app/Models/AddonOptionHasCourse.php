<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonOptionHasCourse extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name'
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'addon_option_has_courses';

    public function addon_option()
    {
        return $this->hasOne('App\Models\RoomType', 'id', 'ref_addon_option_id');
    }
    public function course()
    {
        return $this->hasOne('App\Models\course', 'id', 'ref_course_id');
    }
}
