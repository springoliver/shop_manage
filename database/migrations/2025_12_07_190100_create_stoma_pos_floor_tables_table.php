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
        Schema::create('stoma_pos_floor_tables', function (Blueprint $table) {
            $table->increments('pos_floor_table_id');
            $table->string('pos_floor_section_id', 55);
            $table->string('pos_floor_table_number', 11);
            $table->string('pos_floor_table_seat', 11);
            $table->string('pos_floor_table_colour', 11)->default('CCCCCC');
            $table->smallInteger('pos_floor_table_width')->default(0);
            $table->smallInteger('pos_floor_table_height')->default(0);
            $table->smallInteger('pos_floor_table_top')->default(0);
            $table->smallInteger('pos_floor_table_left')->default(0);
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
        Schema::dropIfExists('stoma_pos_floor_tables');
    }
};

