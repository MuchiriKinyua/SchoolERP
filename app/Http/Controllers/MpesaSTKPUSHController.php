<?php

namespace App\Http\Controllers;

use App\Models\MpesaSTK;
use Iankumu\Mpesa\Facades\Mpesa;
use Illuminate\Http\Request;

class MpesaSTKPUSHController extends Controller
{
    public function STKPush(Request $request)
    {
        $amount = $request->input('amount');
        $phoneno = $request->input('phonenumber');
        $account_number = $request->input('account_number');

        // Call the STK Push API
        $response = Mpesa::stkpush()->register($phoneno, $amount, $account_number);
        
        // Process the response
        $result = $response->json(); 

        // Log the response for debugging
        Log::info('M-Pesa STK Push Response: ', $result);

        // Save to the database if successful
        if (isset($result['MerchantRequestID']) && isset($result['CheckoutRequestID'])) {
            MpesaSTK::create([
                'merchant_request_id' => $result['MerchantRequestID'],
                'checkout_request_id' => $result['CheckoutRequestID'],
                'amount' => $amount,
                'phonenumber' => $phoneno,
            ]);
        }

        return $result;
    }

    public function STKConfirm(Request $request)
    {
        $stk_push_confirm = (new STKPush())->confirm($request);

        if ($stk_push_confirm) {
            $this->result_code = 0;
            $this->result_desc = 'Success';
        } else {
            $this->result_code = 1;
            $this->result_desc = 'An error occurred';
        }

        return response()->json([
            'ResultCode' => $this->result_code,
            'ResultDesc' => $this->result_desc
        ]);
    }
}
