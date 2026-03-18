<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Container\Attributes\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\order;
use App\Models\transactions;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use PgSql\Lob;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    
    //
    // public function createTransaction(Request $request)
    // {
    //     Log::info("Fungsi Terpanggil");
    //     // Production

    //     Log::info("Setting Midtrans");
    //     \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    //     \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
    //     \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
    //     \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS');

    //     Log::info("Validasi Data 1");
    //     Log::info($request);
    //     // Validate the request data
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'phone' => 'required|string|max:15',
    //         'email' => 'required|email',
    //         'quantity' => 'required|integer|min:1',
    //         'gross_amount' => 'required|integer|min:5',
        
    //     ]);

    //     Log::info("Validasi Data 2");
        
    //     $transaction_details = [
    //         'order_id' => uniqid(),
    //         'gross_amount' => $request->gross_amount * $request->quantity, // Total Amount in IDR
            
    //     ];

    //     Log::info("Validasi Data 3");
    //     $customer_details = [
    //         'first_name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //     ];

    //     Log::info("Validasi Data 4");
    //     $params = [
    //         'transaction_details' => $transaction_details,
    //         'customer_details' => $customer_details,
    //     ];
    //     Log::info($params);


    //     Log::info("Disini Tokennya");

    //     try {
    //         Log::info("Berhasil");
    //         $snapToken = Snap::getSnapToken($params);
    //         return response()->json(['snapToken' => $snapToken]);
    //     } catch (\Exception $e) {
    //         Log::info("Pengiriman Gagal");
    //         Log::error($e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    //     Log::info($snapToken);

    //     // $snapToken = Snap::getSnapToken($params);
    //     // return response()->json(['snapToken' => $snapToken]);

    // }
    
        public function createTransaction(Request $request)
        {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'quantity' => 'required|integer|min:1',
            'gross_amount' => 'required|integer|min:1',
            // 'freelancer_id' => 'required|exists:users,id',
            'freelancer_id' => 'required|exists:freelancer,freelancer_id',
            'package_id' => 'required|integer|min:1'
        ]);
    
        // \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // \Midtrans\Config::$isProduction = config('midtrans.is_production');
        // \Midtrans\Config::$isSanitized = true;
        // \Midtrans\Config::$is3ds = true;
        
        Log::info("Setting Midtrans");
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = env('MIDTRANS_IS_SANITIZED');
        \Midtrans\Config::$is3ds = env('MIDTRANS_IS_3DS');
    
        DB::beginTransaction();
    
        try {
            // 1️⃣ Buat ORDER
            $order = order::create([
                'order_id' => uniqid('ORDER-'),
                'client_id' => Auth::id(),
                'freelancer_id' => $request->freelancer_id,
                'package_id' => $request->package_id,
                'order_status' => 'awaiting payment',
                'due_date' => Carbon::now()->addHours(24),
                'amount' => $request->gross_amount * $request->quantity,
            ]);
    
            // 2️⃣ Buat TRANSACTION
            $transaction = transactions::create([
                'order_id' => $order->order_id,
                'user_id' => Auth::id(),
                'amount' => $order->amount,
                'type' => 'payment',
                'description' => 'Midtrans Payment',
            ]);
    
            // 3️⃣ Request Snap Token
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $order->order_id,
                    'gross_amount' => $order->amount,
                ],
                'customer_details' => [
                    'first_name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    
                    // 'first_name' => 'Sean Alden',
                    // 'email' => 'sa2596491@gmail.com',
                    // 'phone' => '081234567890',
                ],
            ]);
    
            // 4️⃣ Simpan token
            $order->update(['snap_token' => $snapToken]);
    
            DB::commit();
    
            return response()->json([
                'snapToken' => $snapToken,
                'order_id' => $order->order_id,
            ]);
    
        } 
        // catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::error($e->getMessage());
        //     Log::info('Snap Token Generated: ' . $snapToken);
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
        
        catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'message' => 'Transaction failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
        public function midtransWebhook(Request $request)
        {
        Log::info('Midtrans Webhook Received', $request->all());
    
        // 1️⃣ Ambil data penting
        $orderId = $request->order_id;
        $transactionStatus = $request->transaction_status;
        $paymentType = $request->payment_type;
        $fraudStatus = $request->fraud_status;
        $signatureKey = $request->signature_key;
    
        // 2️⃣ Validasi Signature (WAJIB)
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $payload = $orderId .
            $request->status_code .
            $request->gross_amount .
            $serverKey;
    
        if (hash('sha512', $payload) !== $signatureKey) {
            Log::warning('Invalid Midtrans signature');
            return response()->json(['message' => 'Invalid signature'], 403);
        }
    
        // 3️⃣ Cari order
        $order = order::where('order_id', $orderId)->first();
    
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    
        // 4️⃣ Mapping STATUS MIDTRANS → STATUS BISNIS
        switch ($transactionStatus) {
    
            case 'pending':
                $order->order_status = 'pending';
                break;
    
            case 'capture':
                if ($paymentType === 'credit_card' && $fraudStatus === 'accept') {
                    $order->order_status = 'in progress';
                }
                break;
    
            case 'settlement':
                $order->order_status = 'in progress';
                break;
    
            case 'expire':
            case 'cancel':
            case 'deny':
                $order->order_status = 'cancelled';
                break;
        }
    
        $order->save();
    
        return response()->json(['message' => 'Webhook processed']);
    }
}