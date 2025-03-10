<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['province_id', 'name'];

    /**
     * Lấy tỉnh/thành phố mà quận/huyện thuộc về
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Lấy danh sách phường/xã thuộc quận/huyện
     */
    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

    /**
     * Lấy danh sách đại lý thuộc quận/huyện
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'district_id');
    }

    /**
     * Lấy danh sách địa chỉ thuộc quận/huyện
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
