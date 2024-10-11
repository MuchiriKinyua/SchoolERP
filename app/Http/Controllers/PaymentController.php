<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\Stkrequest;

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
    
        $PartyA = 254713030677;
        $Amount = 1;
        $AccountReference = $request->input('account_number');
        $TransactionDesc = 'Payment for goods';
        $CallbackUrl = 'https://bec1-196-207-169-62.ngrok-free.app/payments/stkcallback';
        $PartyB = (string)$BusinessShortCode;
    
        try {
            $response = Http::withToken($accessToken)->post($url, [
                'BusinessShortCode' => $BusinessShortCode,
                'Password' => $password,
                'Timestamp' => $Timestamp,
                'TransactionType' => $TransactionType,
                'Amount' => $Amount,
                'PartyA' => $PartyA,
                'PartyB' => $PartyB,
                'PhoneNumber' => $PartyA,
                'CallBackURL' => $CallbackUrl,
                'AccountReference' => $AccountReference,
                'TransactionDesc' => $TransactionDesc,
            ]);
    
            // Log the response for debugging
            \Log::info('STK Push Response: ', $response->json());
    
            $res = json_decode($response->body());
    
            // Check if ResponseCode exists
            if (isset($res->ResponseCode)) {
                $ResponseCode = $res->ResponseCode; 
    
                if ($ResponseCode == 0) {
                    $MerchantRequestID = $res->MerchantRequestID;
                    $CheckoutRequestID = $res->CheckoutRequestID;
                    $CustomerMessage = $res->CustomerMessage;
    
                    $payment = new STKrequest;
                    $payment->phone = $PartyA; // Corrected from $PhoneNumber
                    $payment->amount = $Amount;
                    $payment->reference = $AccountReference;
                    $payment->description = $TransactionDesc;
                    $payment->MerchantRequestID = $MerchantRequestID;
                    $payment->CheckoutRequestID = $CheckoutRequestID;
                    $payment->status = 'Requested';
                    $payment->save();
    
                    return $CustomerMessage;
                } else {
                    // Handle other response codes
                    return response()->json(['error' => 'Payment request failed.', 'response' => $res], 400);
                }
            } else {
                return response()->json(['error' => 'Unexpected response structure.', 'response' => $res], 500);
            }
    
        } catch (Throwable $e) {
            \Log::error('Error initiating STK Push: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the request.'], 500);
        }
    }
    

    public function stkCallback(Request $request) {
        $data = file_get_contents('php://input');
        Storage::disk('local')->put('stk.txt', $data);
    
        $response = json_decode($data);
        
        // Verify the structure of the response to avoid undefined property notices
        if (isset($response->Body->stkCallback)) {
            $ResultCode = $response->Body->stkCallback->ResultCode;
    
            if ($ResultCode == 0) {
                $MerchantRequestID = $response->Body->stkCallback->MerchantRequestID;
                $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;
                $ResultDesc = $response->Body->stkCallback->ResultDesc;
                $Amount = $response->Body->stkCallback->CallbackMetadata->Item[0]->Value;
                $MpesaReceiptNumber = $response->Body->stkCallback->CallbackMetadata->Item[1]->Value;
                $TransactionDate = $response->Body->stkCallback->CallbackMetadata->Item[3]->Value;
                $PhoneNumber = $response->Body->stkCallback->CallbackMetadata->Item[4]->Value;
    
                $payment = STKrequest::where('CheckoutRequestID', $CheckoutRequestID)->firstOrFail();
                $payment->status = 'Paid';
                $payment->TransactionDate = $TransactionDate;
                $payment->MpesaReceiptNumber = $MpesaReceiptNumber;
                $payment->ResultDesc = $ResultDesc;
                $payment->save();
            } else {
                $CheckoutRequestID = $response->Body->stkCallback->CheckoutRequestID;
                $ResultDesc = $response->Body->stkCallback->ResultDesc;
                $payment = STKrequest::where('CheckoutRequestID', $CheckoutRequestID)->firstOrFail();
            
                $payment->ResultDesc = $ResultDesc;
                $payment->status = 'Failed';
                $payment->save();
            }
        } else {
            \Log::error('Unexpected callback structure: ', $data);
        }
    }
    
    
    
    // public function stkQuery() {
    //     $accessToken = $this->token();
    //     $BusinessShortCode = 174379;
    //     $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    //     $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
    //     $Timestamp = Carbon::now()->format('YmdHis');
    //     $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
    //     $CheckoutRequestID = 'ws_CO_11102024130250238745416760'; // Replace with the actual CheckoutRequestID

    //     $response = Http::withToken($accessToken)->post($url, [
    //         'BusinessShortCode' => $BusinessShortCode,
    //         'Timestamp' => $Timestamp,
    //         'Password' => $Password,
    //         'CheckoutRequestID' => $CheckoutRequestID
    //     ]);

    //     return $response->json();
    // }
}
