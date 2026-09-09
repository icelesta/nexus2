<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmLimitMaster extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'adm_limit_masters';

    protected $fillable = [

        'limit_amount',
        'is_active',
        'description',

        'created_by',
        'updated_by',

    ];

    protected function casts(): array
    {
        return [

            'limit_amount' =>
                'decimal:2',

            'is_active' =>
                'boolean',

            'created_by' =>
                'integer',

            'updated_by' =>
                'integer',

            'created_at' =>
                'datetime',

            'updated_at' =>
                'datetime',

            'deleted_at' =>
                'datetime',

        ];
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

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'is_active',
            true
        );
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}