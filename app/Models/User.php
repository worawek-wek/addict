<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable , SoftDeletes;

    /**
     * user id ที่เห็น/จัดการได้ทุกสาขา (เทียบเท่าแอดมิน)
     *  - 1  = Boss กัส (เจ้าของร้าน)
     *  - 235 = ราณี วันหมัด (ranee252600@hotmail.com)
     * ใช้กับสิทธิ์ "เห็นทุกสาขา" เท่านั้น ไม่รวมสิทธิ์ตั้งค่าเจ้าของร้าน (เช่น ตั้งค่าบันได Rank ยังเฉพาะ id=1)
     */
    public const ALL_BRANCH_ADMIN_IDS = [1, 235];

    /** true = user นี้เห็น/จัดการได้ทุกสาขา */
    public static function isAllBranchAdmin($id): bool
    {
        return in_array((int) $id, self::ALL_BRANCH_ADMIN_IDS, true);
    }

    /**
     * user id ที่เข้าได้เฉพาะเมนู stock (สินค้า & สต็อก) เท่านั้น — เมนู/หน้าอื่นถูกซ่อนและกันเข้า
     *  - 235 = ราณี (บัญชี) เห็นทุกสาขาแต่จำกัดเฉพาะ stock
     * (เฉพาะ user รายคน จนกว่าจะมีระบบ permission ตามตำแหน่งจริง)
     */
    public const STOCK_ONLY_IDS = [235];

    /** true = user นี้เข้าได้เฉพาะเมนู stock */
    public static function isStockOnly($id): bool
    {
        return in_array((int) $id, self::STOCK_ONLY_IDS, true);
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'work_status', 'email', 'password', 'commission_mode', 'drink_commission_mode',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that appends to returned entities.
     *
     * @var array
     */
    // protected $appends = ['photo']; ไม่แน่ใจ

    /**
     * The getter that return accessible URL for user photo.
     *
     * @var array
     */

    /**
     * มาม่า/ทีมเชียร์ = พนักงานทุกตำแหน่งยกเว้นพนักงานนวด (position id = 2)
     */
    public function scopeMama($query)
    {
        return $query->where('ref_position_id', '!=', 2);
    }

    public function position()
    {
        return $this->hasOne('App\Models\Position', 'id', 'ref_position_id');
    }
    public function branch()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'ref_branch_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'ref_user_id')->withTrashed();
    }
    public function history_commission()
    {
        return $this->hasMany('App\Models\HistoryCommission', 'ref_staff_id', 'id');
    }
    // public function getPhotoUrlAttribute()
    // {
    //     if ($this->foto !== null) {
    //         return url('media/user/' . $this->id . '/' . $this->foto);
    //     } else {
    //         return url('media-example/no-image.png');
    //     }
    // }
}
