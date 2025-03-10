<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name'];

    /**
     * Lấy quận/huyện mà phường/xã thuộc về
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Lấy tỉnh/thành phố mà phường/xã thuộc về (thông qua quận/huyện)
     */
    public function province()
    {
        return $this->district->province;
    }

    /**
     * Lấy danh sách đại lý thuộc phường/xã
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'ward_id');
    }

    /**
     * Lấy danh sách địa chỉ thuộc phường/xã
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
