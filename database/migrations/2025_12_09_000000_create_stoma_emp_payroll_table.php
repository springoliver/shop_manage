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
        if (Schema::hasTable('stoma_emp_payroll')) {
            return; // Table already exists
        }
        
        Schema::create('stoma_emp_payroll', function (Blueprint $table) {
            $table->bigInteger('payroll_id')->primary();
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid')->nullable();
            $table->unsignedInteger('departmentid');
            $table->unsignedInteger('weekid');  // Match stoma_week.weekid (increments = unsignedInteger)
            $table->enum('weekday', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->enum('shift', ['day', 'night'])->default('day');
            $table->decimal('total_hours', 5, 2);
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->dateTime('editdate')->nullable();
            $table->string('editip', 51)->default('000000000000');
            
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
        Schema::dropIfExists('stoma_emp_payroll');
    }
};

