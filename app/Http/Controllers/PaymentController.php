<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon; 

class PaymentController extends Controller
{
    public function token(){
        $consumerKey = 'DwTKiQJMEO7fH20yFLAwDEdTIgUs2QMofz4lxwBBGJoaC7Hq';
        $consumerSecret = '9MU7cDOrodVAZXsRw8DEDu3BZweMSpPZy8xwuGjeR05yGtj04TrYLkowEkbeSDGG';
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; // Correct endpoint for token
    
        // Make a POST request with Basic Auth
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->post($url);
    
        // Log the entire response for debugging
        \Log::info("Response from token endpoint: " . $response->body());
    
        // Check for successful response
        if ($response->successful()) {
            return $response->json()['access_token']; 
        }
    
        // Log error response for debugging
        \Log::error("Unable to retrieve access token: " . $response->body());
        throw new \Exception("Unable to retrieve access token: " . $response->body());
    }       

    public function initiateStkPush(){
        $accessToken = $this->token(); // This will now be a string
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $BusinessShortCode = 174379;
        $Timestamp = Carbon::now()->format('YmdHis'); 
        $password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
        $TransactionType = 'CustomerPayBillOnline';
        $Amount = 1;
        $PartyA = 254713030677; // Your phone number in international format
        $PartyB = 174379;
        $PhoneNumber = 254713030677;
        $CallbackUrl = ''; // Set your callback URL if applicable
        $AccountReference = 'Coders base';
        $TransactionDesc = 'Payment for goods';
    
        // Make the STK push request
        $response = Http::withToken($accessToken)->post($url, [
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $Timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallbackURL' => $CallbackUrl,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc,
        ]);
    
        // Log the response for debugging
        \Log::info("Response from STK push: " . $response->body());
        
        // Handle the response from the STK push
        return $response; // Return the response for further handling
    }
    
}
