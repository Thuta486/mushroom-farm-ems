<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->unsignedInteger('total_absent_days')->default(0)->after('total_worked_minutes');
            $table->unsignedInteger('total_absent_hours')->default(0)->after('total_absent_days');
            $table->unsignedInteger('total_absent_minutes')->default(0)->after('total_absent_hours');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['total_absent_days', 'total_absent_hours', 'total_absent_minutes']);
        });
    }
};