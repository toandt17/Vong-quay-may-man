<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'lucky_wheel_id',
        'name',
        'image',
        'background_color',
        'icon',
        'win_rate',
        'quantity',
        'remaining',
        'description',
    ];

    protected $casts = [
        'win_rate' => 'decimal:2',
        'quantity' => 'integer',
        'remaining' => 'integer',
    ];

    /**
     * Get the lucky wheel that owns the prize.
     */
    public function luckyWheel(): BelongsTo
    {
        return $this->belongsTo(LuckyWheel::class);
    }

    /**
     * Get the award histories for the prize.
     */
    public function awardHistories(): HasMany
    {
        return $this->hasMany(AwardHistory::class);
    }
}
