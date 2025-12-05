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
        Schema::table('stoma_store', function (Blueprint $table) {
            // First, ensure the 'typeid' column can be nullable
            $table->integer('typeid')->nullable()->default(null)->change();

            // Add the foreign key constraint
            $table->foreign('typeid')
                  ->references('typeid')
                  ->on('stoma_storetype')
                  ->onDelete('SET NULL'); // Set to null if a category is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stoma_store', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['typeid']);
        });
    }
};

