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
        Schema::create('stoma_purchasedproducts', function (Blueprint $table) {
            $table->increments('purchasedproductsid');
            $table->unsignedInteger('purchase_orders_id');
            $table->string('productid', 255);
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('departmentid');
            $table->unsignedInteger('supplierid');
            $table->unsignedInteger('shipmentid');
            $table->enum('deliverydocketstatus', ['Yes', 'No'])->nullable();
            $table->enum('invoicestatus', ['Yes', 'No'])->nullable();
            $table->integer('invoicenumber')->nullable();
            $table->integer('quantity');
            $table->integer('product_price');
            $table->unsignedInteger('taxid');
            $table->integer('totalamount');
            $table->unsignedInteger('purchasemeasuresid');
            $table->string('purchase_orders_type', 11)->nullable();
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->string('insertby', 51);
            $table->dateTime('editdate');
            $table->string('editip', 51);
            $table->string('editby', 51);
            
            // Foreign keys
            $table->foreign('purchase_orders_id')->references('purchase_orders_id')->on('stoma_purchase_orders')->onDelete('cascade');
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('departmentid')->references('departmentid')->on('stoma_store_department')->onDelete('cascade');
            $table->foreign('supplierid')->references('supplierid')->on('stoma_store_suppliers')->onDelete('cascade');
            $table->foreign('shipmentid')->references('shipmentid')->on('stoma_productshipment')->onDelete('cascade');
            $table->foreign('taxid')->references('taxid')->on('stoma_tax_settings')->onDelete('cascade');
            $table->foreign('purchasemeasuresid')->references('purchasemeasuresid')->on('stoma_purchasemeasures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_purchasedproducts');
    }
};

