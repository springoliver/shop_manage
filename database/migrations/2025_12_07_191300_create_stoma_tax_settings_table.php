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
        Schema::create('stoma_tax_settings', function (Blueprint $table) {
            $table->increments('taxid');
            $table->unsignedBigInteger('storeid');
            $table->string('tax_name', 255);
            $table->enum('tax_status', ['Enable', 'Disable'])->default('Enable');
            $table->string('tax_amount', 255);
            $table->unsignedInteger('insertby')->default(0);
            $table->dateTime('insertdate');
            $table->string('insertip', 50);
            $table->dateTime('editdate_tax')->nullable();
            $table->string('editip', 50)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_tax_settings');
    }
};

