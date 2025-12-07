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
        Schema::create('stoma_store_department', function (Blueprint $table) {
            $table->bigIncrements('departmentid');
            $table->string('department', 255);
            $table->integer('storetypeid');
            $table->unsignedBigInteger('storeid')->default(0);
            $table->bigInteger('roster_max_time')->default(0);
            $table->bigInteger('day_max_time')->default(0);
            $table->bigInteger('target_hours')->default(0);
            $table->integer('Monday')->default(0);
            $table->integer('Tuesday')->default(0);
            $table->integer('Wednesday')->default(0);
            $table->integer('Thursday')->default(0);
            $table->integer('Friday')->default(0);
            $table->integer('Saturday')->default(0);
            $table->integer('Sunday')->default(0);
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            
            $table->foreign('storetypeid')->references('typeid')->on('stoma_storetype')->onDelete('cascade');
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            
            $table->index(['storeid', 'storetypeid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_store_department');
    }
};
