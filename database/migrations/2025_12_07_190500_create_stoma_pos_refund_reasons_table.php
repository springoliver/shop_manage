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
        Schema::create('stoma_pos_refund_reasons', function (Blueprint $table) {
            $table->increments('pos_refund_reason_id');
            $table->unsignedBigInteger('storeid');
            $table->string('pos_refund_reason_name', 50);
            $table->unsignedInteger('min_security_level_id');
            $table->string('insertip', 50);
            $table->string('editip', 50)->nullable();
            $table->dateTime('isertdate');
            $table->timestamp('editdate')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('min_security_level_id')->references('usergroupid')->on('stoma_usergroup')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_pos_refund_reasons');
    }
};

