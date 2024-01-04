<?php

namespace App\Http\Controllers;

use App\Mail\ApproveService;
use App\Mail\RejectService;
use App\Models\category;
use App\Models\freelancer;
use App\Models\service;
use App\Models\service_img;
use App\Models\sub_category;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ServiceController extends Controller
{
    public function service()
    {
        $db_service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->select('s.service_id', 's.title', 'u.name', 's.type', 'c.category_name', 'sc.subcategory_name', 's.status', 's.created_at', 's.updated_at', 'f.freelancer_id')
            ->where(function ($query) {
                $query->where('s.IsApproved', '=', 'approved')
                    ->orWhereNull('s.IsApproved'); // Include records where IsApproved is NULL
            })
            ->get();

        $pendingServiceCount = DB::table('service as s')
            ->where('s.IsApproved', '=', 'Pending')
            ->count();

        return view('layouts.service', compact('db_service', 'pendingServiceCount'));
    }

    public function serviceRequest()
    {
        $db_service = DB::table('service as s')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->select('s.IsApproved', 's.service_id', 's.title', 'u.name', 's.type', 'c.category_name', 'sc.subcategory_name', 's.status', 's.created_at', 's.updated_at', 'f.freelancer_id')
            ->where('s.IsApproved', '=', 'pending')
            ->get();

        return view('layouts.serviceVerificationRequest', compact('db_service',));
    }

    public function serviceRequestDetails($service_id)
    {
        $service = DB::table('service as s')
            ->leftJoin('sub_category as sc', 'sc.subcategory_id', '=', 's.subcategory_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'sc.category_id')
            ->leftJoin('freelancer as f', 'f.freelancer_id', '=', 's.freelancer_id')
            ->leftJoin('user as u', 'u.user_id', '=', 'f.user_id')
            ->where('s.service_id', $service_id)
            ->select('s.service_id', 's.title', 's.description', 'c.category_name', 'sc.subcategory_name', 'f.freelancer_id', 'u.name', 's.type', 's.location', 's.custom_order')
            ->first();

        $picture = DB::table('service_img as si')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'si.picture_id')
            ->where('si.service_id', $service_id)
            ->get();

        $package = DB::table('service_package as sp')
            ->where('sp.service_id', $service_id)
            ->get();

        return view('layouts.serviceRequestDetails', compact('service', 'picture', 'package',));
    }

    public function requestApprove($service_id)
    {
        $service = service::where('service_id', $service_id)->first();
        $freelancer = freelancer::where('freelancer_id', $service->freelancer_id)->first();
        $user = user::where('user_id', $freelancer->user_id)->first();
        $subcategory = sub_category::where('subcategory_id', $service->subcategory_id)->first();
        $category = category::where('category_id', $subcategory->category_id)->first();

        if (!$service && !$user) {
            $notification = array(
                'message' => 'Request Approval Failed',
                'alert-type' => 'error'
            );
        } else {
            $service->IsApproved = 'approved';
            $service->status = 'active';
            $service->save();

            Mail::to($user->email)->send(new ApproveService($service->title, $user->name, $category->category_name, $subcategory->subcategory_name));
            $notification = array(
                'message' => 'Request Approved Successfully',
                'alert-type' => 'success'
            );
        }

        return redirect()->route('service.request')->with($notification);
    }

    public function requestReject($service_id)
    {
        $service = service::where('service_id', $service_id)->first();
        $freelancer = freelancer::where('freelancer_id', $service->freelancer_id)->first();
        $user = user::where('user_id', $freelancer->user_id)->first();
        $subcategory = sub_category::where('subcategory_id', $service->subcategory_id)->first();
        $category = category::where('category_id', $subcategory->category_id)->first();

        if (!$service && !$user) {
            $notification = array(
                'message' => 'Request Rejection Failed',
                'alert-type' => 'error'
            );
        } else {
            $service->IsApproved = 'rejected';
            $service->save();
            Mail::to($user->email)->send(new RejectService($service->title, $user->name, $category->category_name, $subcategory->subcategory_name));
            $notification = array(
                'message' => 'Request Rejected Successfully',
                'alert-type' => 'success'
            );
        }



        return redirect()->route('service.request')->with($notification);
    }
}
