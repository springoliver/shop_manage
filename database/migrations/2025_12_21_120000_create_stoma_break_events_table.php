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
        Schema::create('stoma_break_events', function (Blueprint $table) {
            $table->id('breakid');
            $table->unsignedBigInteger('eltid')->nullable()->comment('Reference to emp_login_time record');
            $table->unsignedBigInteger('storeid');
            $table->unsignedBigInteger('employeeid');
            $table->datetime('break_start');
            $table->datetime('break_end')->nullable();
            $table->integer('break_duration')->nullable()->comment('Duration in minutes');
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->datetime('insertdate')->nullable();
            $table->string('insertip', 50)->nullable();
            
            // Indexes
            $table->index('eltid');
            $table->index(['storeid', 'employeeid']);
            $table->index('status');
            $table->index('break_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_break_events');
    }
};

