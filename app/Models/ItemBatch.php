<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemBatch extends Model
{
    use HasFactory;

    protected $table = 'item_batches';

    protected $guarded = [];

    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'manufacture_date' => 'date',
            'expiry_date'      => 'date',
            'quantity'         => 'decimal:4',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'item_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsExpiredAttribute(): bool
    {
        return filled($this->expiry_date)
            && now()->isAfter($this->expiry_date);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->batch_number;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeExpired(
        Builder $query
    ): Builder {
        return $query
            ->whereNotNull('expiry_date')
            ->whereDate(
                'expiry_date',
                '<',
                now()
            );
    }

    public function scopeValid(
        Builder $query
    ): Builder {
        return $query
            ->where(function (Builder $query): void {

                $query
                    ->whereNull('expiry_date')
                    ->orWhereDate(
                        'expiry_date',
                        '>=',
                        now()
                    );

            });
    }
}