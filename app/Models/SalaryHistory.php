<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'wage_amount',
        'effective_date',
        'reason',
    ];

    protected $casts = [
        'wage_amount' => 'decimal:2',
        'effective_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
