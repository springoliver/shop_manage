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
        Schema::create('stoma_pos_modifiers', function (Blueprint $table) {
            $table->increments('pos_modifiers_id');
            $table->unsignedBigInteger('storeid');
            $table->string('pos_modifier_name', 50);
            $table->unsignedInteger('catalog_product_groupid');
            $table->string('insertip', 50);
            $table->string('editip', 50)->nullable();
            $table->dateTime('isertdate');
            $table->timestamp('editdate')->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            // Note: catalog_product_groupid references stoma_catalog_product_group which may not exist yet
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_pos_modifiers');
    }
};

