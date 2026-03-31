<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->json('name');
            $table->json('description');

            $table->decimal('price', 10, 2);

            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();

            $table->integer('pages');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
