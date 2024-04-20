<?php

namespace App\Http\Controllers;

use App\Models\client;
use App\Models\custom_orders;
use App\Models\freelancer;
use App\Models\order;
use App\Models\payment;
use App\Models\service;
use App\Models\service_package;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function order()
    {
        $type = order::latest()->get();

        foreach($type as $item){
            if($item->custom_id == null){
                $item->package = service_package::find($item->package_id);
                $item->service_id = $item->package->service_id;

                $item->service = service::find($item->package->service_id);
                $item->freelancer = DB::table('freelancer as f')
                ->leftJoin('user as u','u.user_id','=','f.user_id')
                ->where('f.freelancer_id','=',$item->freelancer_id)
                ->first();

            }else{
                $item->package = custom_orders::find($item->custom_id);
                $item->service_id = $item->package->service_id;

                $item->service = service::find($item->package->service_id);
                $item->onsite_date = $item->package->delivery_days;
                $item->address = $item->package->address;

                $item->freelancer = DB::table('freelancer as f')
                ->leftJoin('user as u','u.user_id','=','f.user_id')
                ->where('f.freelancer_id','=',$item->freelancer_id)
                ->first();
                
            }

            $tempClient = client::find($item->client_id);
            $userClient = user::find($tempClient->user_id);

            $tempFreelancer = freelancer::find($item->freelancer_id);
            $userFreelancer = user::find($tempFreelancer->user_id);

            $item->buyer = $userClient->name;
            $item->seller = $userFreelancer->name;

            $payment = payment::where('order_id',$item->order_id)->first();
            $item->payment = $payment->payment_type;
        }
        Log::info($type);
        return view('layouts.order', compact('type'));
    }
}
