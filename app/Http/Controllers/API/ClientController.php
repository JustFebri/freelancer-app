<?php

namespace App\Http\Controllers\API;

use App\Events\UpdateOrder;
use App\Events\UpdateOrderFreelancer;
use App\Http\Controllers\Controller;
use App\Mail\ClientAcceptDelivery;
use App\Mail\ClientRequestRevision;
use App\Models\delivery;
use App\Models\freelancer;
use App\Models\picture;
use App\Models\review;
use App\Models\revision;
use App\Models\service;
use App\Models\service_img;
use App\Models\service_package;
use App\Models\sub_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\API\SavedServiceController;
use App\Mail\FreelancerAcceptRevisionRequest;
use App\Mail\FreelancerDeclineRevisionRequest;
use App\Models\client;
use App\Models\custom_orders;
use App\Models\order;
use App\Models\payment;
use App\Models\report;
use App\Models\transactions;
use App\Models\user;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function getAllOrders(string $status)
    {
        $authenticatedUserId = auth()->id();

        if ($status == 'all') {
            $orders = DB::table('order as o')
                ->where('o.client_id', '=', $authenticatedUserId)
                ->where('o.order_status', '!=', 'token')
                ->orderByDesc('o.updated_at')
                ->get();
        } else {
            $orders = DB::table('order as o')
                ->where('o.client_id', '=', $authenticatedUserId)
                ->where('o.order_status', $status)
                ->orderByDesc('o.updated_at')
                ->get();
        }


        foreach ($orders as $item) {
            $dataTemp = payment::where('order_id', $item->order_id)->first();
            $item->reportCount = report::where('order_id', $item->order_id)
                ->where('user_id', $authenticatedUserId)
                ->count();

            $item->token = $dataTemp->token;

            $count = delivery::where('order_id', $item->order_id)->where('fileUrl', '!=', null)->count();
            if ($count == 0) {
                $item->fileCount = 0;
            } else {
                $item->fileCount = 1;
            }

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
                $item->freelancerId = $user->user_id;
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
                $item->freelancerId = $user->user_id;
                $item->picasset = $picture->picasset;
                $item->servicePic = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
            }
            $existingReview = Review::where('client_id', $authenticatedUserId)
                ->where('service_id', $item->service_id)
                ->first();

            if ($existingReview) {
                $item->reviewStatus = 'available';
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
        $data->avg = round((float)$data->avg, 1);
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

        $reviews = DB::table('review as r')
            ->leftJoin('user as u', 'u.user_id', '=', 'r.client_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->orderBy('updated_at', 'desc')
            ->select('p.piclink', 'u.name', 'r.rating', 'r.comment', 'r.updated_at')
            ->get();

        foreach ($services as $item) {

            $item->avg = DB::table('review as r')
                ->where('r.freelancer_id', '=', $freelancer_id)
                ->where('r.service_id', '=', $item->service_id)
                ->avg('r.rating');

            $item->avg = round((float)$item->avg, 1);


            $item->count = DB::table('review as r')
                ->where('r.freelancer_id', '=', $freelancer_id)
                ->where('r.service_id', '=', $item->service_id)
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
        Log::info($reviews);
        return response()->json([
            'data' => $data,
            'languages' => $freelancerLanguage,
            'skills' => $freelancerSkills,
            'services' => $services,
            'portfolio' => $portfolio,
            'reviews' => $reviews,
        ], 200);
    }

    public function downloadFile(string $orderId)
    {
        $order = order::find($orderId);

        $item = delivery::where('order_id', $orderId)->first();


        $path = parse_url($item->fileUrl, PHP_URL_PATH);
        $path = str_replace('/storage', 'public', $path);

        if (Storage::exists($path)) {
            return response()->json([
                'message' => 'Founded',
                'fileUrl' => $item->fileUrl,
                'fileExtension' => $this->getFileExtension($path),
            ], 200);
        } else {
            return response()->json([
                'message' => 'Not Founded',
            ], 404);
        }
    }
    public function getFileExtension($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $fileExtension = $pathInfo['extension'] ?? '';

        return $fileExtension;
    }

    public function completeOrder(string $order_id)
    {
        $authenticatedUserId = auth()->id();

        $order = order::find($order_id);
        $order->order_status = 'completed';
        $order->save();

        $client = client::find($authenticatedUserId);
        $client->orders_made += 1;
        $client->total_spent += $order->amount;
        $client->save();

        $freelancer = freelancer::find($order->freelancer_id);
        $freelancer->revenue += $order->amount;
        $freelancer->save();

        $userFreelancer = user::find($freelancer->user_id);
        $userFreelancer->balance += $order->amount;
        $userFreelancer->save();

        $transaction = new transactions;
        $transaction->order_id = $order_id;
        $transaction->user_id = $freelancer->user_id;
        $transaction->amount = - ($order->amount);
        $transaction->type = 'freelancer_payout';
        $transaction->description = 'payout for finished order ' . $order_id;
        $transaction->save();

        broadcast(new UpdateOrder($order->client_id))->toOthers();
        broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();

        Mail::to($userFreelancer->email)->send(new ClientAcceptDelivery(
            $userFreelancer->name,
        ));

        return response()->json([
            'message' => 'Order Completed',
        ], 200);
    }

    public function sendReview(Request $request)
    {
        $authenticatedUserId = auth()->id();

        $order = order::find($request->order_id);

        if ($order) {
            $result = review::where('client_id', $authenticatedUserId)
                ->where('service_id', $request->service_id)
                ->first();

            if ($result) {
                $result->rating = $request->rating;
                $result->comment = $request->comment;
                $result->save();
            } else {
                $review = new review;
                $review->client_id = $authenticatedUserId;
                $review->freelancer_id = $request->freelancer_id;
                $review->rating = $request->rating;
                $review->comment = $request->comment;
                $review->service_id = $request->service_id;
                $review->save();
            }
        }
        if ($request->broadcast == 'yes') {
            broadcast(new UpdateOrder($order->client_id))->toOthers();
            $freelancer = freelancer::find($order->freelancer_id);
            broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();
        }


        return response()->json([
            'message' => 'Review Sent',
        ], 200);
    }

    public function getReview(string $service_id)
    {
        $reviews = DB::table('review as r')
            ->leftJoin('user as u', 'u.user_id', '=', 'r.client_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->where('service_id', $service_id)
            ->orderBy('updated_at', 'desc')
            ->select('p.piclink', 'u.name', 'r.rating', 'r.comment', 'r.updated_at')
            ->get();

        return response()->json([
            'message' => 'Success fetch review data',
            'data' => $reviews,
        ], 200);
    }

    public function requestRevision(Request $request)
    {
        $revision = new revision;
        $revision->order_id = $request->order_id;
        $revision->notes = $request->notes;
        $revision->status = 'pending';
        $revision->save();

        $order = order::find($request->order_id);
        $order->order_status = 'revision requested';
        $order->save();

        broadcast(new UpdateOrder($order->client_id))->toOthers();
        $freelancer = freelancer::find($order->freelancer_id);
        broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();

        $userFreelancer = user::find($freelancer->user_id);
        $userClient = user::find($order->client_id);

        Mail::to($userFreelancer->email)->send(
            new ClientRequestRevision(
                $userFreelancer->name,
                $userClient->name,
                $request->notes,
                $request->order_id,
            ),
        );

        return response()->json([
            'message' => 'Revision Request Sent',
            'data' => $revision,
        ], 200);
    }
}
