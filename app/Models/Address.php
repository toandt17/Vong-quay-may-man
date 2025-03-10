<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'street',
        'ward_id',
        'district_id',
        'province_id',
        'country',
        'latitude',
        'longitude',
    ];

    /**
     * Lấy phường/xã của địa chỉ
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * Lấy quận/huyện của địa chỉ
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Lấy tỉnh/thành phố của địa chỉ
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Lấy địa chỉ đầy đủ
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->street) {
            $parts[] = $this->street;
        }

        if ($this->ward && $this->ward->name) {
            $parts[] = $this->ward->name;
        }

        if ($this->district && $this->district->name) {
            $parts[] = $this->district->name;
        }

        if ($this->province && $this->province->name) {
            $parts[] = $this->province->name;
        }

        if ($this->country) {
            $parts[] = $this->country;
        }

        return implode(', ', $parts);
    }
}
