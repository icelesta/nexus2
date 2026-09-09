<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentDirectMarketDocument extends Model
{
    use SoftDeletes;

    protected $table =
        'assignment_direct_market_documents';

    protected $fillable = [

        'assignment_direct_market_id',

        'file_name',
        'file_path',
        'file_size',

        'document_type',

        'uploaded_by',
    ];

    protected $casts = [

        'file_size' =>
            'integer',

    ];

    public function assignmentDirectMarket(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentDirectMarket::class,
            'assignment_direct_market_id'
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }
}