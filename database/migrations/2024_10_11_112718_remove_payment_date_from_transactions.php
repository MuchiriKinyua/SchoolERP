<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePaymentDateFromTransactions extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_date');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->timestamp('payment_date')->nullable();
        });
    }
}
