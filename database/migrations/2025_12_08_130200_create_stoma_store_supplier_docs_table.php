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
        Schema::create('stoma_store_supplier_docs', function (Blueprint $table) {
            $table->increments('docid');
            $table->integer('storeid');
            $table->integer('supplierid');
            $table->integer('doctypeid');
            $table->string('docname', 255);
            $table->string('docpath', 255);
            $table->date('doc_date');
            $table->timestamp('insertdatetime')->useCurrent()->useCurrentOnUpdate();
            $table->string('insertip', 51);
            $table->timestamp('editdatetime')->nullable();
            $table->string('editip', 51);
            $table->enum('status', ['Enable', 'Disable']);
            
            $table->index('storeid');
            $table->index('supplierid');
            $table->index('doctypeid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_store_supplier_docs');
    }
};

