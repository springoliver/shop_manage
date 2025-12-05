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
        Schema::create('stoma_storetype', function (Blueprint $table) {
            $table->integer('typeid')->autoIncrement();
            $table->string('store_type', 255);
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            
            $table->primary('typeid');
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_storetype');
    }
};

