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
        Schema::create('stoma_store_products', function (Blueprint $table) {
            $table->increments('productid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('departmentid');
            $table->unsignedInteger('catalog_product_groupid');
            $table->string('supplierid', 255);
            $table->string('product_name', 255);
            $table->enum('product_status', ['Enable', 'Disable'])->default('Enable');
            $table->unsignedInteger('taxid');
            $table->string('product_price', 255);
            $table->string('product_notes', 255);
            $table->unsignedInteger('shipmentid');
            $table->unsignedInteger('purchasepaymentmethodid');
            $table->unsignedInteger('purchasemeasuresid');
            $table->unsignedInteger('insertby')->nullable();
            $table->dateTime('insertdate')->nullable();
            $table->string('insertip', 50)->nullable();
            $table->date('editdate')->nullable();
            $table->string('editip', 50)->nullable();
            $table->string('username', 50)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('departmentid')->references('departmentid')->on('stoma_store_department')->onDelete('cascade');
            $table->foreign('catalog_product_groupid')->references('catalog_product_groupid')->on('stoma_catalog_product_group')->onDelete('cascade');
            $table->foreign('taxid')->references('taxid')->on('stoma_tax_settings')->onDelete('cascade');
            $table->foreign('shipmentid')->references('shipmentid')->on('stoma_productshipment')->onDelete('cascade');
            $table->foreign('purchasepaymentmethodid')->references('purchasepaymentmethodid')->on('stoma_purchasepaymentmethod')->onDelete('cascade');
            $table->foreign('purchasemeasuresid')->references('purchasemeasuresid')->on('stoma_purchasemeasures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_store_products');
    }
};

