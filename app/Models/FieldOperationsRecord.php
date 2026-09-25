<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldOperationsRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'record_date' => 'date',
        'fuel_in' => 'decimal:2',
        'fuel_out' => 'decimal:2',
    ];

    public function rows(): HasMany
    {
        return $this->hasMany(FieldOperationRow::class)->orderBy('row_number');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function total(string $column): float
    {
        if ($column === 'cash_submitted') {
            return max(0, $this->total('total_amount') - $this->total('expenditure'));
        }

        return (float) $this->rows->sum(function (FieldOperationRow $row) use ($column) {
            return match ($column) {
                'total_amount' => $row->total_amount,
                'expenditure' => $row->expenditure_total,
                default => $row->{$column},
            };
        });
    }
}