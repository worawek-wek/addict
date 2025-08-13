<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHasAddonOption extends Model
{
    use HasFactory;

    protected $table = 'order_has_addon_options';

    protected $fillable = [
        'ref_order_id',
        'ref_option_id',
        'price',
    ];

    public $timestamps = true;

    // Relations (Optional)
    public function option()
    {
        return $this->belongsTo(AddonOption::class, 'ref_option_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'ref_order_id');
    }
}
