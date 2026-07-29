<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Cash Advance, Mushroom Eating Cash
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_types');
    }
};
