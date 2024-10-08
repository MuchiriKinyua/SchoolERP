<?php

namespace App\Mpesa;

use App\Models\MpesaSTK;
use Illuminate\Http\Request;
use Iankumu\Mpesa\Facades\Mpesa;

class STKPush
{
    public $failed = false;
    public $response = 'An Unknown Error Occurred';

    public function confirm(Request $request)
    {
        $payload = json_decode($request->getContent());

        if (property_exists($payload, 'Body') && $payload->Body->stkCallback->ResultCode == '0') {
            $merchant_request_id = $payload->Body->stkCallback->MerchantRequestID;
            $checkout_request_id = $payload->Body->stkCallback->CheckoutRequestID;
            $result_desc = $payload->Body->stkCallback->ResultDesc;
            $result_code = $payload->Body->stkCallback->ResultCode;
            $amount = $payload->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $mpesa_receipt_number = $payload->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            $transaction_date = $payload->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $phonenumber = $payload->Body->stkCallback->CallbackMetadata->Item[4]->Value;

            // Fetch the transaction based on the merchant and checkout IDs
            $stkPush = MpesaSTK::where('merchant_request_id', $merchant_request_id)
                ->where('checkout_request_id', $checkout_request_id)
                ->first();

            $data = [
                'result_desc' => $result_desc,
                'result_code' => $result_code,
                'merchant_request_id' => $merchant_request_id,
                'checkout_request_id' => $checkout_request_id,
                'amount' => $amount,
                'mpesa_receipt_number' => $mpesa_receipt_number,
                'transaction_date' => $transaction_date,
                'phonenumber' => $phonenumber,
            ];

            if ($stkPush) {
                $stkPush->fill($data)->save(); // Update the existing record
            } else {
                MpesaSTK::create($data); // Create a new record if it doesn't exist
            }
        } else {
            $this->failed = true;
        }

        return $this;
    }

    // This method should handle STK Query
    public function query($checkoutRequestId)
    {
        // Perform the STK Query
        $response = Mpesa::stkquery($checkoutRequestId);

        /** @var \Illuminate\Http\Client\Response $response */
        $result = $response->json();

        return $result;
    }
}
