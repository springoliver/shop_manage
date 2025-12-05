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
        Schema::create('stoma_department', function (Blueprint $table) {
            $table->bigIncrements('departmentid');
            $table->string('department', 255);
            $table->integer('storetypeid');
            $table->unsignedBigInteger('storeid');
            $table->bigInteger('roster_max_time')->default(0);
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->foreign('storetypeid')->references('typeid')->on('stoma_storetype')->onDelete('cascade');
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_department');
    }
};
