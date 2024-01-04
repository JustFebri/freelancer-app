<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\freelancer;
use App\Models\picture;
use App\Models\service;
use App\Models\service_img;
use App\Models\service_package;
use App\Models\sub_category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\API\SavedServiceController;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function packageActivation(Request $request)
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();

        Log::info($request);
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

    public function getServiceFreelancer()
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();

        $data = DB::table('service as s')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->where('s.freelancer_id', '=', $record->freelancer_id)
            ->get();

        return response()->json([
            'data' => $data,
            'message' => 'Service fetched',
        ], 200);
    }

    public function getAImage($service_id)
    {
        $imageData = service_img::join('picture as p', 'service_img.picture_id', '=', 'p.picture_id')
            ->where('service_img.service_id', '=', $service_id)
            ->first();

        return $imageData;
    }

    public function getPopularService()
    {
        $sub_category = DB::table('sub_category')->get();

        return response()->json([
            'data' => $sub_category,
        ], 200);
    }

    public function getAllCategory()
    {
        $data = DB::table('category')->get();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getAllSubCategory($category_id)
    {
        $data = DB::table('sub_category')->where('category_id', '=', $category_id)->get();
        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getLowestPrice($service_id)
    {
        $service_package = DB::table('service_package as sp')
            ->where('sp.service_id', '=', $service_id)
            ->min('sp.price');

        return $service_package;
    }

    public function getRating($service_id)
    {
        $count = DB::table('review as r')
            ->leftJoin('order as o', 'o.order_id', '=', 'r.order_id')
            ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
            ->where('sp.service_id', '=', $service_id)
            ->count();

        $rating = DB::table('review as r')
            ->leftJoin('order as o', 'o.order_id', '=', 'r.order_id')
            ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
            ->where('sp.service_id', '=', $service_id)
            ->avg('r.rating');

        return [
            'count' => $count,
            'rating' => $rating,
        ];
    }

    public function getServiceImage($service_id)
    {
        Log::info('calling getserviceimage');
        $data = DB::table('service_img as si')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'si.picture_id')
            ->where('si.service_id', '=', $service_id)
            ->select('p.picasset')
            ->get();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getServicePackage($service_id)
    {
        $data = DB::table('service_package as sp')
            ->where('sp.service_id', '=', $service_id)
            ->get();

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getRecommendation()
    {
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description', 'u.email', 'sc.subcategory_name')
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = $var['rating'];
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }

    public function getDisplayBySubCategoryIdAuth($subcategory_id)
    {
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description','u.email', 'sc.subcategory_name')
            ->where('s.subcategory_id', '=', $subcategory_id)
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = $var['rating'];
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }

    public function getDisplayBySubCategoryIdNoAuth($subcategory_id)
    {
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id' ,'=','s.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description','u.email', 'sc.subcategory_name')
            ->where('s.subcategory_id', '=', $subcategory_id)
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = $var['rating'];
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = false;
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }
}
