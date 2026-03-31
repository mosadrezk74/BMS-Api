<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained('governorates')->cascadeOnDelete();
            $table->string('city_name_ar', 200);
            $table->string('city_name_en', 200);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
