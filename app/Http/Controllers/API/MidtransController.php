<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\order;
use App\Models\payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Payout\PayoutApi;
use Xendit\PaymentRequest\PaymentRequestApi;

class MidtransController extends Controller
{
    public function create(Request $request)
    {
        Log::info('called');
        \Midtrans\Config::$serverKey = 'SB-Mid-server-ozqQ40fCNDbY9RqlElgxFL1V';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $id = (string) Str::uuid();

        $order = new order;
        $payment = new payment;

        $params = array(
            'transaction_details' => array(
                'order_id' => $id,
                'gross_amount' => $request->price,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
            ),
            "item_details" => [
                array(
                    "id" => $request->item_id,
                    "price" => $request->price,
                    "quantity" => 1,
                    "name" => $request->item_name,
                    "category" => $request->category,
                    "merchant_name" => $request->merchant_name,
                )
            ],
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return response([
            'data' => $snapToken,
        ], 200);
    }
}
