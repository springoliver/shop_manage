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
        Schema::create('stoma_week', function (Blueprint $table) {
            $table->increments('weekid');
            $table->integer('weeknumber');
            $table->unsignedInteger('yearid');
            
            // Foreign key
            $table->foreign('yearid')->references('yearid')->on('stoma_year')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_week');
    }
};
