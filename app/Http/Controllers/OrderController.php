<?php

namespace App\Http\Controllers;

use App\Models\freelancer;
use App\Models\order;
use App\Models\payment;
use App\Models\service_package;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function order()
    {
        $type = order::latest()->get();
        foreach($type as $item){
            if($item->custom_id == null){
                $package = service_package::find($item->package_id);
                $item->service_id = $package->service_id;
            }else{
                $custom = service_package::find($item->custom_id);
                $item->service_id = $custom->service_id;
            }
            $userClient = user::find($item->client_id);
            $userFreelancer = freelancer::find($item->client_id);
            $userFreelancer = user::find($userFreelancer->user_id);

            $item->buyer = $userClient->name;
            $item->seller = $userFreelancer->name;

            $payment = payment::where('order_id',$item->order_id)->first();
            $item->payment = $payment->payment_type;
        }
        Log::info($type);
        return view('layouts.order', compact('type'));
    }
}
