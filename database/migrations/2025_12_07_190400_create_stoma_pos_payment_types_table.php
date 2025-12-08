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
        Schema::create('stoma_pos_payment_types', function (Blueprint $table) {
            $table->increments('pos_payment_types_id');
            $table->unsignedBigInteger('storeid');
            $table->string('pos_payment_type_name', 50);
            $table->timestamp('insertdate')->useCurrent()->useCurrentOnUpdate();
            $table->string('insertip', 50);
            $table->dateTime('editdate')->nullable();
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
        Schema::dropIfExists('stoma_pos_payment_types');
    }
};

