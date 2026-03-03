<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Hotel;
use App\Models\RoomType;

class Price extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'amount',
        'vat',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'amount'     => 'decimal:2',
        'vat'        => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Scope to find the price active on a given date.
     * Usage: Price::activeOn('2025-06-01')->where(...)
     */
    public function scopeActiveOn(Builder $query, string $date): Builder
    {
        return $query->where('start_date', '<=', $date)
                     ->where('end_date', '>=', $date);
    }

    /**
     * Get the total amount with VAT applied.
     * VAT is a percentage added on top e.g. amount=100, vat=7.5 → 107.50
     */
    public function getTotalAmountAttribute(): float
    {
        return round($this->amount * (1 + $this->vat / 100), 2);
    }
}
