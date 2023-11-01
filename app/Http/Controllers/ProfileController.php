<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\picture;
use App\Models\admin;
use Carbon\Traits\Timestamp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the admin's profile form.
     */
    public function profile()
    {
        $id = Auth::user()->admin_id;
        $profileData = admin::find($id);


        if (empty($profileData->picture_id)) {
            return view('admin.profile', compact('profileData'));
        } else {
            $profileData = admin::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                ->select('picture.picasset', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
                ->find($id);
            return view('admin.profile', compact('profileData'));
        };
    }

    public function profileStore(Request $request)
    {
        $id = Auth::user()->admin_id;
        $profileData = admin::find($id);
        $currentTimestamp = Carbon::now();

        if ($request->file('photo')) {
            if (!empty($profileData->picture_id)) {
                $profileData->email = $request->email;
                $data = admin::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                    ->select('admin.picture_id', 'picture.picasset', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
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

                $profileData->save();

                $notification = array(
                    'message' => 'Profile Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->back()->with($notification);
            } else {
                $request->file('photo')->store('public/images');
                $filename = $request->file('photo')->hashName();
                $path = 'public/images/' . $filename;
                $url = asset(Storage::url($path));

                $newPicture = new picture;
                $newPicture->piclink = $url;
                $newPicture->picasset = Storage::url($path);
                $newPicture->save();

                $profileData->email = $request->email;
                $profileData->picture_id = $newPicture->picture_id;
                $profileData->save();
                $notification = array(
                    'message' => 'Profile Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->back()->with($notification);
            }
        } else {
            $profileData->email = $request->email;
            $profileData->save();
            $notification = array(
                'message' => 'Profile Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->back()->with($notification);
        }
    }

    function changePassword(Request $request)
    {
        $id = Auth::user()->admin_id;
        $profileData = admin::find($id);
        if (empty($profileData->picture_id)) {
            return view('admin.change_password', compact('profileData'));
        } else {
            $profileData = admin::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                ->select('picture.picasset', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
                ->find($id);
            return view('admin.change_password', compact('profileData'));
        };
    }

    function updatePassword(Request $request)
    {

        // Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8'
        ]);

        // Match The Old Password
        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update The New Password
        admin::where('admin_id', auth()->user()->admin_id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }
}
