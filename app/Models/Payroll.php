<?php

namespace App\Models;

use App\Enums\PayrollStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'gross_salary',
        'total_worked_days',
        'total_worked_hours',
        'total_worked_minutes',
        'total_absent_days',
        'total_absent_hours',
        'total_absent_minutes',
        'total_bonus',
        'total_deduction',
        'total_advances',
        'net_pay',
        'status',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'gross_salary' => 'decimal:2',
        'total_worked_days' => 'integer',
        'total_worked_hours' => 'integer',
        'total_worked_minutes' => 'integer',
        'total_absent_days' => 'integer',
        'total_absent_hours' => 'integer',
        'total_absent_minutes' => 'integer',
        'total_bonus' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'total_advances' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'status' => PayrollStatus::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function payrollAdjustments(): HasMany
    {
        return $this->hasMany(PayrollAdjustment::class);
    }
}