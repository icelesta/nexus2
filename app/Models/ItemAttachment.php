<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ItemAttachment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'item_attachments';

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [

            'effective_date' => 'date',

            'expiry_date' => 'date',

            'is_active' => 'boolean',

            'created_at' => 'datetime',

            'updated_at' => 'datetime',

            'deleted_at' => 'datetime',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFileUrlAttribute(): ?string
    {
        if (blank($this->file_path)) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    public function getIsExpiredAttribute(): bool
    {
        if (blank($this->expiry_date)) {
            return false;
        }

        return now()->gt($this->expiry_date);
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'deleted_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'is_active',
            true
        );
    }

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
            ->where('is_active', true)
            ->where(function ($query) {

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