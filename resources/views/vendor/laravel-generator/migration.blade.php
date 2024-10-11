<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{ $config->tableName }}', function (Blueprint $table) {
            // Define your fields here
            // For example:
            $table->id(); // This creates an auto-incrementing ID column
            $table->string('phone'); // Phone number column
            $table->string('account_number')->nullable(); // Account number column, nullable
            $table->decimal('amount', 10, 2); // Amount column
            $table->timestamps(); // Created at and updated at timestamps
            $table->string('mpesa_reference')->nullable(); // M-Pesa reference column
            $table->string('status'); // Status column
            $table->string('merchant_request_id'); // Merchant request ID column
            $table->string('checkout_request_id'); // Checkout request ID column
            $table->string('mpesa_receipt_number')->nullable(); // M-Pesa receipt number column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $config->tableName }}'); // Use dropIfExists to prevent errors if the table doesn't exist
    }
};
