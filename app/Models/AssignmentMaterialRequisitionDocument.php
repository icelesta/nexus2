<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentMaterialRequisitionDocument extends Model
{
    protected $table = 'assignment_material_requisition_documents';

    protected $fillable = [
        'assignment_material_requisition_id',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function assignmentMaterialRequisition(): BelongsTo
    {
        return $this->belongsTo(
            AssignmentMaterialRequisition::class,
            'assignment_material_requisition_id'
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