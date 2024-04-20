<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\saved_services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SavedServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currentUserId = auth()->id();
        $savedService = DB::table('saved_services as ss')
            ->leftJoin('service as s', 's.service_id', '=', 'ss.service_id')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->where('ss.user_id', '=', $currentUserId)
            ->select('s.service_id', 's.title', 'f.user_id', 'u.name', 'sc.subcategory_name', 's.custom_order', 's.type', 's.location', 'p.picasset', 's.description', 'u.email')
            ->get();

        foreach ($savedService as $item) {
            $item->lowestPrice = app('App\Http\Controllers\API\ServiceController')->getLowestPrice($item->service_id);
            $var = app('App\Http\Controllers\API\ServiceController')->getRating($item->service_id);
            $item->count = $var['count'];
            $item->rating = number_format($var['rating'], 1);
            $item->servicePic = app('App\Http\Controllers\API\ServiceController')->getAImage($item->service_id);
            $item->serviceFav = $this->show($item->service_id);

            if ($item->user_id == $currentUserId) {
                $item->isSeller = true;
            } else {
                $item->isSeller = false;
            }
        }

        return response([
            'data' => $savedService,
            'message' => 'Saved Service fetched successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        Log::info('calling store');
        $currentUserId = auth()->id();
        $serviceId = request('service_id');

        $isServiceSaved = saved_services::where('user_id', $currentUserId)
            ->where('service_id', $serviceId)
            ->exists();

        if ($isServiceSaved) {
            return response([
                'message' => 'Service is already saved for the user.',
            ], 422);
        }

        saved_services::create([
            'user_id' => $currentUserId,
            'service_id' => $serviceId,
        ]);
        Log::info('done store');
        return response([
            'message' => 'Service has been saved for the user.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $serviceId)
    {
        $currentUserId = auth()->id();
        $data = saved_services::where('user_id', '=', $currentUserId)
            ->where('service_id', '=', $serviceId)
            ->exists();

        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $serviceId)
    {
        $currentUserId = auth()->id();

        $deletedRows = saved_services::where('user_id', $currentUserId)
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
}
