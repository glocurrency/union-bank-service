<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnionBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('union_bank_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->unique()->index();
            $table->uuid('processing_item_id')->index();
            $table->uuid('union_bank_sender_id')->unique()->index();
            $table->uuid('union_bank_recipient_id')->unique()->index();

            $table->string('state_code');
            $table->longText('state_code_reason')->nullable();

            $table->string('error_code')->nullable();
            $table->longText('error_code_description')->nullable();

            $table->string('reference')->unique();
            $table->string('remote_reference')->nullable();
            $table->char('receive_currency_code', 3);
            $table->double('receive_amount');

            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->foreign('union_bank_sender_id')->references('id')->on('union_bank_senders');
            $table->foreign('union_bank_recipient_id')->references('id')->on('union_bank_recipients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('union_bank_transactions');
    }
}
