<?php

namespace App\Http\Controllers;

use App\Mail\ApproveEmail;
use App\Mail\MyTestEmail;
use App\Mail\RejectEmail;
use App\Models\client;
use App\Models\freelancer;
use App\Models\freelancer_language;
use App\Models\freelancer_skill;
use App\Models\personal_url;
use App\Models\picture;
use App\Models\review;
use App\Models\service;
use App\Models\service_img;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FreelancerController extends Controller
{
    public function freelancer()
    {
        $db_freelancer = DB::table('freelancer as f')
            ->leftJoin('user as u', 'f.user_id', '=', 'u.user_id')
            ->leftJoin('picture as p', 'u.picture_id', '=', 'p.picture_id')
            ->select('f.freelancer_id', 'f.identity_number', 'f.description', 'f.IsApproved', 'f.rating', 'f.revenue', 'u.user_id', 'u.picture_id', 'p.picasset', 'u.name', 'u.email', 'u.created_at', 'u.updated_at', 'u.status', 'u.profile_type')
            ->where(function ($query) {
                $query->where('f.IsApproved', '=', 'approved')
                    ->orWhereNull('f.IsApproved');
            })
            ->latest()
            ->get();

        $pendingFreelancerCount = DB::table('freelancer as f')
            ->where('f.IsApproved', '=', 'pending')
            ->count();

        return view('layouts.freelancer', compact('db_freelancer', 'pendingFreelancerCount'));
    }

    public function freelancerRequest()
    {
        $db_freelancer = DB::table('freelancer as f')
            ->leftJoin('user as u', 'f.user_id', '=', 'u.user_id')
            ->leftJoin('picture as p', 'u.picture_id', '=', 'p.picture_id')
            ->select('f.freelancer_id', 'f.identity_number', 'f.description', 'f.IsApproved', 'f.rating', 'f.revenue', 'u.user_id', 'u.picture_id', 'p.picasset', 'u.name', 'u.email', 'u.created_at', 'u.updated_at', 'u.status', 'u.profile_type')
            ->where('f.IsApproved', '=', 'pending')
            ->latest()
            ->get();

        return view('layouts.freelancerVerificationRequest', compact('db_freelancer',));
    }
    public function freelancerStore(request $request)
    {
        $currentTimestamp = Carbon::now();
        $description = $request->description ?? '';

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:8',
            'identity_number' => 'required|numeric|digits:16|unique:freelancer',
            'description' => 'required|string|min:150',
            'status' => 'required|in:active,suspended',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if ($request->file('photo')) {
            $request->file('photo')->store('public/images');
            $filename = $request->file('photo')->hashName();
            $path = 'public/images/' . $filename;
            $url = asset(Storage::url($path));

            $newPicture = new picture;
            $newPicture->piclink = $url;
            $newPicture->picasset = Storage::url($path);
            $newPicture->save();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'picture_id' => $newPicture->picture_id,
                'status' => $request->status,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                'email_verified_at' => Carbon::now(),
                'remember_token' => null,
                'profile_type' => 'freelancer',
                'last_login' => null,
            ];

            $user_id = DB::table('user')->insertGetId($userData);

            client::insert([
                'user_id' => $user_id,
                'orders_made' => 0,
                'total_spent' => 0,
            ]);

            freelancer::insert([
                'user_id' => $user_id,
                'identity_number' => $request->identity_number,
                'description' => $description,
                'rating' => 0,
                'revenue' => 0,
            ]);

            $notification = array(
                'message' => 'Freelancer Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('freelancer')->with($notification);
        } else {

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'picture_id' => null,
                'status' => $request->status,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                'email_verified_at' => null,
                'remember_token' => null,
                'profile_type' => 'freelancer',
                'last_login' => null,
            ];

            $user_id = DB::table('user')->insertGetId($userData);

            client::insert([
                'user_id' => $user_id,
                'orders_made' => 0,
                'total_spent' => 0,
            ]);

            freelancer::insert([
                'user_id' => $user_id,
                'identity_number' => $request->identity_number,
                'description' => $description,
                'rating' => 0,
                'revenue' => 0,
            ]);

            $notification = array(
                'message' => 'Freelancer Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('freelancer')->with($notification);
        }
    }

    public function freelancerDelete($freelancer_id, $user_id)
    {
        freelancer::findOrFail($freelancer_id)->delete();
        DB::table('client')->where('user_id', $user_id)->delete();
        user::findOrFail($user_id)->delete();

        $notification = array(
            'message' => 'Freelancer Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function freelancerDeletePic($freelancer_id, $user_id, $picture_id)
    {
        freelancer::findOrFail($freelancer_id)->delete();
        DB::table('client')->where('user_id', $user_id)->delete();
        user::findOrFail($user_id)->delete();

        $picture = picture::findOrFail($picture_id);
        $filename = basename($picture->picasset);
        Storage::delete('public/images/' . $filename);
        $picture->delete();

        $notification = array(
            'message' => 'Freelancer Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function freelancerEdit(Request $request)
    {
        $id = $request->id;
        $profileData = user::find($id);
        $freelancerData = DB::table('freelancer')->where('user_id', $id)->first();
        $currentTimestamp = Carbon::now();
        $desc = $request->description ?? '';

        Log::info($request);

        if (
            $profileData->email == $request->email &&
            $freelancerData->identity_number == $request->identity_number
        ) {
            $request->validate([
                'name' => 'required|string|max:200',
                'description' => 'required|string|min:150',
                'status' => 'required|in:active,suspended',
            ]);
        } else if ($profileData->email == $request->email) {
            $request->validate([
                'name' => 'required|string|max:200',
                'identity_number' => 'required|numeric|digits:16|unique:freelancer',
                'description' => 'required|string|min:150',
                'status' => 'required|in:active,suspended',
            ]);
        } else if ($freelancerData->identity_number == $request->identity_number) {
            $request->validate([
                'name' => 'required|string|max:200',
                'email' => 'required|email|unique:user',
                'description' => 'required|string|min:150',
                'status' => 'required|in:active,suspended',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:200',
                'email' => 'required|email|unique:user',
                'identity_number' => 'required|numeric|digits:16|unique:freelancer',
                'description' => 'required|string|min:150',
                'status' => 'required|in:active,suspended',
            ]);
        }

        if ($request->file('photo')) {

            if (!empty($profileData->picture_id)) {
                $data = user::join('picture', 'user.picture_id', '=', 'picture.picture_id')
                    ->select('user.picture_id', 'picture.picasset', 'user.name', 'user.email', 'user.created_at', 'user.updated_at')
                    ->find($id);

                $filename = basename($data->picasset);
                Storage::delete('public/images/' . $filename);

                $file = $request->file('photo');
                $file->store('public/images');
                $filename = $request->file('photo')->hashName();
                $path = 'public/images/' . $filename;
                $url = asset(Storage::url($path));

                picture::findOrFail($data->picture_id)->update([
                    'piclink' => $url,
                    'picasset' => Storage::url($path),
                ]);

                $profileData->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'updated_at' => $currentTimestamp,
                    'status' => $request->status
                ]);
                DB::table('freelancer')->where('user_id', $id)->update([
                    'identity_number' => $request->identity_number,
                    'description' =>  $desc,
                ]);

                $notification = array(
                    'message' => 'Freelancer Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('freelancer')->with($notification);
            } else {
                $request->file('photo')->store('public/images');
                $filename = $request->file('photo')->hashName();
                $path = 'public/images/' . $filename;
                $url = asset(Storage::url($path));

                $id = picture::insertGetId([
                    'piclink' => $url,
                    'picasset' => Storage::url($path),
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);

                $profileData->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'updated_at' => $currentTimestamp,
                    'picture_id' => $id,
                    'status' => $request->status
                ]);

                DB::table('freelancer')->where('user_id', $id)->update([
                    'identity_number' => $request->identity_number,
                    'description' =>  $desc,
                ]);

                $notification = array(
                    'message' => 'Freelancer Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('freelancer')->with($notification);
            }
        } else {
            $profileData->update([
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => $currentTimestamp,
                'status' => $request->status
            ]);

            DB::table('freelancer')->where('user_id', $id)->update([
                'identity_number' => $request->identity_number,
                'description' =>  $desc,
            ]);

            $notification = array(
                'message' => 'Freelancer Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('freelancer')->with($notification);
        }
    }

    public function freelancerProfile($freelancer_id)
    {
        $freelancer = DB::table('freelancer as f')
            ->leftJoin('user as u', 'f.user_id', '=', 'u.user_id')
            ->leftJoin('picture as p', 'u.picture_id', '=', 'p.picture_id')
            ->select('f.freelancer_id', 'f.identity_number', 'f.description', 'f.rating', 'f.revenue', 'u.user_id', 'u.picture_id', 'p.picasset',  'u.name', 'u.email', 'u.created_at', 'u.updated_at', 'u.status', 'u.profile_type')
            ->where('f.freelancer_id', $freelancer_id)
            ->first();

        $user = user::find($freelancer->user_id);

        $formattedDate = Carbon::parse($user->email_verified_at)->format('F d, Y');

        $personalurl = personal_url::where('freelancer_id', $freelancer_id)->get();
        $reviewCount = review::where('freelancer_id', $freelancer_id)->count();
        $avgRating = round(review::where('freelancer_id', $freelancer_id)->avg('rating'), 2);

        $dataSK = DB::table('freelancer_skill as fs')
            ->leftJoin('skill as s', 's.skill_id', '=', 'fs.skill_id')
            ->where('freelancer_id', $freelancer_id)
            ->get();

        $dataLG = DB::table('freelancer_language as fl')
            ->leftJoin('language as l', 'l.language_id', '=', 'fl.language_id')
            ->where('freelancer_id', $freelancer_id)
            ->get();

        $portfolios = DB::table('portfolio as p')
            ->leftJoin('portfolio_img as pi', 'pi.portfolio_id', '=', 'p.portfolio_id')
            ->leftJoin('picture as pic', 'pic.picture_id', '=', 'pi.picture_id')
            ->where('freelancer_id', $freelancer_id)
            ->get();

        $services = service::where('freelancer_id', $freelancer_id)->get();
        foreach ($services as $item) {
            $item->picasset = DB::table('service_img as si')
                ->leftJoin('picture as p', 'p.picture_id', '=', 'si.picture_id')
                ->where('si.service_id', $item->service_id)
                ->pluck('p.picasset')
                ->first();
        }

        return view('layouts.freelancerProfile', compact('freelancer', 'formattedDate', 'personalurl', 'reviewCount', 'avgRating', 'portfolios', 'dataLG', 'dataSK','services'));
    }

    public function freelancerRequestDetails($freelancer_id)
    {
        $freelancer = DB::table('freelancer as f')
            ->leftJoin('user as u', 'f.user_id', '=', 'u.user_id')
            ->leftJoin('picture as p', 'u.picture_id', '=', 'p.picture_id')
            ->leftJoin('picture as p1', 'p1.picture_id', '=', 'f.id_card')
            ->leftJoin('picture as p2', 'p2.picture_id', '=', 'f.id_card_with_selfie')
            ->select(
                'f.freelancer_id',
                'f.identity_number',
                'f.identity_name',
                'f.identity_gender',
                'f.identity_address',
                'f.description',
                'f.rating',
                'f.revenue',
                'u.user_id',
                'u.picture_id',
                'p.picasset',
                'u.name',
                'u.email',
                'u.created_at',
                'u.updated_at',
                'u.status',
                'u.profile_type',
                'f.id_card',
                'f.id_card_with_selfie',
                'p1.picasset as p1',
                'p2.picasset as p2',
            )
            ->where('f.freelancer_id', $freelancer_id)
            ->first();

        $language = DB::table('freelancer_language as fl')
            ->leftJoin('language as l', 'l.language_id', '=', 'fl.language_id')
            ->where('fl.freelancer_id', $freelancer_id)
            ->get();

        $occupation = DB::table('freelancer as f')
            ->leftJoin('occupation as o', 'o.freelancer_id', '=', 'f.freelancer_id')
            ->leftJoin('category as c', 'c.category_id', '=', 'o.category_id')
            ->where('f.freelancer_id', $freelancer_id)
            ->get();

        $sub_occupation = DB::table('freelancer as f')
            ->leftJoin('sub_occupation as o', 'o.freelancer_id', '=', 'f.freelancer_id')
            ->leftJoin('sub_category as c', 'c.subcategory_id', '=', 'o.subcategory_id')
            ->where('f.freelancer_id', $freelancer_id)
            ->get();

        $skills = DB::table('freelancer as f')
            ->leftJoin('freelancer_skill as fs', 'fs.freelancer_id', '=', 'f.freelancer_id')
            ->leftJoin('skill as s', 's.skill_id', '=', 'fs.skill_id')
            ->where('f.freelancer_id', $freelancer_id)
            ->get();

        $url = DB::table('personal_url as pu')
            ->where('pu.freelancer_id', $freelancer_id)
            ->get();

        return view('layouts.freelancerRequestDetails', compact('freelancer', 'occupation', 'sub_occupation', 'skills', 'language', 'url'));
    }

    public function requestApprove($user_id, $freelancer_id)
    {
        $user = user::where('user_id', $user_id)->first();
        $freelancer = freelancer::where('freelancer_id', $freelancer_id)->first();

        if (!$user) {
            $notification = array(
                'message' => 'Request Approval Failed',
                'alert-type' => 'error'
            );
        } else {
            $user->profile_type = 'freelancer';
            $user->save();
            $freelancer->IsApproved = 'approved';
            $freelancer->save();
            Mail::to($user->email)->send(new ApproveEmail($user->name));
            $notification = array(
                'message' => 'Request Approved Successfully',
                'alert-type' => 'success'
            );
        }

        return redirect()->route('freelancer.request')->with($notification);
    }

    public function requestReject(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $user = user::where('user_id', $request->user_id)->first();
        $freelancer = freelancer::where('freelancer_id', $request->freelancer_id)->first();

        if (!$user) {
            $notification = array(
                'message' => 'Request Rejection Failed',
                'alert-type' => 'error'
            );
        } else {
            $freelancer->isApproved = 'rejected';
            $freelancer->save();
            Mail::to($user->email)->send(new RejectEmail($user->name, $request->reason));
            $notification = array(
                'message' => 'Request Rejected Successfully',
                'alert-type' => 'success'
            );
        }

        return redirect()->route('freelancer.request')->with($notification);
    }
}
