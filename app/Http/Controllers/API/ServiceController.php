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
    public function getServiceFreelancer()
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();

        $data = DB::table('service as s')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->where('s.freelancer_id', '=', $record->freelancer_id)
            ->get();

        foreach ($data as $item) {
            $item->servicePic = $this->getAImage($item->service_id);
        }

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
            ->where('service_id', '=', $service_id)
            ->count();

        $rating = DB::table('review as r')
            ->where('service_id', '=', $service_id)
            ->avg('r.rating');


        return [
            'count' => $count,
            'rating' => round((float)$rating, 1),
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
        $authenticatedUserId = auth()->id();

        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.custom_order')
            ->where('u.user_id', '!=', $authenticatedUserId)
            ->where('s.isApproved', '!=', 'pending')
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);

            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }

    public function getDisplayBySubCategoryIdAuth($subcategory_id)
    {
        $authenticatedUserId = auth()->id();
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description', 'u.email', 'sc.subcategory_name')
            ->where('s.subcategory_id', '=', $subcategory_id)
            ->where('u.user_id', '!=', $authenticatedUserId)
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }

    public function getDisplayBySubCategoryIdNoAuth($subcategory_id)
    {
        $authenticatedUserId = auth()->id();
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description', 'u.email', 'sc.subcategory_name')
            ->where('s.subcategory_id', '=', $subcategory_id)
            ->where('u.user_id', '!=', $authenticatedUserId)
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = false;
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }
}
