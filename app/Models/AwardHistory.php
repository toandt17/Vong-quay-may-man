<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AwardHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'lucky_wheel_id',
        'prize_id',
        'spin_time',
        'is_win',
    ];

    protected $casts = [
        'spin_time' => 'datetime',
        'is_win' => 'boolean',
    ];

    /**
     * Get the participant that owns the award history.
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get the lucky wheel that owns the award history.
     */
    public function luckyWheel(): BelongsTo
    {
        return $this->belongsTo(LuckyWheel::class);
    }

    /**
     * Get the prize that owns the award history.
     */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }
}
