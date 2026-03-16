<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VmsExpenseItem extends Model
{
    protected $guarded = [];

    public function expense() {
        return $this->belongsTo(VmsExpense::class, 'vms_expense_id');
    }

    public function expenseType() {
        return $this->belongsTo(ExpenseType::class);
    }
}
