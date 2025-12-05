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
        Schema::create('stoma_mailformat', function (Blueprint $table) {
            $table->integer('emailid')->autoIncrement();
            $table->text('vartitle');
            $table->string('uniquename', 255);
            $table->text('variables');
            $table->string('varsubject', 255);
            $table->text('varmailformat');
            $table->timestamp('timestamp')->useCurrent()->useCurrentOnUpdate();
            
            $table->primary('emailid');
        });
        
        DB::statement('ALTER TABLE stoma_mailformat ENGINE = MyISAM');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_mailformat');
    }
};

