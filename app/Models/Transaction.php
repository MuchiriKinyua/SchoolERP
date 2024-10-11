<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'account_number',
        'amount',
        'merchant_request_id', // Make sure this field exists in your database
        'checkout_request_id',  // Make sure this field exists in your database
        'mpesa_receipt_number',  // Rename this from mpesa_reference if that's what you're saving
        'status',
        'payment_date', // Add this field to the fillable array
    ];
}
