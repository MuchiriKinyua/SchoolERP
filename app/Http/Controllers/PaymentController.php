<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Transaction;

class PaymentController extends Controller
{
    public function token() {
        $consumerKey = 'giv5UaFWPIKILI1BkHXEOVFONfthoQldVBcOto2T3OcgeKMF';
        $consumerSecret = '4jKkpLL6OV4XSwD4XejopCANUojMPsabJeGXRDRw0ndB6qf4cnmLLHaoKedmO8sR';
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
        
        if ($response->failed()) {
            \Log::error('Failed to get access token: ', $response->json());
            return response()->json(['error' => 'Unable to get access token.'], 500);
        }

        return $response['access_token'];
    }

    public function initiateStkPush(Request $request) {
        $accessToken = $this->token();
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $BusinessShortCode = 174379;
        $Timestamp = Carbon::now()->format('YmdHis');
        $password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
        $TransactionType = 'CustomerPayBillOnline';

        $PartyA = $request->input('phone');
        
        // Validate phone number format
        if (!preg_match('/^2547[0-9]{8}$/', $PartyA)) {
            return response()->json(['error' => 'Invalid phone number format.'], 400);
        }

        $Amount = $request->input('amount');
        $AccountReference = $request->input('account_number');
        $TransactionDesc = 'Payment for goods';
        $CallbackUrl = 'https://9ef2-196-207-169-62.ngrok-free.app/payments/stkcallback'; 

        // Ensure PartyB is set to BusinessShortCode
        $PartyB = (string)$BusinessShortCode; // Set PartyB to your Business Shortcode

        $response = Http::withToken($accessToken)->post($url, [
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $password,
            'Timestamp' => $Timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB, // Ensure PartyB is set here
            'PhoneNumber' => $PartyA,
            'CallBackURL' => $CallbackUrl,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc,
        ]);

        // Log the response for debugging
        \Log::info('STK Push Response: ', $response->json());

        // Return the response to the user
        return response()->json($response->json());
    }

    public function stkCallback(Request $request) {
        $data = $request->all();
    
        // Log the callback data for debugging
        Storage::disk('local')->put('stk_callback.txt', json_encode($data));
    
        // Check if the callback contains a valid transaction
        if (isset($data['Body']['stkCallback']['ResultCode']) && $data['Body']['stkCallback']['ResultCode'] == 0) {
            // Successful transaction
            $callbackMetadata = $data['Body']['stkCallback']['CallbackMetadata']['Item'];
    
            // Initialize variables
            $mpesaReference = null;
            $paymentDate = null;
            $amount = null;
            $phoneNumber = null;
    
            // Extract values based on expected keys
            foreach ($callbackMetadata as $item) {
                switch ($item['Name']) {
                    case 'MpesaReceiptNumber':
                        $mpesaReference = $item['Value'];
                        break;
                    case 'TransactionDate':
                        // Parse the transaction date
                        $paymentDate = Carbon::createFromFormat('YmdHis', $item['Value']);
                        break;
                    case 'Amount':
                        $amount = $item['Value'];
                        break;
                    case 'PhoneNumber':
                        $phoneNumber = $item['Value'];
                        break;
                }
            }
            
            // Log the extracted values for debugging
            \Log::info('Extracted values: ', [
                'mpesaReference' => $mpesaReference,
                'paymentDate' => $paymentDate,
                'amount' => $amount,
                'phoneNumber' => $phoneNumber,
            ]);
    
            // Assume account_number is fixed or derived from the transaction
            $accountNumber = '1'; // Replace with your logic to get the account number if needed
    
            // Save transaction to the database
            Transaction::create([
                'phone' => $phoneNumber,
                'amount' => $amount,
                'merchant_request_id' => $data['Body']['stkCallback']['MerchantRequestID'], // Save merchant request ID
                'checkout_request_id' => $data['Body']['stkCallback']['CheckoutRequestID'], // Save checkout request ID
                'mpesa_receipt_number' => $mpesaReference, // M-Pesa receipt number
                'payment_date' => $paymentDate, // Payment date
                'status' => 'successful', // Mark as successful
                'account_number' => $accountNumber, // Add account number
            ]);
            
    
            return response()->json(['message' => 'Transaction recorded successfully'], 200);
        } else {
            // Handle failed transaction here
            \Log::warning('Transaction failed: ', $data);
            return response()->json(['message' => 'Transaction failed'], 400);
        }
    }     
}
