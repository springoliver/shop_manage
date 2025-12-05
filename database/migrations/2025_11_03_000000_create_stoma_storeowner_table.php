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
        Schema::create('stoma_storeowner', function (Blueprint $table) {
            $table->id('ownerid');
            $table->string('firstname', 255);
            $table->string('lastname', 255);
            $table->string('username', 255);
            $table->string('emailid', 255)->unique();
            $table->string('password', 255);
            $table->string('profile_photo', 255)->nullable();
            $table->string('phone', 51);
            $table->string('country', 55)->default('');
            $table->string('address1', 255)->default('');
            $table->string('address2', 255)->nullable();
            $table->string('state', 255)->default('');
            $table->string('city', 255)->default('');
            $table->string('zipcode', 21)->default('');
            $table->date('dateofbirth');
            $table->enum('accept_terms', ['Yes', 'No'])->default('Yes');
            $table->dateTime('signupdate')->useCurrent();
            $table->string('signupip', 51)->default('');
            $table->integer('signupby')->default(0);
            $table->dateTime('editdate')->useCurrent();
            $table->string('editip', 51)->default('');
            $table->integer('editby')->default(0);
            $table->enum('status', ['Pending Setup', 'Active', 'Suspended', 'Closed'])->default('Pending Setup');
            $table->dateTime('lastlogindate')->nullable();
            $table->string('lastloginip', 51)->default('');

            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_storeowner');
    }
};

