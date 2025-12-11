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
        Schema::create('stoma_dashboard_settings', function (Blueprint $table) {
            $table->increments('dashboardsettingsid');
            $table->integer('storeid');
            $table->integer('sale_per_labour_hour')->default(1)->comment('1. Flat, 2. Day Basis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_dashboard_settings');
    }
};

