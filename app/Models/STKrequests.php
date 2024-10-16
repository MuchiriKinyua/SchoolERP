<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STKrequests extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'STKrequests'; // Specify your table name

    // Fillable properties
    protected $fillable = [
        'phone',
        'amount',
        'reference',
        'description',
        'MerchantRequestID',
        'CheckoutRequestID',
        'status',
        'TransactionDate',
        'MpesaReceiptNumber',
        'ResultDesc',
    ];

    // If you have timestamps in your table, you can keep the following line
    public $timestamps = true; // Set to false if you don't have created_at and updated_at columns
}
