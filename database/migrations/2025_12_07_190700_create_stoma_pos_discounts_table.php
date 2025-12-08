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
        Schema::create('stoma_pos_discounts', function (Blueprint $table) {
            $table->increments('pos_discount_id');
            $table->string('pos_discount_percentage', 11);
            $table->string('pos_discount_name', 50);
            $table->unsignedBigInteger('storeid');
            $table->dateTime('insertdate');
            $table->dateTime('editdate')->nullable();
            $table->string('editip', 51)->nullable();
            $table->string('insertip', 50);
            $table->unsignedInteger('editby')->default(0);
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_pos_discounts');
    }
};

