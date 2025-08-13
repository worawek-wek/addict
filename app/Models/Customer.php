<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    // use HasFactory;
   protected $fillable = [
        'name','first_name','last_name','nationality','phone',
        'contact_app','contact_app_handle','id_card','password','ref_branch_id',
        'email', // ถ้ามี
    ];

    protected $hidden = ['password', 'remember_token'];

    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $table = 'customers';

    public function orders()
    {
        return $this->hasMany(Order::class, 'ref_customer_id');
    }
    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
}
