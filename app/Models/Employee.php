<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'joining_date',
        'position',
        'employment_status',
        'wage_amount',
        'emergency_contact',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'wage_amount' => 'decimal:2',
        'employment_status' => EmploymentStatus::class,
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function salaryHistories(): HasMany
    {
        return $this->hasMany(SalaryHistory::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function holiday(): HasOne
    {
        return $this->hasOne(Holiday::class);
    }

    public function cashAdvances(): HasMany
    {
        return $this->hasMany(CashAdvance::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
