<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'hours_worked',
        'minutes_worked',
        'work_type',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => AttendanceStatus::class,
        'hours_worked' => 'integer',
        'minutes_worked' => 'integer',
        'work_type' => WorkType::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
