<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->unsignedBigInteger('book_id')->index('book_id');
            $table->integer('quantity');
            $table->unsignedBigInteger('ship_id')->index('ship_id');


            $table->foreign(['book_id'])
                ->references(['id'])
                ->on('books')
                ->onDelete('no action');
            $table->foreign(['user_id'])
                ->references(['id'])
                ->on('users')
                ->onDelete('no action');
            $table->foreign(['ship_id'])
                ->references(['id'])
                ->on('shipping_address')
                ->onDelete('no action');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
