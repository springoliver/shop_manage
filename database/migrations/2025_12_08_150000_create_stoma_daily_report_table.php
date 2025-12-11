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
        Schema::create('stoma_daily_report', function (Blueprint $table) {
            $table->increments('reportid');
            $table->integer('storeid');
            $table->integer('weekno');
            $table->date('date');
            $table->decimal('total_sell', 10, 2);
            $table->decimal('total_sell_r', 10, 2);
            $table->decimal('cc_sell', 10, 2);
            $table->decimal('cc_sell2', 10, 2);
            $table->string('cc_ref', 22)->nullable();
            $table->string('cc_ref2', 22)->nullable();
            $table->decimal('cashsales', 10, 2);
            $table->string('number_customer', 11)->nullable();
            $table->decimal('aph', 10, 2);
            $table->decimal('total_cash_receipt', 10, 2);
            $table->text('payout_notes')->nullable();
            $table->integer('todaysfloat')->nullable();
            $table->integer('used_voucher');
            $table->text('used_vouchers')->nullable();
            $table->integer('sold_voucher');
            $table->text('sold_vouchers')->nullable();
            $table->integer('refunds');
            $table->integer('complementary')->nullable();
            $table->text('void_notes')->nullable();
            $table->integer('vaste')->nullable();
            $table->decimal('today_cash', 10, 2)->nullable();
            $table->integer('s_safe')->nullable();
            $table->text('any_issue')->nullable();
            $table->text('empshift_notes')->nullable();
            $table->integer('ballance');
            $table->string('today_tip', 11)->nullable();
            $table->string('house_tip', 11)->nullable();
            $table->integer('nextdayfloat')->nullable();
            $table->enum('status', ['Ok', 'Open', 'Issue'])->default('Open');
            $table->string('insertby', 51)->nullable();
            $table->dateTime('insertdate');
            $table->string('insertip', 61);
            $table->timestamp('modifieddate')->useCurrent()->useCurrentOnUpdate();
            $table->string('modifiedip', 61)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoma_daily_report');
    }
};

