<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function pay(Request $request)
{
    // Validate the form inputs
    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:15',
        'account_number' => 'required|string|max:20',
    ]);

    $phone_number = $request->input('phone_number');
    $amount = 1; // Use 1 for testing; adjust as necessary
    $account_number = $request->input('account_number'); // Fetch the account number from the request

    try {
        // Get the access token
        $accessToken = config('mpesa.access_token');
        
        // Request a new access token
        $accessToken = Mpesa::getAccessToken();
        Log::info('Access Token: ' . $accessToken);

        // Call the STK Push API to create a payment
        $response = Mpesa::stkpush($phone_number, $amount, $account_number); // Pass the necessary parameters

        // Log the response for debugging
        Log::info('M-Pesa STK Push Response: ', (array)$response);

        // Check response for success or failure
        if (isset($response['success']) && $response['success']) {
            return back()->with('success', 'Payment successful!');
        } else {
            Log::error('M-Pesa STK Push Response Error: ', (array)$response);
            return back()->with('error', 'Payment failed. Please check your details.');
        }

    } catch (\Exception $e) {
        Log::error('M-Pesa Payment Error: ' . $e->getMessage());
        return back()->with('error', 'An error occurred while processing your payment.');
    }
}
}




