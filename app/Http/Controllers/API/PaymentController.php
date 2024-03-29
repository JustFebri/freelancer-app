<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Payout\PayoutApi;
use Xendit\PaymentRequest\PaymentRequestApi;

// class PaymentController extends Controller
// {
//     public function __construct()
//     {
//         Configuration::setXenditKey('xnd_development_pMYibEUiB66qER2pdkw1BLJlZQIkJee23tZDNS6FOkbIvtT0ns45VutFxxN');
//     }

//     public function create(Request $request)
//     {
//         $params = [
//             'external_id' => (string) Str::uuid(),
//             'amount' => $request->amount,
//             'description' => $request->description,
//             'payer_email' => $request->payer_email,
//             'invoice_duration' => 86400,
//             'customer_notification_preference' => [
//                 'invoice_created' => [
//                     'email'
//                 ],
//                 'invoice_reminder' => [
//                     'email'
//                 ],
//                 'invoice_paid' => [
//                     'email'
//                 ],
//                 'invoice_expired' => [
//                     'email'
//                 ]
//             ],
//             'currency' => 'IDR',
//             'reminder_time' => 1,
//             'should_send_email' => true,
//         ];

//         $apiInstance = new InvoiceApi();
//         $createInvoice = $apiInstance->createInvoice($params);

//         $payment = new Payment;
//         $payment->status = 'pending';
//         $payment->checkout_link =  $createInvoice['invoice_url'];
//         $payment->external_id = $params['external_id'];
//         $payment->save();

//         return response()->json(['data' => $createInvoice['invoice_url']]);
//     }

//     public function webhook(Request $request)
//     {
//         Log::info($request);
//         $apiInstance = new InvoiceApi();
//         $getlnvoice = $apiInstance->getInvoiceById($request->id);
//         Log::info($getlnvoice);
//         // // Get data 
//         // $payment = Payment::where('external_id', $request->external_id)->firstOrFail();

//         // if ($payment->status == 'settled') {
//         //     return response()->json(['data' => 'Payment has been already processed']);
//         // }
//         // // Update status payment 
//         // $payment->status = strtolower($getlnvoice['status']);
//         // $payment->save();

//         return response()->json(['data' => 'Success']);
//     }

//     public function payout(Request $request)
//     {
//         Log::info($request);
        
//         // // Get data 
//         // $payment = Payment::where('external_id', $request->external_id)->firstOrFail();

//         // if ($payment->status == 'settled') {
//         //     return response()->json(['data' => 'Payment has been already processed']);
//         // }
//         // // Update status payment 
//         // $payment->status = strtolower($getlnvoice['status']);
//         // $payment->save();

//         return response()->json(['data' => 'Success']);
//     }
// }
