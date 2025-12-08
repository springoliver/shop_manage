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
        Schema::create('stoma_employee_review_subjects', function (Blueprint $table) {
            $table->increments('review_subjectid');
            $table->unsignedBigInteger('storeid');
            $table->integer('usergroupid');
            $table->string('subject_name', 255);
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->timestamp('insertdatetime')->useCurrent();
            $table->string('insertip', 51);
            $table->timestamp('editdatetime')->nullable();
            $table->string('editip', 51)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('usergroupid')->references('usergroupid')->on('stoma_usergroup')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_employee_review_subjects');
    }
};

