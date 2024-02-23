<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\freelancer;
use App\Models\picture;
use App\Models\review;
use App\Models\service;
use App\Models\service_img;
use App\Models\service_package;
use App\Models\sub_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\API\SavedServiceController;
use App\Models\custom_orders;
use App\Models\user;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    // public function getAllOrders(Request $request)
    // {
    //     $authenticatedUserId = auth()->id();
    //     $orders = DB::table('order as o')
    //         ->leftJoin('payments as ps', 'ps.order_id', '=', 'o.order_id')
    //         ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
    //         ->leftJoin('service as s', 's.service_id', '=', 'sp.service_id')
    //         ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')

    //         ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
    //         ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')

    //         ->where('o.client_id', '=', $authenticatedUserId)
    //         ->where('o.order_status', '!=', 'token')
    //         ->select('s.service_id', 'sp.title as package_title', 's.title as service_title', 'sp.price', 'o.created_at', 'o.updated_at', 'o.order_status', 'p.picasset', 'u.name')
    //         ->get();

    //     foreach ($orders as $item) {
    //         $item->servicePic = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
    //     }

    //     Log::info($orders);

    //     return response()->json([
    //         'data' => $orders,
    //     ], 200);
    // }

    public function getAllOrders(string $status)
    {
        $authenticatedUserId = auth()->id();

        if ($status == 'all') {
            $orders = DB::table('order as o')
                ->where('o.client_id', '=', $authenticatedUserId)
                ->where('o.order_status', '!=', 'token')
                ->orderByDesc('o.updated_at')
                ->get();
        }else{
            $orders = DB::table('order as o')
            ->where('o.client_id', '=', $authenticatedUserId)
            ->where('o.order_status', $status)
            ->orderByDesc('o.updated_at')
            ->get();
        }


        foreach ($orders as $item) {
            if ($item->package_id != null) {
                $package = service_package::find($item->package_id);
                $service = service::find($package->service_id);
                $freelancer = freelancer::find($service->freelancer_id);
                $user = user::find($freelancer->user_id);
                $picture = picture::find($user->picture_id);

                $item->service_id = $service->service_id;
                $item->service_name = $service->title;
                $item->type_name = $package->title;
                $item->price = $package->price;
                $item->name = $user->name;
                $item->picasset = $picture->picasset;
                $item->servicePic = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
            } else {
                $custom = custom_orders::find($item->custom_id);
                $service = service::find($custom->service_id);
                $freelancer = freelancer::find($custom->freelancer_id);
                $user = user::find($freelancer->user_id);
                $picture = picture::find($user->picture_id);

                $item->service_id = $service->service_id;
                $item->service_name = $service->title;
                $item->type_name = 'Custom Order';
                $item->price = $custom->price;
                $item->name = $user->name;
                $item->picasset = $picture->picasset;
                $item->servicePic = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
            }
        }

        return response()->json([
            'data' => $orders,
        ], 200);
    }

    public function fetchDataSeller(string $freelancer_id)
    {
        $data = DB::table('freelancer as f')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->where('f.freelancer_id', $freelancer_id)
            ->select('p.piclink', 'u.name', 'f.description')
            ->first();

        $data->avg = review::where('freelancer_id', $freelancer_id)->avg('rating');
        $data->count = review::where('freelancer_id', $freelancer_id)->count();

        $freelancerLanguage = DB::table('freelancer_language as fl')
            ->leftJoin('language as l', 'l.language_id', '=', 'fl.language_id')
            ->where('fl.freelancer_id', '=', $freelancer_id)
            ->get();

        $freelancerSkills = DB::table('freelancer_skill as fs')
            ->leftJoin('skill as s', 's.skill_id', '=', 'fs.skill_id')
            ->where('fs.freelancer_id', '=', $freelancer_id)
            ->get();

        $services = DB::table('service as s')
            ->where('s.freelancer_id', '=', $freelancer_id)
            ->get();

        foreach ($services as $item) {
            $item->avg = DB::table('review as r')
                ->leftJoin('order as o', 'o.order_id', '=', 'r.order_id')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('r.freelancer_id', '=', $freelancer_id)
                ->where('sp.service_id', '=', $item->service_id)
                ->avg('r.rating');

            $item->count = DB::table('review as r')
                ->leftJoin('order as o', 'o.order_id', '=', 'r.order_id')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('r.freelancer_id', '=', $freelancer_id)
                ->where('sp.service_id', '=', $item->service_id)
                ->count();

            $item->lowestPrice = app('App\Http\Controllers\API\ServiceController')->getLowestPrice($item->service_id);
            $item->serviceLink = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
        }

        $portfolio = DB::table('portfolio as p')
            ->where('p.freelancer_id', '=', $freelancer_id)
            ->get();

        foreach ($portfolio as $item) {
            $item->portfolioPic = DB::table('portfolio_img as pi')
                ->leftJoin('picture as p', 'p.picture_id', '=', 'pi.picture_id')
                ->where('pi.portfolio_id', '=', $item->portfolio_id)
                ->get();
        }

        return response()->json([
            'data' => $data,
            'languages' => $freelancerLanguage,
            'skills' => $freelancerSkills,
            'services' => $services,
            'portfolio' => $portfolio,
        ], 200);
    }
}
