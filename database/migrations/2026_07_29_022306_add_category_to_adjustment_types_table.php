<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adjustment_types', function (Blueprint $table) {
            $table->string('category')->default('deduction')->after('name'); // bonus, deduction
        });
    }

    public function down(): void
    {
        Schema::table('adjustment_types', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
