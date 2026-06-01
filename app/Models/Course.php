<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name',
        'show_online_booking',
    ];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'courses';

    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }

}
