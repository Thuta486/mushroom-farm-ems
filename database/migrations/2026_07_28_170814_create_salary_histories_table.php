<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('wage_amount', 12, 2);
            $table->date('effective_date');
            $table->string('reason')->nullable(); // e.g. Promotion, Annual Increment
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_histories');
    }
};
