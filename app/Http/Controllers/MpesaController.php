<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Safaricom\Mpesa\Mpesa; // Ensure you import the Mpesa class

class MpesaController extends Controller
{
    public function token()
    {
        $consumerKey = 'DwTKiQJMEO7fH20yFLAwDEdTIgUs2QMofz4lxwBBGJoaC7Hq';
        $consumerSecret = '9MU7cDOrodVAZXsRw8DEDu3BZweMSpPZy8xwuGjeR05yGtj04TrYLkowEkbeSDGG';
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        
        $response = Http::withBasicAuth($consumerKey, $consumerSecret)->get($url);
        return $response->json(); // Return the response as JSON
    }

    public function pay(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'amount' => 'required|numeric',
            'phone' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'account_number' => 'required|string',
        ]);
    
        // Logic for processing the payment...
        try {
            // Initialize the Mpesa class
            $mpesa = new Mpesa();
            
            // Execute the STK Push Simulation
            $response = $mpesa->STKPushSimulation(
                env('MPESA_SHORTCODE', '174379'),
                env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'),
                '174379', // BusinessShortCode
                $request->amount, // Use the amount from the form
                $request->phone, // Use the phone number from the form
                '174379', // PartyB (business shortcode)
                $request->phone, // PhoneNumber (customer phone number)
                env('MPESA_CALLBACK_URL', 'http://127.0.0.1:8000/api/mpesa/callback'), // CallBackURL
                'AccountReference', // AccountReference (unique to the transaction)
                'Payment for services', // TransactionDesc
                'Test transaction' // Remarks
            );
    
            // Log the M-Pesa response
            \Log::info('M-Pesa response: ', ['response' => $response]); // Use an array for context
    
            // Record the transaction in the database
            Transaction::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'account_number' => $request->account_number,
                'amount' => $request->amount,
                'transaction_id' => $response->transaction_id ?? null, // Adjust based on the response structure
            ]);
    
            // Return success message to the user
            return redirect()->back()->with('success', 'Payment processed successfully!');
        } catch (\Exception $e) {
            // Log the error and return a JSON response with the error message
            \Log::error('Error in payment processing: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an issue processing your payment.');
        }
    }
    public function callback(Request $request)
{
    // Log the callback response
    \Log::info('M-Pesa Callback Response: ', ['data' => $request->all()]);

    // Handle the callback response
    // You might want to update the transaction status in the database here based on the response

    return response()->json(['status' => 'success']);
}   
}
