<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasLocalizedAttributes;

class AdvanceType extends Model
{
    use HasFactory;

    use HasLocalizedAttributes;

    protected $fillable = ['name_en', 'name_my'];

    public function cashAdvances(): HasMany
    {
        return $this->hasMany(CashAdvance::class);
    }
}
