<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('phone');
        $table->string('account_number');
        $table->decimal('amount', 10, 2);
        $table->string('merchant_request_id')->nullable(); // Optional
        $table->string('checkout_request_id')->nullable(); // Optional
        $table->string('mpesa_receipt_number')->nullable(); // M-Pesa receipt number
        $table->timestamp('payment_date')->nullable(); // Payment date
        $table->string('status');
        $table->timestamps();
    });
    
    
}


    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
