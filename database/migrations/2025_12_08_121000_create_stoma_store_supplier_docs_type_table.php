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
        Schema::create('stoma_store_supplier_docs_type', function (Blueprint $table) {
            $table->increments('docs_type_id');
            $table->string('docs_type_name', 255);
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
        Schema::dropIfExists('stoma_store_supplier_docs_type');
    }
};

