<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Safaricom\Mpesa\Mpesa;

class MpesaController extends Controller
{
    // Method to get an access token
    public function token()
    {
        $consumerKey = env('MPESA_CONSUMER_KEY');
        $consumerSecret = env('MPESA_CONSUMER_SECRET');
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; 
        
        // Request the access token using the consumer key and secret
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);

        // Check if the request was successful
        if ($response->successful()) {
            $token = $response->json()['access_token'];

            // Store the token in the session for later use
            session(['mpesa_access_token' => $token]);

            return $token;
        } else {
            return response()->json(['error' => 'Unable to fetch access token'], 500);
        }
    }

    // Method to initiate STK Push
    public function pay(Request $request)
    {
        // Check if the access token is available in session
        $accessToken = session('mpesa_access_token');

        // If access token is not found, get a new one
        if (!$accessToken) {
            $accessToken = $this->token();
            if (!$accessToken) {
                return response()->json(['error' => 'Invalid access token'], 401);
            }
        }

        // Validate the incoming request
        $request->validate([
            'amount' => 'required|numeric',
            'phone' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'account_number' => 'required|string',
        ]);

        try {
            // Initialize the Mpesa class
            $mpesa = new Mpesa();

            // Execute the STK Push Simulation
            $response = $mpesa->STKPushSimulation(
                env('MPESA_SHORTCODE', '174379'),
                env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
                '174379', // BusinessShortCode
                $request->amount, // Amount from the form
                $request->phone, // Phone number from the form
                '174379', // PartyB (business shortcode)
                $request->phone, // PhoneNumber (customer phone number)
                env('MPESA_CALLBACK_URL', 'http://127.0.0.1:8000/api/mpesa/callback'), // Callback URL
                $request->account_number, // AccountReference unique to the transaction
                'Payment for services', // Transaction description
                'Test transaction' // Remarks
            );

            // Log the M-Pesa response
            \Log::info('M-Pesa response: ', ['response' => $response]);

            // Parse response and extract necessary details (adjust based on your response structure)
            $responseData = json_decode($response, true);

            // Record the transaction in the database
            Transaction::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'account_number' => $request->account_number,
                'amount' => $request->amount,
                'transaction_id' => $responseData['CheckoutRequestID'] ?? null, // Adjust based on actual response
            ]);

            // Return success message to the user
            return redirect()->back()->with('success', 'Payment processed successfully!');
        } catch (\Exception $e) {
            // Log the error and return a JSON response with the error message
            \Log::error('Error in payment processing: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue processing your payment.');
        }
        \Log::error('Error in payment processing: ', [
            'message' => $e->getMessage(),
            'response' => $response ?? null
        ]);        
    }

    // Callback method to handle M-Pesa response
    public function callback(Request $request)
    {
        \Log::info('M-Pesa Callback Response: ', ['data' => $request->all()]);
    
        // Example of handling the success/failure cases based on the response
        $callbackData = $request->all();
        
        // Find the transaction using the CheckoutRequestID from the callback
        $transaction = Transaction::where('transaction_id', $callbackData['CheckoutRequestID'])->first();
        
        if ($transaction) {
            // Update the transaction status based on the callback result
            $transaction->status = $callbackData['ResultCode'] == 0 ? 'success' : 'failed';
            $transaction->save();
        }
    
        return response()->json(['status' => 'success']);
    }
    
}
