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
        Schema::create('stoma_pages', function (Blueprint $table) {
            $table->integer('pageid')->autoIncrement();
            $table->string('meta_title', 255)->default('');
            $table->text('meta_keyword')->default('');
            $table->text('meta_description')->default('');
            $table->string('page_title', 255)->default('');
            $table->text('short_description')->default('');
            $table->text('description')->default('');
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->timestamp('edit_date')->useCurrent()->useCurrentOnUpdate();
            $table->string('edit_ip', 51)->default('');
            $table->integer('edit_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_pages');
    }
};

