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
        Schema::create('c2brequests', function (Blueprint $table) {
        $table->id();
        $table->string('TransactionType');
        $table->string('TransID');
        $table->string('TransTime');
        $table->decimal('TransAmount', 10, 2);
        $table->string('BusinessShortCode');
        $table->string('BillRefNumber')->nullable();
        $table->string('InvoiceNumber')->nullable();
        $table->decimal('OrgAccountBalance', 10, 2)->nullable();
        $table->string('ThirdPartyTransID')->nullable();
        $table->string('MSISDN');
        $table->string('FirstName')->nullable();
        $table->string('MiddleName')->nullable();
        $table->string('LastName')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c2brequests');
    }
};
