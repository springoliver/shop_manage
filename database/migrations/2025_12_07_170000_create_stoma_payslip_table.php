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
        Schema::create('stoma_payslip', function (Blueprint $table) {
            $table->increments('payslipid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->string('payslipname', 255);
            $table->unsignedInteger('weekid');
            $table->string('year', 4);
            $table->timestamp('insertdatetime')->useCurrent();
            $table->string('insertip', 51);
            $table->timestamp('editdatetime')->nullable();
            $table->string('editip', 51)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('employeeid')->references('employeeid')->on('stoma_employee')->onDelete('cascade');
            $table->foreign('weekid')->references('weekid')->on('stoma_week')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_payslip');
    }
};

