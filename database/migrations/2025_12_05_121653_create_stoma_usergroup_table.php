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
        Schema::create('stoma_usergroup', function (Blueprint $table) {
            $table->increments('usergroupid');
            $table->string('groupname', 255);
            $table->enum('level_access', ['Admin', 'View']);
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->unsignedBigInteger('storeid');
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_usergroup');
    }
};
