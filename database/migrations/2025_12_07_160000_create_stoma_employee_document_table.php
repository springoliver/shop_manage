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
        Schema::create('stoma_employee_document', function (Blueprint $table) {
            $table->increments('docid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->string('docname', 255);
            $table->string('docpath', 255);
            $table->timestamp('insertdatetime')->useCurrent();
            $table->enum('tc_agree', ['Yes', 'No'])->default('Yes');
            $table->enum('signature', ['Yes', 'No'])->default('No');
            $table->string('insertip', 51);
            $table->timestamp('editdatetime')->nullable();
            $table->string('editip', 51)->nullable();
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            
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
        Schema::dropIfExists('stoma_employee_document');
    }
};

