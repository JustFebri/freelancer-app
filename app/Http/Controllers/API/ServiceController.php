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
use App\Http\Requests\API\FilterDataRequest;
use App\Http\Requests\API\FilterSubCategoryRequest;
use App\Models\custom_orders;
use App\Models\order;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function getServiceFreelancer()
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();

        $data = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->where('s.freelancer_id', '=', $record->freelancer_id)
            ->where(function ($query) {
                $query->where('s.status', '!=', 'archived')
                    ->orWhereNull('s.status'); // Include records where IsApproved is NULL
            })
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.custom_order', 's.type', 's.location', 's.IsApproved')
            ->get();

        foreach ($data as $item) {
            $item->servicePic = $this->getAImage($item->service_id);

            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
            if ($item->user_id == $currentUserId) {
                $item->isSeller = true;
            } else {
                $item->isSeller = false;
            }
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
            ->orderBy('price')
            ->where('sp.package_status', 'active')
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
            ->select('u.user_id', 'u.name', 's.service_id', 'p.picasset', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.custom_order', 's.type', 's.location')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->get();

        foreach ($service as $item) {
            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);

            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);
            if ($item->user_id == $authenticatedUserId) {
                $item->isSeller = true;
            } else {
                $item->isSeller = false;
            }
        }

        return response()->json([
            'data' => $service,
        ], 200);
    }

    public function getDisplayBySubCategoryIdAuth($subcategory_id)
    {
        Log::info('called');
        $authenticatedUserId = auth()->id();
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.type', 's.location', 's.custom_order', 'f.freelancer_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where('s.subcategory_id', '=', $subcategory_id)
            ->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($service as $item) {
            $item->ratingBarang = review::where('service_id', '=', $item->service_id)
                ->avg('rating');
            $item->ratingFreelancer = review::where('client_id', '=', $item->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $item->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $item->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($item->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $item->totalBarang = $countPackage + $countCustom;
            $item->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $item->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($item->ratingBarang * $weights['ratingBarang']);
            Log::info($item->totalBarang * $weights['totalBarang']);
            Log::info($item->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($item->totalOrder * $weights['totalOrder']);

            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($item->service_id);

            if ($item->user_id == $authenticatedUserId) {
                $item->isSeller = true;
            } else {
                $item->isSeller = false;
            }

            $rankedSuggestions[] = [
                'title' => $item->title,
                'description' => $item->description,
                'service_id' => $item->service_id,
                'score' => $score,

                'lowestPrice' => $item->lowestPrice,
                'count' => $item->count,
                'rating' => $item->rating,
                'servicePic' => $item->servicePic->piclink,
                'serviceFav' => $item->serviceFav,
                'name' => $item->name,
                'email' => $item->email,
                'user_id' => $item->user_id,
                'piclink' => $item->piclink,
                'complete' => $item->totalBarang,
                'subcategory_name' => $item->subcategory_name,
                'location' => $item->location,
                'custom_order' => $item->custom_order,
                'type' => $item->type,
                'isSeller' => $item->isSeller,
            ];
        }

        Log::info($rankedSuggestions);

        usort($rankedSuggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json([
            'data' => $rankedSuggestions,
        ], 200);
    }

    public function getDisplayBySubCategoryIdNoAuth($subcategory_id)
    {
        $service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.type', 's.location', 's.custom_order', 'f.freelancer_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where('s.subcategory_id', '=', $subcategory_id)
            ->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($service as $item) {
            $item->ratingBarang = review::where('service_id', '=', $item->service_id)
                ->avg('rating');
            $item->ratingFreelancer = review::where('client_id', '=', $item->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $item->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $item->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($item->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $item->totalBarang = $countPackage + $countCustom;
            $item->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $item->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($item->ratingBarang * $weights['ratingBarang']);
            Log::info($item->totalBarang * $weights['totalBarang']);
            Log::info($item->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($item->totalOrder * $weights['totalOrder']);

            $score = $item->ratingBarang * $weights['ratingBarang'] +
                $item->ratingFreelancer * $weights['ratingPenjual'] +
                $item->totalBarang * $weights['totalBarang'] +
                $item->totalOrder * $weights['totalOrder'];

            $item->lowestPrice = $this->getLowestPrice($item->service_id);
            $var = $this->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);
            $item->servicePic = $this->getAImage($item->service_id);
            $item->serviceFav = false;

            $rankedSuggestions[] = [
                'title' => $item->title,
                'description' => $item->description,
                'service_id' => $item->service_id,
                'score' => $score,

                'lowestPrice' => $item->lowestPrice,
                'count' => $item->count,
                'rating' => $item->rating,
                'servicePic' => $item->servicePic->piclink,
                'name' => $item->name,
                'email' => $item->email,
                'user_id' => $item->user_id,
                'piclink' => $item->piclink,
                'complete' => $item->totalBarang,
                'subcategory_name' => $item->subcategory_name,
                'location' => $item->location,
                'custom_order' => $item->custom_order,
                'type' => $item->type,
                'isSeller' => false,
            ];
        }

        usort($rankedSuggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json([
            'data' => $rankedSuggestions,
        ], 200);
    }

    public function getResult(string $keyword)
    {
        $currentUserId = auth()->id();
        $keywords = explode(' ', $keyword);
        Log::info($keywords);

        $suggestions = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where(function ($queryBuilder) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $queryBuilder->where('s.title', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 's.custom_order', 'sc.subcategory_name', 's.type', 's.location', 'f.freelancer_id')
            ->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($suggestions as $suggestion) {
            $suggestion->ratingBarang = review::where('service_id', '=', $suggestion->service_id)
                ->avg('rating');
            $suggestion->ratingFreelancer = review::where('client_id', '=', $suggestion->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($suggestion->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $suggestion->totalBarang = $countPackage + $countCustom;
            $suggestion->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $suggestion->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($suggestion->ratingBarang * $weights['ratingBarang']);
            Log::info($suggestion->totalBarang * $weights['totalBarang']);
            Log::info($suggestion->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($suggestion->totalOrder * $weights['totalOrder']);

            $score = $suggestion->ratingBarang * $weights['ratingBarang'] +
                $suggestion->ratingFreelancer * $weights['ratingPenjual'] +
                $suggestion->totalBarang * $weights['totalBarang'] +
                $suggestion->totalOrder * $weights['totalOrder'];

            $suggestion->lowestPrice = $this->getLowestPrice($suggestion->service_id);
            $var = $this->getRating($suggestion->service_id);
            $suggestion->count = $var['count'];
            $suggestion->rating = number_format($var['rating'], 1);
            $suggestion->servicePic = $this->getAImage($suggestion->service_id);
            $suggestion->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($suggestion->service_id);

            if ($suggestion->user_id == $currentUserId) {
                $suggestion->isSeller = true;
            } else {
                $suggestion->isSeller = false;
            }

            $rankedSuggestions[] = [
                'title' => $suggestion->title,
                'description' => $suggestion->description,
                'service_id' => $suggestion->service_id,
                'score' => $score,

                'lowestPrice' => $suggestion->lowestPrice,
                'count' => $suggestion->count,
                'rating' => $suggestion->rating,
                'servicePic' => $suggestion->servicePic->piclink,
                'serviceFav' => $suggestion->serviceFav,
                'name' => $suggestion->name,
                'email' => $suggestion->email,
                'user_id' => $suggestion->user_id,
                'piclink' => $suggestion->piclink,
                'complete' => $suggestion->totalBarang,
                'subcategory_name' => $suggestion->subcategory_name,
                'location' => $suggestion->location,
                'custom_order' => $suggestion->custom_order,
                'type' => $suggestion->type,
                'isSeller' => $suggestion->isSeller,
            ];
        }

        usort($rankedSuggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        Log::info($rankedSuggestions);

        return response()->json([
            'suggestions' => $rankedSuggestions,
        ], 200);
    }

    public function getResultNotLogged(string $keyword)
    {
        $keywords = explode(' ', $keyword);
        Log::info($keywords);

        $suggestions = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where(function ($queryBuilder) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $queryBuilder->where('s.title', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 's.custom_order', 'sc.subcategory_name', 's.type', 's.location', 'f.freelancer_id')
            ->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($suggestions as $suggestion) {
            $suggestion->ratingBarang = review::where('service_id', '=', $suggestion->service_id)
                ->avg('rating');
            $suggestion->ratingFreelancer = review::where('client_id', '=', $suggestion->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($suggestion->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $suggestion->totalBarang = $countPackage + $countCustom;
            $suggestion->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $suggestion->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($suggestion->ratingBarang * $weights['ratingBarang']);
            Log::info($suggestion->totalBarang * $weights['totalBarang']);
            Log::info($suggestion->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($suggestion->totalOrder * $weights['totalOrder']);

            $score = $suggestion->ratingBarang * $weights['ratingBarang'] +
                $suggestion->ratingFreelancer * $weights['ratingPenjual'] +
                $suggestion->totalBarang * $weights['totalBarang'] +
                $suggestion->totalOrder * $weights['totalOrder'];

            $suggestion->lowestPrice = $this->getLowestPrice($suggestion->service_id);
            $var = $this->getRating($suggestion->service_id);
            $suggestion->count = $var['count'];
            $suggestion->rating = number_format($var['rating'], 1);
            $suggestion->servicePic = $this->getAImage($suggestion->service_id);

            $rankedSuggestions[] = [
                'title' => $suggestion->title,
                'description' => $suggestion->description,
                'service_id' => $suggestion->service_id,
                'score' => $score,

                'lowestPrice' => $suggestion->lowestPrice,
                'count' => $suggestion->count,
                'rating' => $suggestion->rating,
                'servicePic' => $suggestion->servicePic->piclink,
                'name' => $suggestion->name,
                'email' => $suggestion->email,
                'user_id' => $suggestion->user_id,
                'piclink' => $suggestion->piclink,
                'complete' => $suggestion->totalBarang,
                'subcategory_name' => $suggestion->subcategory_name,
                'location' => $suggestion->location,
                'custom_order' => $suggestion->custom_order,
                'type' => $suggestion->type,
                'isSeller' => false,
            ];
        }

        Log::info($rankedSuggestions);

        usort($rankedSuggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return response()->json([
            'suggestions' => $rankedSuggestions,
        ], 200);
    }

    function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371000
    ) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function filterData(FilterDataRequest $request)
    {
        $currentUserId = auth()->id();
        $keywords = explode(' ', $request->keyword);

        $suggestions = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where(function ($queryBuilder) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $queryBuilder->where('s.title', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 's.custom_order', 'sc.subcategory_name', 's.type', 's.location', 's.lat', 's.lng', 'f.freelancer_id');

        if ($request->type != null) {
            $suggestions->where('s.type', $request->type);
        }

        $suggestions =  $suggestions->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        Log::info($request->lat);
        Log::info($request->lng);

        foreach ($suggestions as $suggestion) {
            $suggestion->ratingBarang = review::where('service_id', '=', $suggestion->service_id)
                ->avg('rating');
            $suggestion->ratingFreelancer = review::where('client_id', '=', $suggestion->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($suggestion->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $suggestion->totalBarang = $countPackage + $countCustom;
            $suggestion->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $suggestion->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($suggestion->ratingBarang * $weights['ratingBarang']);
            Log::info($suggestion->totalBarang * $weights['totalBarang']);
            Log::info($suggestion->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($suggestion->totalOrder * $weights['totalOrder']);

            $score = $suggestion->ratingBarang * $weights['ratingBarang'] +
                $suggestion->ratingFreelancer * $weights['ratingPenjual'] +
                $suggestion->totalBarang * $weights['totalBarang'] +
                $suggestion->totalOrder * $weights['totalOrder'];

            $suggestion->lowestPrice = $this->getLowestPrice($suggestion->service_id);
            $var = $this->getRating($suggestion->service_id);
            $suggestion->count = $var['count'];
            $suggestion->rating = number_format($var['rating'], 1);
            $suggestion->servicePic = $this->getAImage($suggestion->service_id);
            $suggestion->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($suggestion->service_id);

            if ($suggestion->user_id == $currentUserId) {
                $suggestion->isSeller = true;
            } else {
                $suggestion->isSeller = false;
            }

            if ($request->lat != null && $request->lng != null) {
                $suggestion->distance = $this->haversineGreatCircleDistance(
                    $request->lat,
                    $request->lng,
                    $suggestion->lat,
                    $suggestion->lng,
                );

                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'serviceFav' => $suggestion->serviceFav,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'distance' => $suggestion->distance,
                    'isSeller' => $suggestion->isSeller,
                ];
            } else {
                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'serviceFav' => $suggestion->serviceFav,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'isSeller' => $suggestion->isSeller,
                ];
            }
        }

        if ($request->lowRange != null) {
            $lowRange = $request->lowRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($lowRange) {
                return $item['lowestPrice'] >= $lowRange;
            });
        }
        if ($request->highRange != null) {
            $highRange = $request->highRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($highRange) {
                return $item['lowestPrice'] <= $highRange;
            });
        }
        if ($request->rating != null) {
            $rating = $request->rating;
            if ($rating == 5 || $rating == 0) {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] == $rating;
                });
            } else {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] >= $rating;
                });
            }
        }

        Log::info($rankedSuggestions);


        if ($request->lat != null && $request->lng != null) {
            usort($rankedSuggestions, function ($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
        } else {
            usort($rankedSuggestions, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });
        }


        return response()->json([
            'suggestions' => $rankedSuggestions,
        ], 200);
    }

    public function filterDataNotLogged(FilterDataRequest $request)
    {
        $currentUserId = auth()->id();
        $keywords = explode(' ', $request->keyword);
        Log::info($request);

        // type: null, custom: true, lowRange: null, highRange: null, rating: null

        $suggestions = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where(function ($queryBuilder) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $queryBuilder->where('s.title', 'LIKE', '%' . $keyword . '%');
                }
            })
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 's.custom_order', 'sc.subcategory_name', 's.type', 's.location', 's.lat', 's.lng', 'f.freelancer_id');

        if ($request->type != null) {
            $suggestions->where('s.type', $request->type);
        }

        $suggestions =  $suggestions->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($suggestions as $suggestion) {
            $suggestion->ratingBarang = review::where('service_id', '=', $suggestion->service_id)
                ->avg('rating');
            $suggestion->ratingFreelancer = review::where('client_id', '=', $suggestion->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($suggestion->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $suggestion->totalBarang = $countPackage + $countCustom;
            $suggestion->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $suggestion->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($suggestion->ratingBarang * $weights['ratingBarang']);
            Log::info($suggestion->totalBarang * $weights['totalBarang']);
            Log::info($suggestion->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($suggestion->totalOrder * $weights['totalOrder']);

            $score = $suggestion->ratingBarang * $weights['ratingBarang'] +
                $suggestion->ratingFreelancer * $weights['ratingPenjual'] +
                $suggestion->totalBarang * $weights['totalBarang'] +
                $suggestion->totalOrder * $weights['totalOrder'];

            $suggestion->lowestPrice = $this->getLowestPrice($suggestion->service_id);
            $var = $this->getRating($suggestion->service_id);
            $suggestion->count = $var['count'];
            $suggestion->rating = number_format($var['rating'], 1);
            $suggestion->servicePic = $this->getAImage($suggestion->service_id);

            if ($request->lat != null && $request->lng != null) {
                Log::info('calledlat');
                $suggestion->distance = $this->haversineGreatCircleDistance(
                    $request->lat,
                    $request->lng,
                    $suggestion->lat,
                    $suggestion->lng,
                );

                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'distance' => $suggestion->distance,
                    'isSeller' => false,
                ];
            } else {
                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'isSeller' => false,
                ];
            }
        }

        if ($request->lowRange != null) {
            $lowRange = $request->lowRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($lowRange) {
                return $item['lowestPrice'] >= $lowRange;
            });
        }
        if ($request->highRange != null) {
            $highRange = $request->highRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($highRange) {
                return $item['lowestPrice'] <= $highRange;
            });
        }
        if ($request->rating != null) {
            $rating = $request->rating;
            if ($rating == 5 || $rating == 0) {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] == $rating;
                });
            } else {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] >= $rating;
                });
            }
        }

        Log::info($rankedSuggestions);

        if ($request->lat != null && $request->lng != null) {
            usort($rankedSuggestions, function ($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
        } else {
            usort($rankedSuggestions, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });
        }

        return response()->json([
            'suggestions' => $rankedSuggestions,
        ], 200);
    }

    public function filterSubCategory(FilterSubCategoryRequest $request)
    {
        $currentUserId = auth()->id();

        $suggestions = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.type', 's.location', 's.custom_order', 's.lat', 's.lng', 'f.freelancer_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where('s.subcategory_id', '=', $request->subcategory_id);


        if ($request->type != null) {
            $suggestions->where('s.type', $request->type);
        }

        $suggestions =  $suggestions->get();
        Log::info($suggestions);

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($suggestions as $suggestion) {
            $suggestion->ratingBarang = review::where('service_id', '=', $suggestion->service_id)
                ->avg('rating');
            $suggestion->ratingFreelancer = review::where('client_id', '=', $suggestion->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($suggestion->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $suggestion->totalBarang = $countPackage + $countCustom;
            $suggestion->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $suggestion->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($suggestion->ratingBarang * $weights['ratingBarang']);
            Log::info($suggestion->totalBarang * $weights['totalBarang']);
            Log::info($suggestion->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($suggestion->totalOrder * $weights['totalOrder']);

            $score = $suggestion->ratingBarang * $weights['ratingBarang'] +
                $suggestion->ratingFreelancer * $weights['ratingPenjual'] +
                $suggestion->totalBarang * $weights['totalBarang'] +
                $suggestion->totalOrder * $weights['totalOrder'];

            $suggestion->lowestPrice = $this->getLowestPrice($suggestion->service_id);
            $var = $this->getRating($suggestion->service_id);
            $suggestion->count = $var['count'];
            $suggestion->rating = number_format($var['rating'], 1);
            $suggestion->servicePic = $this->getAImage($suggestion->service_id);
            $suggestion->serviceFav = app('App\Http\Controllers\API\SavedServiceController')->show($suggestion->service_id);

            if ($suggestion->user_id == $currentUserId) {
                $suggestion->isSeller = true;
            } else {
                $suggestion->isSeller = false;
            }

            if ($request->lat != null && $request->lng != null) {
                $suggestion->distance = $this->haversineGreatCircleDistance(
                    $request->lat,
                    $request->lng,
                    $suggestion->lat,
                    $suggestion->lng,
                );

                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'serviceFav' => $suggestion->serviceFav,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'distance' => $suggestion->distance,
                    'isSeller' => $suggestion->isSeller,
                ];
            } else {
                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'serviceFav' => $suggestion->serviceFav,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'isSeller' => $suggestion->isSeller,
                ];
            }
        }

        Log::info('LOW: ' . $request->keyword);
        Log::info('HIGH: ' . $request->highRange);
        Log::info('RATING: ' . $request->rating);

        if ($request->lowRange != null) {
            $lowRange = $request->lowRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($lowRange) {
                return $item['lowestPrice'] >= $lowRange;
            });
        }
        if ($request->highRange != null) {
            $highRange = $request->highRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($highRange) {
                return $item['lowestPrice'] <= $highRange;
            });
        }
        if ($request->rating != null) {
            $rating = $request->rating;
            if ($rating == 5 || $rating == 0) {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] == $rating;
                });
            } else {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] >= $rating;
                });
            }
        }

        Log::info($rankedSuggestions);

        if ($request->lat != null && $request->lng != null) {
            usort($rankedSuggestions, function ($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
        } else {
            usort($rankedSuggestions, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });
        }

        return response()->json([
            'suggestions' => $rankedSuggestions,
        ], 200);
    }

    public function filterSubCategoryNotLogged(FilterSubCategoryRequest $request)
    {
        $suggestions = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->select('u.user_id', 'u.name', 's.service_id', 'p.piclink', 's.title', 's.description', 'u.email', 'sc.subcategory_name', 's.type', 's.location', 's.custom_order', 's.lat', 's.lng', 'f.freelancer_id')
            ->where('u.status', 'active')
            ->where('s.status', 'active')
            ->where('s.subcategory_id', '=', $request->subcategory_id);

        if ($request->type != null) {
            $suggestions->where('s.type', $request->type);
        }

        $suggestions =  $suggestions->get();

        $weights = [
            'ratingBarang' => 0.256,
            'ratingPenjual' => 0.258,
            'totalBarang' => 0.238,
            'totalOrder' => 0.248,
        ];

        $rankedSuggestions = [];

        foreach ($suggestions as $suggestion) {
            $suggestion->ratingBarang = review::where('service_id', '=', $suggestion->service_id)
                ->avg('rating');
            $suggestion->ratingFreelancer = review::where('client_id', '=', $suggestion->user_id)
                ->avg('rating');

            $countPackage = DB::table('order as o')
                ->leftJoin('service_package as sp', 'sp.package_id', '=', 'o.package_id')
                ->where('sp.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            $countCustom = DB::table('order as o')
                ->leftJoin('custom_orders as co', 'co.custom_id', '=', 'o.custom_id')
                ->where('co.service_id', $suggestion->service_id)
                ->where('o.order_status', 'completed')
                ->count();

            Log::info($suggestion->service_id . ' ' . $countPackage . ' ' . $countCustom);

            $suggestion->totalBarang = $countPackage + $countCustom;
            $suggestion->totalOrder = DB::table('order as o')
                ->where('freelancer_id', $suggestion->freelancer_id)
                ->where('o.order_status', 'completed')
                ->count();

            $score = 0;
            Log::info($suggestion->ratingBarang * $weights['ratingBarang']);
            Log::info($suggestion->totalBarang * $weights['totalBarang']);
            Log::info($suggestion->ratingFreelancer * $weights['ratingPenjual']);
            Log::info($suggestion->totalOrder * $weights['totalOrder']);

            $score = $suggestion->ratingBarang * $weights['ratingBarang'] +
                $suggestion->ratingFreelancer * $weights['ratingPenjual'] +
                $suggestion->totalBarang * $weights['totalBarang'] +
                $suggestion->totalOrder * $weights['totalOrder'];

            $suggestion->lowestPrice = $this->getLowestPrice($suggestion->service_id);
            $var = $this->getRating($suggestion->service_id);
            $suggestion->count = $var['count'];
            $suggestion->rating = number_format($var['rating'], 1);
            $suggestion->servicePic = $this->getAImage($suggestion->service_id);

            if ($request->lat != null && $request->lng != null) {
                $suggestion->distance = $this->haversineGreatCircleDistance(
                    $request->lat,
                    $request->lng,
                    $suggestion->lat,
                    $suggestion->lng,
                );

                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'distance' => $suggestion->distance,
                    'isSeller' => false,
                ];
            } else {
                $rankedSuggestions[] = [
                    'title' => $suggestion->title,
                    'description' => $suggestion->description,
                    'service_id' => $suggestion->service_id,
                    'score' => $score,

                    'lowestPrice' => $suggestion->lowestPrice,
                    'count' => $suggestion->count,
                    'rating' => $suggestion->rating,
                    'servicePic' => $suggestion->servicePic->piclink,
                    'name' => $suggestion->name,
                    'email' => $suggestion->email,
                    'user_id' => $suggestion->user_id,
                    'piclink' => $suggestion->piclink,
                    'complete' => $suggestion->totalBarang,
                    'subcategory_name' => $suggestion->subcategory_name,
                    'location' => $suggestion->location,
                    'custom_order' => $suggestion->custom_order,
                    'type' => $suggestion->type,
                    'isSeller' => false,
                ];
            }
        }

        if ($request->lowRange != null) {
            $lowRange = $request->lowRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($lowRange) {
                return $item['lowestPrice'] >= $lowRange;
            });
        }
        if ($request->highRange != null) {
            $highRange = $request->highRange;
            $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($highRange) {
                return $item['lowestPrice'] <= $highRange;
            });
        }
        if ($request->rating != null) {
            $rating = $request->rating;
            if ($rating == 5 || $rating == 0) {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] == $rating;
                });
            } else {
                $rankedSuggestions = array_filter($rankedSuggestions, function ($item) use ($rating) {
                    return $item['rating'] >= $rating;
                });
            }
        }

        Log::info($rankedSuggestions);

        if ($request->lat != null && $request->lng != null) {
            usort($rankedSuggestions, function ($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
        } else {
            usort($rankedSuggestions, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });
        }

        return response()->json([
            'suggestions' => $rankedSuggestions,
        ], 200);
    }
}
