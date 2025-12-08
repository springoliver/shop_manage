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
        Schema::create('stoma_pos_floor_sections', function (Blueprint $table) {
            $table->increments('pos_floor_section_id');
            $table->string('pos_floor_section_name', 255);
            $table->string('pos_floor_section_colour', 11);
            $table->string('pos_section_list_number', 10);
            $table->unsignedBigInteger('storeid');
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->unsignedInteger('insertby')->default(0);
            $table->dateTime('editdate')->nullable();
            $table->string('editip', 51)->nullable();
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
        Schema::dropIfExists('stoma_pos_floor_sections');
    }
};

