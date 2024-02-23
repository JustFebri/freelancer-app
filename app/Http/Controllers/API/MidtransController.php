<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\client;
use App\Models\freelancer;
use App\Models\order;
use App\Models\payment;
use App\Models\service;
use App\Models\transactions;
use App\Models\user;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        \Midtrans\Config::$serverKey = 'SB-Mid-server-ozqQ40fCNDbY9RqlElgxFL1V';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $authenticatedUserId = auth()->id();

        $oderId = (string) Str::uuid();

        $freelancer = freelancer::where('user_id', '=', $request->seller_id)->first();

        $order = new order;
        $order->order_id = $oderId;
        $order->package_id = $request->package_id;
        $order->client_id = $authenticatedUserId;
        $order->freelancer_id = $freelancer->freelancer_id;
        $order->amount = $request->price;
        $order->order_status = 'token';
        $order->due_date = Carbon::now()->addHours(24);
        $order->save();


        $params = array(
            'transaction_details' => array(
                'order_id' =>  $oderId,
                'gross_amount' => $request->price,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
            ),
            'item_details' => [
                array(
                    "id" => $request->package_id,
                    "price" => $request->price,
                    "quantity" => 1,
                    "name" => $request->service_name . " (" . $request->package_name . ")",
                    "category" => $request->sub_category,
                    "merchant_name" => $request->merchant_name,
                )
            ],
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $payment = new payment;
        $payment->order_id = $oderId;
        $payment->token = $snapToken;
        $payment->client_id = $authenticatedUserId;
        $payment->amount = $request->price;
        $payment->save();

        return response([
            'data' => $snapToken,
        ], 200);
    }

    public function createCustom(Request $request)
    {
        \Midtrans\Config::$serverKey = 'SB-Mid-server-ozqQ40fCNDbY9RqlElgxFL1V';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $authenticatedUserId = auth()->id();

        $oderId = (string) Str::uuid();

        $order = new order;
        $order->order_id = $oderId;
        $order->custom_id = $request->custom_id;
        $order->client_id = $authenticatedUserId;
        $order->freelancer_id = $request->freelancer_id;
        $order->amount = $request->price;
        $order->order_status = 'token';
        $order->due_date = Carbon::now()->addHours(24);
        $order->save();

        Log::info($order);

        $freelancer = DB::table('freelancer as f')
            ->where('f.freelancer_id', '=', $request->freelancer_id)
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->first();

        $service = DB::table('service as s')
            ->where('s.service_id', '=', $request->service_id)
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->first();

        $params = array(
            'transaction_details' => array(
                'order_id' =>  $oderId,
                'gross_amount' => $request->price,
            ),
            'customer_details' => array(
                'first_name' => $request->name,
                'email' => $request->email,
            ),
            'item_details' => [
                array(
                    "id" => $request->custom_id,
                    "price" => $request->price,
                    "quantity" => 1,
                    "name" => $service->title . " (Custom Order)",
                    "category" => $service->subcategory_name,
                    "merchant_name" => $freelancer->name,
                )
            ],
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $payment = new payment;
        $payment->order_id = $oderId;
        $payment->token = $snapToken;
        $payment->client_id = $authenticatedUserId;
        $payment->amount = $request->price;
        $payment->save();

        return response([
            'data' => $snapToken,
        ], 200);
    }

    public function webhook(Request $request)
    {
        Log::info('called');
        $serverKey = 'SB-Mid-server-ozqQ40fCNDbY9RqlElgxFL1V';
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            // Log::info($request->transaction_status);
            switch ($request->transaction_status) {
                case 'capture':
                    Log::info('payment is capture');
                    $order = order::find($request->order_id);
                    $order->order_status = 'pending';
                    $order->due_date = Carbon::now()->addHours(24);
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'capture';
                    $payment->save();
                    break;

                case 'settlement':
                    Log::info('payment is settled');
                    $order = order::find($request->order_id);
                    $order->order_status = 'pending';
                    $order->due_date = Carbon::now()->addHours(24);
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'settlement';
                    $payment->save();

                    $client = client::find($payment->client_id);

                    $transaction = new transactions();
                    $transaction->order_id = $request->order_id;
                    $transaction->user_id = $client->user_id;
                    $transaction->amount = $payment->amount;
                    $transaction->type = 'payment';
                    $transaction->description = 'payment for order ' . $order->order_id;
                    $transaction->save();
                    break;

                case 'pending':
                    Log::info('payment is pending');
                    $order = order::find($request->order_id);
                    $order->order_status = 'awaiting payment';
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'pending';
                    $payment->save();
                    break;

                case 'deny':
                    Log::info('payment is deny');
                    $order = order::find($request->order_id);
                    $order->order_status = 'failure';
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'deny';
                    $payment->save();
                    break;

                case 'cancel':
                    Log::info('payment is cancel');
                    $order = order::find($request->order_id);
                    $order->order_status = 'cancelled';
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'cancel';
                    $payment->save();
                    break;

                case 'expire':
                    Log::info('payment is cancel');
                    $order = order::find($request->order_id);
                    $order->order_status = 'failure';
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'expire';
                    $payment->save();
                    break;

                case 'refund':
                    Log::info('payment is refund');
                    $order = order::find($request->order_id);
                    $order->order_status = 'cancelled';
                    $order->save();

                    $payment = payment::where('order_id', '=', $request->order_id)->first();
                    $payment->payment_type = $request->payment_type;
                    $payment->payment_status = 'refund';
                    $payment->save();
                    break;

                default:
                    Log::info('Case not covered');
            }
            // $order = order::find($request->order_id);
            // $order->update(['order_status' => $request->transaction_status]);
        }
    }

    public function cancel(string $order_id)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.sandbox.midtrans.com/v2/' . $order_id . '/cancel', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic U0ItTWlkLXNlcnZlci1venFRNDBmQ05EYlk5UnFsRWxneEZMMVY6',
            ],
        ]);

        Log::info($response->getBody());
    }

    public function refund(string $order_id)
    {
        $authenticatedUserId = auth()->id();
        $order = order::find($order_id);
        $order->order_status = 'cancelled';
        $order->save();

        $payment = payment::where('order_id', '=', $order_id)->first();
        $payment->payment_status = 'refunded';
        $payment->save();

        $user = user::find($authenticatedUserId);
        $user->balance = $order->amount;
        $user->save();

        $transaction = new transactions();
        $transaction->order_id = $order_id;
        $transaction->user_id = $authenticatedUserId;
        $transaction->amount = -($order->amount);
        $transaction->type = 'refund';
        $transaction->description = 'refund for order ' . $order_id;
        $transaction->save();
    }
}
