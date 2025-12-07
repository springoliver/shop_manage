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
        Schema::create('stoma_emp_payroll_hrs', function (Blueprint $table) {
            $table->increments('payroll_id');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->integer('weekno');
            $table->date('week_start');
            $table->date('week_end');
            $table->year('year');
            $table->string('hours_worked', 11);
            $table->string('numberofdaysworked', 5)->nullable();
            $table->time('break_deducted');
            $table->decimal('sunday_hrs', 5, 2)->nullable();
            $table->decimal('owertime1_hrs', 5, 2)->nullable();
            $table->decimal('owertime2_hrs', 5, 2)->nullable();
            $table->decimal('holiday_hrs', 5, 2)->nullable();
            $table->decimal('holiday_days', 5, 2)->nullable();
            $table->decimal('sickpay_hrs', 5, 2)->nullable();
            $table->decimal('extras1_hrs', 5, 2)->nullable();
            $table->string('extras2_hrs', 152)->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->string('notes', 251)->nullable();
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->timestamp('editdate')->nullable();
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
        Schema::dropIfExists('stoma_emp_payroll_hrs');
    }
};

