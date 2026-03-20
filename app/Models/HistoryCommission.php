<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryCommission extends Model
{
    protected $table = 'history_commissions';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'type',
    ];
}
