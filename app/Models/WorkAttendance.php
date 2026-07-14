<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkAttendance extends Model
{
    protected $table = 'work_attendances';

    protected $fillable = [
        'ref_staff_id',
        'ref_branch_id',
        'work_date',
        'check_in_at',
        'check_out_at',
        'status',
    ];

    protected $casts = [
        'work_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'ref_staff_id')->withTrashed();
    }
}
