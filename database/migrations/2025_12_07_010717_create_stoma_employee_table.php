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
        Schema::create('stoma_employee', function (Blueprint $table) {
            $table->bigIncrements('employeeid');
            $table->unsignedBigInteger('storeid');
            $table->integer('usergroupid')->nullable();
            $table->unsignedBigInteger('departmentid');
            $table->integer('roster_week_hrs')->default(100);
            $table->integer('roster_day_hrs')->default(20);
            $table->integer('break_every_hrs');
            $table->integer('break_min');
            $table->enum('paid_break', ['Yes', 'No']);
            $table->enum('display_hrs_hols', ['Yes', 'No'])->default('No');
            $table->decimal('holiday_percent', 10, 0)->default(0);
            $table->decimal('holiday_day_entitiled', 10, 0)->default(0);
            $table->string('firstname', 255);
            $table->string('lastname', 255);
            $table->string('username', 255);
            $table->string('emailid', 255);
            $table->string('emptaxnumber', 51);
            $table->string('empnationality', 51);
            $table->date('empjoindate');
            $table->string('empbankdetails1', 51);
            $table->string('empbankdetails2', 51);
            $table->bigInteger('emplogin_code');
            $table->string('password', 255);
            $table->string('profile_photo', 255)->nullable();
            $table->string('phone', 51);
            $table->string('country', 55);
            $table->string('address1', 255);
            $table->string('address2', 255);
            $table->string('state', 255);
            $table->string('city', 255);
            $table->string('zipcode', 21);
            $table->date('dateofbirth');
            $table->enum('accept_terms', ['Yes', 'No'])->default('Yes');
            $table->enum('payment_method', ['hourly', 'weekly', 'fortnightly', 'lunar', 'monthly', 'yearly']);
            $table->enum('sallary_method', ['hourly', 'yearly']);
            $table->decimal('pay_rate_hour', 10, 2)->default(0);
            $table->dateTime('signupdate');
            $table->string('signupip', 51);
            $table->integer('signupby')->default(0);
            $table->dateTime('editdate')->nullable();
            $table->string('editip', 51)->nullable();
            $table->integer('editby')->default(0);
            $table->enum('status', ['Deactivate', 'Active', 'Suspended', 'Closed'])->default('Active');
            $table->dateTime('lastlogindate')->nullable();
            $table->string('lastloginip', 51)->nullable();
            $table->decimal('pay_rate_week', 10, 2)->nullable();
            $table->decimal('pay_rate_year', 10, 2)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
            $table->foreign('usergroupid')->references('usergroupid')->on('stoma_usergroup')->onDelete('set null');
            $table->foreign('departmentid')->references('departmentid')->on('stoma_store_department')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_employee');
    }
};
