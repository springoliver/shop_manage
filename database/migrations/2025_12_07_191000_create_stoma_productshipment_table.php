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
        Schema::create('stoma_productshipment', function (Blueprint $table) {
            $table->increments('shipmentid');
            $table->string('shipment', 255);
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
        Schema::dropIfExists('stoma_productshipment');
    }
};

