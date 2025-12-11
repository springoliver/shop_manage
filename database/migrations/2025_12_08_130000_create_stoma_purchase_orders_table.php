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
        Schema::create('stoma_purchase_orders', function (Blueprint $table) {
            $table->increments('purchase_orders_id');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('departmentid');
            $table->unsignedInteger('supplierid');
            $table->unsignedInteger('categoryid')->default(0);
            $table->unsignedInteger('shipmentid');
            $table->string('deliverydocketstatus', 11);
            $table->string('deliverynotes', 255)->nullable();
            $table->string('invoicestatus', 11);
            $table->integer('invoicenumber')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_tax', 10, 2);
            $table->decimal('amount_inc_tax', 10, 2);
            $table->string('products_bought', 255)->nullable();
            $table->date('delivery_date');
            $table->string('po_note', 255)->nullable();
            $table->string('purchase_orders_type', 22);
            $table->dateTime('insertdate');
            $table->string('status', 11);
            $table->string('creditnote', 51)->default('No');
            $table->string('creditnotedesc', 255)->nullable();
            $table->string('insertip', 51);
            $table->string('insertby', 51);
            $table->dateTime('editdate');
            $table->string('editip', 51);
            $table->string('editby', 51);
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('departmentid')->references('departmentid')->on('stoma_store_department')->onDelete('cascade');
            $table->foreign('supplierid')->references('supplierid')->on('stoma_store_suppliers')->onDelete('cascade');
            $table->foreign('shipmentid')->references('shipmentid')->on('stoma_productshipment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_purchase_orders');
    }
};

