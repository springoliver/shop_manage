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
        Schema::create('stoma_emp_login_time', function (Blueprint $table) {
            $table->increments('eltid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->timestamp('clockin')->nullable();
            $table->timestamp('clockout')->nullable();
            $table->unsignedInteger('weekid');
            $table->enum('day', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->enum('inRoster', ['Yes', 'No'])->default('No');
            $table->enum('status', ['clockin', 'clockout'])->default('clockin');
            $table->timestamp('insertdate')->useCurrent();
            $table->string('insertby', 51)->nullable();
            $table->string('editby', 51)->nullable();
            $table->string('insertip', 51)->nullable();
            $table->string('editip', 51)->nullable();
            $table->dateTime('editdate')->nullable();
            
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
        Schema::dropIfExists('stoma_emp_login_time');
    }
};

