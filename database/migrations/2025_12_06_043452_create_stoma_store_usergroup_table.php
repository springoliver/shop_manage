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
        Schema::create('stoma_store_usergroup', function (Blueprint $table) {
            $table->increments('suid');
            $table->unsignedBigInteger('storeid');
            $table->integer('usergroupid')->nullable();
            $table->decimal('hour_charge', 10, 2)->default('0.00');
            $table->integer('total_week_hour')->default(0);
            $table->datetime('insertdatetime')->nullable();
            $table->string('insertip', 45)->nullable();
            $table->datetime('editdatetime')->nullable();
            $table->string('editip', 45)->nullable();
            
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('usergroupid')->references('usergroupid')->on('stoma_usergroup')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_store_usergroup');
    }
};
