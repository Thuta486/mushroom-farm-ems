<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('allowed_days_per_month')->default(2);
            $table->timestamps();

            $table->unique('employee_id'); // one fixed setting per employee
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
