<?php

namespace App\Http\Controllers;

use App\Models\freelancer;
use App\Models\picture;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FreelancerController extends Controller
{
    public function freelancer()
    {
        $type = DB::table('freelancer')
            ->leftJoin('picture', 'freelancer.picture_id', '=', 'picture.picture_id')
            ->select('picture.picture_id', 'picture.filetype', 'picture.file', 'freelancer.freelancer_id', 'freelancer.name', 'freelancer.email', 'freelancer.identity_number', 'freelancer.information', 'freelancer.location', 'freelancer.created_at', 'freelancer.updated_at', 'freelancer.status')
            ->latest()
            ->get();
        return view('layouts.freelancer', compact('type'));
    }

    public function freelancerStore(request $request)
    {
        $currentTimestamp = Carbon::now();
        $location = $request->location ?? '';
        $information = $request->information ?? '';

        $request->validate([
            'name' => 'required|max:200|string',
            'email' => 'required|unique:freelancer|email',
            'password' => 'required|min:8',
            'identity_number' => 'required|min:16|unique:freelancer'
        ]);

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = date('YmDHi') . $file->getClientOriginalName();
            $newPicture = new picture;
            $newPicture->filename = $filename;
            $newPicture->file = $file->getContent();
            $newPicture->filetype = $file->getMimeType();
            $newPicture->save();

            freelancer::insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'location' => $location,
                'picture_id' => $newPicture->picture_id,
                'status' => $request->status,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                'information' => $information,
                'identity_number' => $request->identity_number,
                'email_verified_at' => null,
                'remember_token' => null
            ]);

            $notification = array(
                'message' => 'Freelancer Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('freelancer')->with($notification);
        } else {

            freelancer::insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'location' => $location,
                'picture_id' => null,
                'status' =>  $request->status,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                'information' => $information,
                'identity_number' => $request->identity_number,
                'email_verified_at' => null,
                'remember_token' => null
            ]);

            $notification = array(
                'message' => 'Freelancer Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('freelancer')->with($notification);
        }
    }

    public function freelancerDelete($freelancer_id)
    {
        freelancer::findOrFail($freelancer_id)->delete();

        $notification = array(
            'message' => 'Freelancer Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function freelancerDeletePic($freelancer_id, $picture_id)
    {
        freelancer::findOrFail($freelancer_id)->delete();

        picture::findOrFail($picture_id)->delete();


        $notification = array(
            'message' => 'Freelancer Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function freelancerEdit(Request $request)
    {
        $id = $request->id;
        $profileData = freelancer::find($id);
        $currentTimestamp = Carbon::now();
        $location = $request->location ?? '';

        if ($profileData->email == $request->email && $profileData->identity_number == $request->identity_number) {
            $request->validate([
                'name' => 'required|max:200',
            ]);
        } else if ($profileData->email == $request->email) {
            $request->validate([
                'name' => 'required|max:200',
                'identity_number' => 'required|unique:freelancer'
            ]);
        } else if ($profileData->identity_number == $request->identity_number) {
            $request->validate([
                'name' => 'required|max:200',
                'email' => 'required|unique:freelancer',
            ]);
        } else {
            $request->validate([
                'name' => 'required|max:200',
                'email' => 'required|unique:freelancer',
                'identity_number' => 'required|unique:freelancer'
            ]);
        }

        if ($request->file('photo')) {

            if (!empty($profileData->picture_id)) {
                $data = freelancer::join('picture', 'freelancer.picture_id', '=', 'picture.picture_id')
                    ->select('picture.file', 'client.picture_id', 'picture.filetype', 'client.name', 'client.email', 'client.created_at', 'client.updated_at')
                    ->find($id);

                $file = $request->file('photo');
                picture::findOrFail($data->picture_id)->update([
                    'filename' => date('YmDHi') . $file->getClientOriginalName(),
                    'filetype' => $file->getMimeType(),
                    'file' => $file->getContent(),
                    'updated_at' => $currentTimestamp,
                ]);

                $profileData->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => $location,
                    'updated_at' => $currentTimestamp,
                    'status' => $request->status
                ]);

                $notification = array(
                    'message' => 'Freelancer Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('freelancer')->with($notification);
            } else {
                $file = $request->file('photo');
                $id = picture::insertGetId([
                    'filename' => date('YmDHi') . $file->getClientOriginalName(),
                    'filetype' => $file->getMimeType(),
                    'file' => $file->getContent(),
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);

                $profileData->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => $location,
                    'updated_at' => $currentTimestamp,
                    'picture_id' => $id,
                    'status' => $request->status
                ]);

                $notification = array(
                    'message' => 'Freelancer Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('freelancer')->with($notification);
            }
        } else {
            freelancer::findOrFail($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'location' => $location,
                'updated_at' => $currentTimestamp,
                'status' => $request->status
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
        $freelancer = DB::table('freelancer')
            ->leftJoin('picture', 'freelancer.picture_id', '=', 'picture.picture_id')
            ->select('picture.picture_id', 'picture.filetype', 'picture.file', 'freelancer.freelancer_id', 'freelancer.name', 'freelancer.email', 'freelancer.identity_number', 'freelancer.information', 'freelancer.location', 'freelancer.created_at', 'freelancer.updated_at', 'freelancer.status')
            ->where('freelancer.freelancer_id', $freelancer_id)
            ->first();

        $formattedDate = Carbon::parse($freelancer->created_at)->format('F d, Y');

        return view('layouts.freelancerProfile', compact('freelancer', 'formattedDate'));
    }
}
