<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('gross_salary', 12, 2);
            $table->unsignedInteger('total_worked_days')->default(0);
            $table->unsignedInteger('total_worked_hours')->default(0);
            $table->unsignedInteger('total_worked_minutes')->default(0);
            $table->decimal('total_bonus', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('total_advances', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2);
            $table->string('status')->default('unpaid'); // unpaid, paid
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
