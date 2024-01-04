<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Payout\PayoutApi;
use Xendit\PaymentRequest\PaymentRequestApi;

class PaymentController extends Controller
{
    public function __construct()
    {
        Configuration::setXenditKey('xnd_development_pMYibEUiB66qER2pdkw1BLJlZQIkJee23tZDNS6FOkbIvtT0ns45VutFxxN');
    }

    public function create(Request $request)
    {
        $params = [
            'external_id' => (string) Str::uuid(),
            'amount' => $request->amount,
            'description' => $request->description,
            'payer_email' => $request->payer_email,
            'invoice_duration' => 86400,
            'customer_notification_preference' => [
                'invoice_created' => [
                    'email'
                ],
                'invoice_reminder' => [
                    'email'
                ],
                'invoice_paid' => [
                    'email'
                ],
                'invoice_expired' => [
                    'email'
                ]
            ],
            'currency' => 'IDR',
            'reminder_time' => 1,
            'should_send_email' => true,
        ];

        $apiInstance = new InvoiceApi();
        $createInvoice = $apiInstance->createInvoice($params);

        $payment = new payment;
        $payment->status = 'pending';
        $payment->checkout_link =  $createInvoice['invoice_url'];
        $payment->external_id = $params['external_id'];
        $payment->save();

        return response([
            'data' => $createInvoice['invoice_url']
        ], 200);
    }

    public function webhook(Request $request)
    {
        $apiInstance = new InvoiceApi();
        $getlnvoice = $apiInstance->getInvoiceById($request->id);
        // Get data 
        $payment = Payment::where('external_id', $request->external_id)->firstOrFail();

        if ($payment->status == 'settled') {
            return response()->json(['data' => 'Payment has been already processed']);
        }
        // Update status payment 
        $payment->status = strtolower($getlnvoice['status']);
        $payment->save();

        return response()->json(['data' => 'Success']);
    }

    public function payout(Request $request)
    {
        $apiInstance = new PayoutApi();
        $external_id = (string) Str::uuid();
        // $create_payout_request = [
        //     "external_id" => $external_id,
        //     "amount" => $request->amount,
        //     "email" => "$request->email",
        // ];
        $testing = [
            "reference_id" => "DISB-001",
            "currency" => "IDR",
            "channel_code" => "ID_OVO",
            "channel_properties" => [
                "account_holder_name" => "Febri",
                "account_number" => "081330914532"
            ],
            "amount" => 39000,
            "description" => "Test Ovo",
            "type" => "DIRECT_DISBURSEMENT",
            "receipt_notification" => [
                "email_to" =>  ["febri.k150201@gmail.com"],
                "email_cc" => ["febri.k150201@gmail.com"]
            ]
        ];

        $result = $apiInstance->createPayout($external_id, null, $testing);
        return response()->json(['data' => 'Success']);
    }
}
