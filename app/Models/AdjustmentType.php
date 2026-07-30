<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdjustmentType extends Model
{
    use HasFactory;

    use \App\Traits\HasLocalizedAttributes;

    protected $fillable = ['name_en', 'name_my', 'category'];

    public function payrollAdjustments(): HasMany
    {
        return $this->hasMany(PayrollAdjustment::class);
    }
}
