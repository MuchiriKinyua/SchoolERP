<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Safaricom\Mpesa\Mpesa;

class MpesaController extends Controller
{
    public function initiateStkPush()
    {
        // Initialize the Mpesa class
        $mpesa = new Mpesa();
        
        // Execute the STK Push Simulation
        $response = $mpesa->STKPushSimulation(
            env('MPESA_SHORTCODE'),          // Shortcode from environment variables
            env('MPESA_PASSKEY'),            // Passkey from environment variables
            '174379',                        // BusinessShortCode (same as shortcode)
            '100',                           // Amount to be charged
            '254713030677',                  // PartyA (the customer's phone number)
            '174379',                        // PartyB (business shortcode)
            '254713030677',                  // PhoneNumber (customer phone number)
            'https://example.com/callback',  // CallBackURL
            'TestAccount',                   // AccountReference (unique to the transaction)
            'Payment for services',          // TransactionDesc (description of the transaction)
            'Test transaction'               // Remarks
        );

        // Return the response
        return $response;
    }
}
