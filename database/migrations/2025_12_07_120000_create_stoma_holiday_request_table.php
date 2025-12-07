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
        Schema::create('stoma_holiday_request', function (Blueprint $table) {
            $table->increments('requestid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->dateTime('from_date');
            $table->dateTime('to_date');
            $table->string('subject', 255);
            $table->text('description');
            $table->enum('status', ['Pending', 'Declined', 'Approved'])->default('Pending');
            $table->text('reason')->nullable();
            $table->timestamp('insertdatetime')->useCurrent();
            $table->string('insertip', 51);
            $table->timestamp('editdatetime')->nullable();
            $table->string('editip', 51)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('employeeid')->references('employeeid')->on('stoma_employee')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_holiday_request');
    }
};

