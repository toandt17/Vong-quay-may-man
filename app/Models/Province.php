<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Lấy danh sách quận/huyện thuộc tỉnh/thành phố
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * Lấy danh sách đại lý thuộc tỉnh/thành phố
     */
    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class, 'province_id');
    }

    /**
     * Lấy danh sách địa chỉ thuộc tỉnh/thành phố
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
