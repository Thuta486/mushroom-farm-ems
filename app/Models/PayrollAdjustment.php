<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_id',
        'adjustment_type_id',
        'amount',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function adjustmentType(): BelongsTo
    {
        return $this->belongsTo(AdjustmentType::class);
    }
}
