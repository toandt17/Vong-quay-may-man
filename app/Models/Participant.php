<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'province',
        'district',
        'ward',
        'address',
        'is_farmer',
        'rice_variety',
        'used_products',
    ];

    protected $casts = [
        'is_farmer' => 'boolean',
    ];

    /**
     * Get the award histories for the participant.
     */
    public function awardHistories(): HasMany
    {
        return $this->hasMany(AwardHistory::class);
    }

    /**
     * Check if the participant has already spun the wheel.
     */
    public function hasSpun(): bool
    {
        return $this->awardHistories()->exists();
    }
}
