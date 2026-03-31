<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('governorate_name_ar', 50);
            $table->string('governorate_name_en', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governorates');
    }
};
