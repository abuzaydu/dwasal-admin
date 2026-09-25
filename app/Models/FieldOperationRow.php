<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldOperationRow extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expenditure_entries' => 'array',
        'cash_entries' => 'array',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(FieldOperationsRecord::class, 'field_operations_record_id');
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->amount_per_trip ?? 0) * (int) ($this->trip_count ?? 0);
    }

    public function getExpenditureTotalAttribute(): float
    {
        return (float) collect($this->expenditure_entries ?? [])->sum('amount');
    }

    public function getCashSubmittedTotalAttribute(): float
    {
        $totalAmount = (float) $this->total_amount;
        $expenseTotal = (float) $this->expenditure_total;

        return max(0, $totalAmount - $expenseTotal);
    }
}