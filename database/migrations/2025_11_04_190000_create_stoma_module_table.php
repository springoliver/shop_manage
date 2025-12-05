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
        Schema::create('stoma_module', function (Blueprint $table) {
            $table->id('moduleid');
            $table->string('module', 255);
            $table->text('module_description');
            $table->text('module_detailed_info')->nullable();
            $table->decimal('price_1months', 10, 2);
            $table->decimal('price_3months', 10, 2)->default(0.00);
            $table->decimal('price_6months', 10, 2)->default(0.00);
            $table->decimal('price_12months', 10, 2)->default(0.00);
            $table->bigInteger('free_days');
            $table->enum('status', ['Enable', 'Disable'])->default('Enable');
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->integer('insertby');
            $table->dateTime('editdate');
            $table->string('editip', 51);
            $table->integer('editby');
            
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_module');
    }
};

