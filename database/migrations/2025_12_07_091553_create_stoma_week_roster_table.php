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
        Schema::create('stoma_week_roster', function (Blueprint $table) {
            $table->increments('wrid');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->unsignedBigInteger('departmentid')->nullable();
            $table->time('start_time')->default('00:00:00');
            $table->time('end_time')->default('00:00:00');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->enum('shift', ['day', 'night'])->default('day');
            $table->date('day_date');
            $table->enum('work_status', ['on', 'off']);
            $table->unsignedInteger('weekid');
            $table->enum('status', ['past', 'current', 'future'])->default('future');
            $table->dateTime('insertdatetime');
            $table->string('insertip', 51);
            $table->timestamp('editdatetime')->nullable();
            $table->integer('break_every_hrs')->default(1);
            $table->integer('break_min')->default(4);
            $table->enum('paid_break', ['Yes', 'No']);
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('employeeid')->references('employeeid')->on('stoma_employee')->onDelete('cascade');
            $table->foreign('departmentid')->references('departmentid')->on('stoma_store_department')->onDelete('set null');
            $table->foreign('weekid')->references('weekid')->on('stoma_week')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_week_roster');
    }
};
