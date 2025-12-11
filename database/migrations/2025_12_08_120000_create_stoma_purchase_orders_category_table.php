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
        Schema::create('stoma_purchase_orders_category', function (Blueprint $table) {
            $table->increments('categoryid');
            $table->string('category_name', 255);
            $table->unsignedBigInteger('storeid');
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->integer('insertby');
            $table->dateTime('editdate');
            $table->string('editip', 51);
            $table->integer('editby');
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_purchase_orders_category');
    }
};

