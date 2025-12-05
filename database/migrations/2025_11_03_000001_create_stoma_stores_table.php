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
        Schema::create('stoma_store', function (Blueprint $table) {
            $table->id('storeid');
            $table->unsignedBigInteger('storeownerid')->default(0);
            $table->string('store_name', 255);
            $table->integer('typeid')->default(0);
            $table->string('logofile', 255)->nullable();
            $table->string('full_google_address', 255)->nullable();
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
            $table->string('website_url', 255)->nullable();
            $table->string('store_email', 255);
            $table->string('store_email_pass', 255)->nullable();
            $table->string('manager_email', 255)->nullable();

            // Monday hours
            $table->time('monday_hour_from')->nullable();
            $table->time('monday_hour_to')->nullable();
            $table->enum('monday_dayoff', ['Yes', 'No'])->nullable();

            // Tuesday hours
            $table->time('tuesday_hour_from')->nullable();
            $table->time('tuesday_hour_to')->nullable();
            $table->enum('tuesday_dayoff', ['Yes', 'No'])->nullable();

            // Wednesday hours
            $table->time('wednesday_hour_from')->nullable();
            $table->time('wednesday_hour_to')->nullable();
            $table->enum('wednesday_dayoff', ['Yes', 'No'])->nullable();

            // Thursday hours
            $table->time('thursday_hour_from')->nullable();
            $table->time('thursday_hour_to')->nullable();
            $table->enum('thursday_dayoff', ['Yes', 'No'])->nullable();

            // Friday hours
            $table->time('friday_hour_from')->nullable();
            $table->time('friday_hour_to')->nullable();
            $table->enum('friday_dayoff', ['Yes', 'No'])->nullable();

            // Saturday hours
            $table->time('saturday_hour_from')->nullable();
            $table->time('saturday_hour_to')->nullable();
            $table->enum('saturday_dayoff', ['Yes', 'No'])->nullable();

            // Sunday hours
            $table->time('sunday_hour_from')->nullable();
            $table->time('sunday_hour_to')->nullable();
            $table->enum('sunday_dayoff', ['Yes', 'No'])->nullable();

            // Metadata fields
            $table->dateTime('insertdate');
            $table->string('insertip', 51);
            $table->integer('insertby')->default(0);
            $table->dateTime('editdate');
            $table->string('editip', 51);
            $table->integer('editby')->default(0);
            $table->enum('status', ['Pending Setup', 'Active', 'Suspended', 'Closed'])->default('Active');

            // Foreign key
            $table->foreign('storeownerid')->references('ownerid')->on('stoma_storeowner')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_store');
    }
};
