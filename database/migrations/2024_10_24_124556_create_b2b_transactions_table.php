<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

    class CreateB2bTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('b2b_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('initiator_name');
            $table->string('paybill');
            $table->string('account_number');
            $table->decimal('amount', 15, 2);
            $table->string('remarks')->nullable();
            $table->string('occasion')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_description')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_status')->default('Pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('b2b_transactions');
    }
}


