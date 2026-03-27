<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'company_id',
        'dt_name',
        'active',
    ];

    public function legalDocuments()
    {
        return $this->hasMany(LegalDocument::class, 'document_type_id');
    }
}
