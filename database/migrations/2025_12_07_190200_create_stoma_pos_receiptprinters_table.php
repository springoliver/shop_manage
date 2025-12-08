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
        Schema::create('stoma_pos_receiptprinters', function (Blueprint $table) {
            $table->increments('pos_receiptprinters_id');
            $table->unsignedBigInteger('storeid');
            $table->string('pos_receiptprinters_name', 50);
            $table->string('pos_receiptprinters_ipadress', 51);
            $table->string('pos_receiptprinters_port', 22);
            $table->string('pos_receiptprinters_type', 11);
            $table->string('pos_receiptprinters_profile', 11);
            $table->string('pos_receiptprinters_path', 255)->nullable();
            $table->string('pos_receiptprinters_char_per_line', 11);
            $table->enum('table_number', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('customer_number', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('server_name', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('receipt_number', ['Enable', 'Disable', ''])->default('Enable');
            $table->enum('store_name', ['Enable', 'Disable', ''])->default('Enable');
            $table->enum('date_time', ['Enable', 'Disable', ''])->default('Enable');
            $table->enum('tax_summary', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('tender_details', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('customer_address', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('customer_email', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('customer_tel', ['Enable', 'Disable', ''])->default('Disable');
            $table->enum('service_charge', ['Enable', 'Disable', ''])->default('Enable');
            $table->string('sc_message', 250);
            $table->enum('cut_paper', ['Enable', 'Disable', ''])->default('Enable');
            $table->enum('barcode', ['Enable', 'Disable', ''])->default('Disable');
            $table->timestamp('insertdate')->useCurrent();
            $table->string('insertip', 51);
            $table->string('insertby', 52);
            $table->dateTime('editdate')->nullable();
            $table->string('editip', 51)->nullable();
            $table->string('editby', 52)->nullable();
            
            // Foreign keys
            $table->foreign('storeid')->references('storeid')->on('stoma_store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_pos_receiptprinters');
    }
};

