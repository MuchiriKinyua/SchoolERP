<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function pay(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'account_number' => 'required|string|max:20',
        ]);

        $phone_number = $request->input('phone_number');
        $amount = 100; // Set the amount you want to charge

        try {
            // Call the C2B API to create a payment
            $response = Mpesa::c2b()->register($phone_number, $amount);

            // Check response for success or failure
            if ($response['success']) {
                return back()->with('success', 'Payment successful!');
            } else {
                return back()->with('error', 'Payment failed. Please check your details.');
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa Payment Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing your payment.');
        }
    }
}
