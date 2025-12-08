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
        Schema::create('stoma_store_suppliers', function (Blueprint $table) {
            $table->increments('supplierid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('departmentid');
            $table->enum('purchase_supplier', ['Yes', 'No'])->default('Yes');
            $table->string('supplier_name', 255);
            $table->string('supplier_phone', 255);
            $table->string('supplier_phone2', 255)->nullable();
            $table->string('supplier_email', 255);
            $table->string('supplier_rep', 255);
            $table->string('supplier_acc_number', 22)->default('0');
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->dateTime('insertdate');
            $table->string('insertip', 50);
            $table->dateTime('editdate_supplier')->nullable();
            $table->string('editip', 50)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('departmentid')->references('departmentid')->on('stoma_store_department')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_store_suppliers');
    }
};

