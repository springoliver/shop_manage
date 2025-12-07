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
        Schema::create('stoma_paid_module', function (Blueprint $table) {
            $table->increments('pmid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('moduleid');
            $table->timestamp('purchase_date')->useCurrent();
            $table->timestamp('expire_date')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->timestamp('insertdatetime')->nullable();
            $table->string('insertip', 51)->nullable();
            $table->string('paypal_profile_id', 255)->nullable();
            $table->string('transactionid', 255)->nullable();
            $table->boolean('isTrial')->default(0);
            
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('moduleid')->references('moduleid')->on('stoma_module')->onDelete('cascade');
            
            $table->index(['storeid', 'moduleid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_paid_module');
    }
};
