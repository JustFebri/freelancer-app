<?php

namespace App\Http\Controllers\API;

use App\Events\UpdateCustom;
use App\Events\UpdateMessage;
use App\Events\UpdateOrder;
use App\Events\UpdateOrderFreelancer;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreMessageRequest;
use App\Mail\FreelancerDeliveredWork;
use App\Mail\FreelancerDeliveredWorkWithoutAttachment;
use App\Models\category;
use App\Models\custom_orders;
use App\Models\freelancer;
use App\Models\freelancer_language;
use App\Models\freelancer_skill;
use App\Models\language;
use App\Models\LanguageClass;
use App\Models\occupation;
use App\Models\order;
use App\Models\payment;
use App\Models\picture;
use App\Models\portfolio;
use App\Models\portfolio_img;
use App\Models\review;
use App\Models\service;
use App\Models\service_img;
use App\Models\service_package;
use App\Models\skill;
use App\Models\sub_category;
use App\Models\sub_occupation;
use App\Models\transactions;
use App\Models\user;
use App\Models\delivery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FreelancerController extends Controller
{
    public function freelancerActivation(Request $request)
    {
        $currentUserId = auth()->id();
        $user = user::find($currentUserId);


        $file2 = $request->file('idCardImage')->store('public/images');
        $file3 = $request->file('idCardWithSefieImage')->store('public/images');

        if ($request->file('profileImage') != null) {
            $file1 = $request->file('profileImage')->store('public/images');
            $filename = $request->file('profileImage')->hashName();
            $path = 'public/images/' . $filename;
            $url = asset(Storage::url($path));

            $picData1 = [
                'piclink' => $url,
                'picasset' => Storage::url($path),
            ];

            $pic1 = picture::create($picData1);

            $user->name = $request->name;
            $user->picture_id = $pic1->picture_id;
            $user->save();
        }

        $filename = $request->file('idCardImage')->hashName();
        $path = 'public/images/' . $filename;
        $url = asset(Storage::url($path));
        $picData2 = [
            'piclink' => $url,
            'picasset' => Storage::url($path),
        ];

        $filename = $request->file('idCardWithSefieImage')->hashName();
        $path = 'public/images/' . $filename;
        $url = asset(Storage::url($path));
        $picData3 = [
            'piclink' => $url,
            'picasset' => Storage::url($path),
        ];

        $pic2 = picture::create($picData2);
        $pic3 = picture::create($picData3);

        $freelancer = freelancer::create([
            'user_id' => $currentUserId,
            'identity_number' => $request->niknumber,
            'identity_name' => $request->nikname,
            'identity_gender' => $request->nikgender,
            'identity_address' => $request->nikaddress,
            'description' => $request->description,
            'rating' => 0,
            'revenue' => 0,
            'link' => $request->url,
            'id_card' => $pic2->picture_id,
            'id_card_with_selfie' => $pic3->picture_id,
            'isApproved' => "pending",
        ]);

        $dataStringLanguages = $request->input('languages');
        $dataArray = json_decode($dataStringLanguages);
        foreach ($dataArray as $data) {
            $result = language::where('language_name', $data->language)->value('language_id');
            freelancer_language::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'language_id' => $result,
                'proficiency_level' => $data->level,
            ]);
        }

        $dataStringOccupations = $request->input('occupations');
        $occupationData = json_decode($dataStringOccupations);
        $result = category::where('category_name', $occupationData->occupation)->value('category_id');
        occupation::create([
            'freelancer_id' => $freelancer->freelancer_id,
            'category_id' => $result,
            'from' => $occupationData->from,
            'to' => $occupationData->to,
        ]);


        $skillsArray = explode(',', $request->skills);
        foreach ($skillsArray as $data) {
            $record = skill::firstOrCreate(['skill_name' => $data]);
            freelancer_skill::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'skill_id' => $record->skill_id,
            ]);
        }

        $skillsArray = explode(',', $request->subcategoryOccupation);
        foreach ($skillsArray as $data) {
            $result = sub_category::where('subcategory_name', $data)->value('subcategory_id');
            sub_occupation::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'subcategory_id' => $result,
            ]);
        }

        return response([
            'message' => 'Verification request has been successfully created',
        ], 201);
    }

    public function packageActivation(Request $request)
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();

        $result = sub_category::where('subcategory_name', $request->subCategory)->value('subcategory_id');

        $service = service::create([
            'freelancer_id' => $record->freelancer_id,
            'subcategory_id' => $result,
            'title' => $request->title,
            'description' => $request->desc,
            'location' => $request->location ?? '',
            'type' => $request->type,
            'custom_order' => $request->customOrder,
            'IsApproved' => 'pending'
        ]);

        $dataStringPackage = $request->input('packages');
        $dataArray = json_decode($dataStringPackage);
        foreach ($dataArray as $data) {
            $result = service_package::create([
                'service_id' => $service->service_id,
                'title' => $data->title,
                'description' => $data->desc,
                'price' => $data->price,
                'revision' => $data->revision,
                'delivery_days' => $data->deliveryDays,
            ]);
        }

        $images = $request->file('images');
        if ($images) {
            foreach ($images as $image) {
                $image->store('public/images');
                $filename = $image->hashName();
                $path = 'public/images/' . $filename;
                $url = asset(Storage::url($path));

                $newPicture = new picture;
                $newPicture->piclink = $url;
                $newPicture->picasset = Storage::url($path);
                $newPicture->save();

                $service_img = new service_img;
                $service_img->service_id = $service->service_id;
                $service_img->picture_id = $newPicture->picture_id;
                $service_img->save();
            }
        }

        return response([
            'message' => 'Service request has been successfully created',
        ], 201);
    }

    public function updateService(Request $request)
    {
        $result = sub_category::where('subcategory_name', $request->subCategory)->value('subcategory_id');

        $service = service::find($request->serviceId);
        $service->subcategory_id = $result;
        $service->title = $request->title;
        $service->description = $request->desc;
        $service->type = $request->type;
        $service->custom_order = $request->customOrder;
        $service->IsApproved = 'pending';
        $service->save();

        service_package::where('service_id', $request->serviceId)->delete();

        $dataStringPackage = $request->input('packages');
        $dataArray = json_decode($dataStringPackage);
        foreach ($dataArray as $data) {
            $result = service_package::create([
                'service_id' => $service->service_id,
                'title' => $data->title,
                'description' => $data->desc,
                'price' => $data->price,
                'revision' => $data->revision,
                'delivery_days' => $data->deliveryDays,
            ]);
        }



        if ($request->updateImage == 'true') {
            $listImage = service_img::where('service_id', $request->serviceId)->get();

            foreach ($listImage as $item) {
                $picture = picture::findOrFail($item->picture_id);
                $filename = basename($picture->picasset);
                Storage::delete('public/images/' . $filename);

                $picture->delete();
                $item->delete();
            }

            $images = $request->file('images');
            if ($images) {
                foreach ($images as $image) {
                    $image->store('public/images');
                    $filename = $image->hashName();
                    $path = 'public/images/' . $filename;
                    $url = asset(Storage::url($path));

                    $newPicture = new picture;
                    $newPicture->piclink = $url;
                    $newPicture->picasset = Storage::url($path);
                    $newPicture->save();

                    $service_img = new service_img;
                    $service_img->service_id = $service->service_id;
                    $service_img->picture_id = $newPicture->picture_id;
                    $service_img->save();
                }
            }
        }



        return response([
            'message' => 'Service Updated',
        ], 201);
    }

    function deleteSellerService(string $serviceId)
    {
        $currentUserId = auth()->id();

        $service_img = service_img::where('service_id', $serviceId)->get();

        foreach ($service_img as $item) {
            $picture = picture::findOrFail($item->picture_id);
            $filename = basename($picture->picasset);
            Storage::delete('public/images/' . $filename);

            $picture->delete();
        }

        $freelancer = freelancer::where('user_id', $currentUserId)->first();

        $deletedRows = service::where('freelancer_id', $freelancer->freelancer_id)
            ->where('service_id', $serviceId)
            ->delete();

        if ($deletedRows > 0) {
            return response([
                'message' => 'Service has been successfully deleted.',
            ], 200);
        } else {
            return response([
                'message' => 'Service not found or not saved for the user.',
            ], 404);
        }
    }

    function getServiceData(string $serviceId)
    {
        $data = DB::table('service as s')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->where('s.service_id', '=', $serviceId)
            ->select('s.type', 'c.category_name', 'sc.subcategory_name', 's.custom_order', 's.location', 's.title', 's.description', 's.service_id')
            ->first();

        $data->packages = DB::table('service_package as sp')->where('sp.service_id', '=', $data->service_id)->get();

        $data->images = DB::table('service_img as si')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'si.picture_id')
            ->where('si.service_id', '=', $serviceId)->get();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    function getDropdownItem()
    {
        $currentUserId = auth()->id();

        $data = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->where('f.user_id', $currentUserId)
            ->select('s.service_id', 's.title')
            ->get();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    function createCustomOrder(Request $request)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', $currentUserId)->first();

        $data = custom_orders::create([
            'service_id' => $request->service_id,
            'freelancer_id' => $freelancer->freelancer_id,
            'description' => $request->description,
            'price' => $request->price,
            'revision' => $request->revision,
            'delivery_days' => $request->delivery_days,
            'status' => 'pending',
            'expiration_date' => $request->expiration_date,
        ]);

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function setCustomOrderStatus(Request $request)
    {
        $customOrder = custom_orders::where('custom_id', $request->custom_id)
            ->first();

        if (!$customOrder) {
            return response()->json([
                'message' => 'Custom Order not found',
            ], 404);
        }

        $customOrder->status = $request->status;
        $customOrder->save();

        Log::info($request->chatRoom_id);
        broadcast(new UpdateCustom($request->chatRoom_id))->toOthers();

        return response()->json([
            'data' => $customOrder,
            'message' => 'Custom Order status updated successfully',
        ], 200);
    }

    public function addPortfolio(Request $request)
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();

        $portfolio = new portfolio;
        $portfolio->freelancer_id = $record->freelancer_id;
        $portfolio->title = $request->title;
        $portfolio->description = $request->desc;
        $portfolio->save();

        $images = $request->file('images');
        if ($images) {
            foreach ($images as $image) {
                $image->store('public/images');
                $filename = $image->hashName();
                $path = 'public/images/' . $filename;
                $url = asset(Storage::url($path));

                $newPicture = new picture;
                $newPicture->piclink = $url;
                $newPicture->picasset = Storage::url($path);
                $newPicture->save();

                $portfolio_img = new portfolio_img;
                $portfolio_img->portfolio_id = $portfolio->portfolio_id;
                $portfolio_img->picture_id = $newPicture->picture_id;
                $portfolio_img->save();
            }
        }

        return response([
            'message' => 'Service request has been successfully created',
        ], 200);
    }
    public function getHeader(Request $request)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', '=', $currentUserId)->first();

        $user = DB::table('user as u')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->where('u.user_id', '=', $currentUserId)
            ->first();

        $averageReview = review::where('freelancer_id', $freelancer->freelancer_id)->avg('rating');
        $reviewCount = review::where('freelancer_id', $freelancer->freelancer_id)->count();

        return response([
            'user' => $user,
            'avg' => round((float)$averageReview, 1),
            'count' => $reviewCount,
        ], 200);
    }
    public function getAbout(Request $request)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', '=', $currentUserId)->first();

        $freelancerLanguage = DB::table('freelancer_language as fl')
            ->leftJoin('language as l', 'l.language_id', '=', 'fl.language_id')
            ->where('fl.freelancer_id', '=', $freelancer->freelancer_id)
            ->get();

        $freelancerSkills = DB::table('freelancer_skill as fs')
            ->leftJoin('skill as s', 's.skill_id', '=', 'fs.skill_id')
            ->where('fs.freelancer_id', '=', $freelancer->freelancer_id)
            ->get();

        return response()->json([
            'freelancer' => $freelancer,
            'languages' => $freelancerLanguage,
            'skills' => $freelancerSkills,
            'language' => $freelancerLanguage,
        ], 200);
    }

    public function getServices(Request $request)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', '=', $currentUserId)->first();

        $services = DB::table('service as s')
            ->where('s.freelancer_id', '=', $freelancer->freelancer_id)
            ->get();

        foreach ($services as $item) {
            $item->avg = DB::table('review as r')
                ->where('r.freelancer_id', '=', $freelancer->freelancer_id)
                ->where('r.service_id', '=', $item->service_id)
                ->avg('r.rating');

            $item->avg = round((float)$item->avg, 1);

            $item->count = DB::table('review as r')
                ->where('r.freelancer_id', '=', $freelancer->freelancer_id)
                ->where('r.service_id', '=', $item->service_id)
                ->count();

            $item->lowestPrice = app('App\Http\Controllers\API\ServiceController')->getLowestPrice($item->service_id);
            $item->serviceLink = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
        }

        return response()->json([
            'services' => $services,
        ], 200);
    }

    public function getReviews(Request $request)
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();
    }

    public function getPortfolio(Request $request)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', '=', $currentUserId)->first();

        $portfolio = DB::table('portfolio as p')
            ->where('p.freelancer_id', '=', $freelancer->freelancer_id)
            ->get();

        foreach ($portfolio as $item) {
            $item->portfolioPic = DB::table('portfolio_img as pi')
                ->leftJoin('picture as p', 'p.picture_id', '=', 'pi.picture_id')
                ->where('pi.portfolio_id', '=', $item->portfolio_id)
                ->get();
        }

        return response()->json([
            'services' => $portfolio,
        ], 200);
    }
    public function getPortfolioById(string $portfolio_id)
    {
        $portfolio = portfolio::find($portfolio_id);
        $portfolio_img = DB::table('portfolio_img as pi')
            ->where('pi.portfolio_id', '=', $portfolio_id)
            ->leftJoin('picture as p', 'p.picture_id', '=', 'pi.picture_id')
            ->select('p.piclink')
            ->get();
        $freelancer = DB::table('freelancer as f')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->where('f.freelancer_id', '=', $portfolio->freelancer_id)
            ->select('p.piclink', 'u.name')
            ->first();
        return response()->json([
            'portfolio' => $portfolio,
            'portfolio_img' => $portfolio_img,
            'freelanceer' => $freelancer,
        ], 200);
    }

    public function updateSellerProfile(Request $request)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', '=', $currentUserId)->first();

        $dataLanguage = freelancer_language::where('freelancer_id', $freelancer->freelancer_id)->delete();
        $dataSkill = freelancer_skill::where('freelancer_id', $freelancer->freelancer_id)->delete();

        $freelancer = freelancer::find($freelancer->freelancer_id);
        $freelancer->description = $request->description;
        $freelancer->save();

        $dataStringLanguages = $request->languages;
        $dataArray = json_decode($dataStringLanguages);
        foreach ($dataArray as $data) {
            $result = language::where('language_name', $data->language)->value('language_id');
            freelancer_language::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'language_id' => $result,
                'proficiency_level' => $data->level,
            ]);
        }

        $skillsArray = explode(',', $request->skills);
        foreach ($skillsArray as $data) {
            $record = skill::firstOrCreate(['skill_name' => $data]);
            freelancer_skill::create([
                'freelancer_id' => $freelancer->freelancer_id,
                'skill_id' => $record->skill_id,
            ]);
        }

        return response([
            'message' => 'Seller Profile Updated',
        ], 200);
    }

    public function deletePortfolio(string $portfolio_id)
    {

        $listImage = portfolio_img::where('portfolio_id', $portfolio_id)->get();

        foreach ($listImage as $item) {
            $picture = picture::findOrFail($item->picture_id);
            $filename = basename($picture->picasset);
            Storage::delete('public/images/' . $filename);

            $picture->delete();
            $item->delete();
        }

        portfolio::destroy($portfolio_id);

        return response([
            'message' => 'Portfolio Deleted',
        ], 200);
    }

    public function updatePortfolio(Request $request)
    {
        $portfolio = portfolio::find($request->portfolioId);
        $portfolio->title = $request->title;
        $portfolio->description = $request->desc;
        $portfolio->save();

        if ($request->updateImage == 'true') {
            $listImage = portfolio_img::where('portfolio_id', $portfolio->portfolio_id)->get();

            foreach ($listImage as $item) {
                $picture = picture::findOrFail($item->picture_id);
                $filename = basename($picture->picasset);
                Storage::delete('public/images/' . $filename);

                $picture->delete();
                $item->delete();
            }

            $images = $request->file('images');
            if ($images) {
                foreach ($images as $image) {
                    $image->store('public/images');
                    $filename = $image->hashName();
                    $path = 'public/images/' . $filename;
                    $url = asset(Storage::url($path));

                    $newPicture = new picture;
                    $newPicture->piclink = $url;
                    $newPicture->picasset = Storage::url($path);
                    $newPicture->save();

                    $portfolio_img = new portfolio_img;
                    $portfolio_img->portfolio_id = $portfolio->portfolio_id;
                    $portfolio_img->picture_id = $newPicture->picture_id;
                    $portfolio_img->save();
                }
            }
        }

        return response([
            'message' => 'Portfolio Updated',
        ], 200);
    }

    public function getAllOrder(string $status)
    {
        $currentUserId = auth()->id();
        $freelancer = freelancer::where('user_id', '=', $currentUserId)->first();

        if ($status == 'all') {
            $package = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->leftJoin('service as s', 's.service_id', '=', 'sp.service_id')
                ->leftJoin('client as c', 'c.client_id', '=', 'o.client_id')
                ->leftJoin('user as u', 'u.user_id', '=', 'c.user_id')
                ->where('s.freelancer_id', $freelancer->freelancer_id)
                ->where('o.order_status', '!=', 'token')
                ->where('o.order_status', '!=', 'awaiting payment')
                ->whereNotNull('o.package_id')
                ->orderByDesc('o.updated_at')
                ->get();

            $custom_orders = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->leftJoin('service as s', 's.service_id', '=', 'co.service_id')
                ->leftJoin('client as c', 'c.client_id', '=', 'o.client_id')
                ->leftJoin('user as u', 'u.user_id', '=', 'c.user_id')
                ->where('co.freelancer_id', $freelancer->freelancer_id)
                ->where('o.order_status', '!=', 'token')
                ->where('o.order_status', '!=', 'awaiting payment')
                ->whereNotNull('o.custom_id')
                ->orderByDesc('o.updated_at')
                ->get();
        } else {
            $package = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->leftJoin('service as s', 's.service_id', '=', 'sp.service_id')
                ->leftJoin('client as c', 'c.client_id', '=', 'o.client_id')
                ->leftJoin('user as u', 'u.user_id', '=', 'c.user_id')
                ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
                ->where('s.freelancer_id', $freelancer->freelancer_id)
                ->where('o.order_status', $status)
                ->where('o.order_status', '!=', 'token')
                ->where('o.order_status', '!=', 'awaiting payment')
                ->whereNotNull('o.package_id')
                ->orderByDesc('o.updated_at')
                ->get();

            $custom_orders = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->leftJoin('service as s', 's.service_id', '=', 'co.service_id')
                ->leftJoin('client as c', 'c.client_id', '=', 'o.client_id')
                ->leftJoin('user as u', 'u.user_id', '=', 'c.user_id')
                ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
                ->where('co.freelancer_id', $freelancer->freelancer_id)
                ->where('o.order_status', $status)
                ->where('o.order_status', '!=', 'token')
                ->where('o.order_status', '!=', 'awaiting payment')
                ->whereNotNull('o.custom_id')
                ->orderByDesc('o.updated_at')
                ->get();
        }

        foreach ($package as $item) {
            $data1 = service_package::find($item->package_id);
            $data2 = service::find($data1->service_id);

            $item->service_name = $data2->title;
            $item->type_name = $data1->title;
        }

        foreach ($custom_orders as $item) {
            $data1 = custom_orders::find($item->custom_id);
            $data2 = service::find($data1->service_id);

            $item->service_name = $data2->title;
            $item->type_name = 'Custom Order';
        }

        return response([
            'package' => $package,
            'custom' => $custom_orders,
        ], 200);
    }

    function orderConfirmation(Request $request)
    {
        $order = order::find($request->order_id);

        if ($request->status == 'accept') {
            $order->order_status = 'in progress';
            if ($order->package_id != null) {
                $package = service_package::find($order->package_id);
                $order->due_date = Carbon::now()->addDays($package->delivery_days);
            } else {
                $custom = custom_orders::find($order->custom_id);
                $order->due_date = Carbon::now()->addDays($custom->delivery_days);
            }
            $order->save();
        } else {
            $order->order_status = 'cancelled';
            $order->save();

            $payment = payment::where('order_id', '=', $request->order_id)->first();
            $payment->payment_status = 'refunded';
            $payment->save();

            $user = user::find($order->client_id);
            $user->balance +=  $order->amount;
            $user->save();

            $transaction = new transactions();
            $transaction->order_id = $request->order_id;
            $transaction->user_id = $order->client_id;
            $transaction->amount = - ($order->amount);
            $transaction->type = 'client_refund';
            $transaction->description = 'refund for order ' . $request->order_id;
            $transaction->save();
        }

        broadcast(new UpdateOrder($order->client_id))->toOthers();

        $freelancer = freelancer::find($order->freelancer_id);
        broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();

        return response([
            'message' => 'Order Updated',
        ], 200);
    }

    public function deliverNow(Request $request)
    {
        $order = order::find($request->order_id);
        $order->order_status = 'delivered';
        $order->due_date = Carbon::now()->addDays(3);
        $order->save();

        $delivery = new delivery;
        $delivery->order_id = $request->order_id;

        if ($request->file('fileUrl') != null) {
            $file = $request->file('fileUrl')->store('public/files');
            $filename = $request->file('fileUrl')->hashName();
            $path = 'public/files/' . $filename;
            $url = asset(Storage::url($path));
            $delivery->fileUrl = $url;
        }

        $delivery->description = $request->desc;
        $delivery->save();

        broadcast(new UpdateOrder($order->client_id))->toOthers();
        $freelancer = freelancer::find($order->freelancer_id);
        broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();

        $freelancer = user::find($freelancer->user_id);
        $client = user::find($order->client_id);
        
        if ($request->file('fileUrl') != null) {
            Mail::to($client->email)->send(new FreelancerDeliveredWork(
                $freelancer->name,
                $request->desc,
                $client->name,
                storage_path('app/' . $path),
            ));
        }else{
            Mail::to($client->email)->send(new FreelancerDeliveredWorkWithoutAttachment(
                $freelancer->name,
                $request->desc,
                $client->name,
            ));
        }

        return response([
            'message' => 'Order Delivered',
        ], 200);
    }
}
