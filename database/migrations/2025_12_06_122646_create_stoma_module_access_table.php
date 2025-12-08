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
        Schema::create('stoma_module_access', function (Blueprint $table) {
            $table->bigIncrements('accessid');
            $table->unsignedBigInteger('storeid');
            $table->integer('usergroupid')->nullable();
            $table->unsignedBigInteger('moduleid');
            $table->enum('level', ['Admin', 'View', 'None'])->default('None');
            $table->timestamp('insertdate')->nullable();
            $table->string('insertip', 255)->default('');
            $table->timestamp('editdate')->useCurrent()->useCurrentOnUpdate();
            $table->string('editip', 255)->default('');
            
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('usergroupid')->references('usergroupid')->on('stoma_usergroup')->onDelete('cascade');
            $table->foreign('moduleid')->references('moduleid')->on('stoma_module')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_module_access');
    }
};
