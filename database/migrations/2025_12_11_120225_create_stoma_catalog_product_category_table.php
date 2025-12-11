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
        if (Schema::hasTable('stoma_catalog_product_category')) {
            return;
        }

        Schema::create('stoma_catalog_product_category', function (Blueprint $table) {
            $table->increments('catalog_product_categoryid');
            $table->string('catalog_product_groupid', 55);
            $table->string('catalog_product_category_name', 255);
            $table->string('catalog_product_category_colour', 11)->default('CCCCCC');
            $table->enum('catalog_product_sell_online', ['Enable', 'Disable'])->default('Enable');
            $table->string('catalog_product_taxid', 55);
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
        Schema::dropIfExists('stoma_catalog_product_category');
    }
};
