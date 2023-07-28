<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\picture;
use App\Models\User;
use Carbon\Traits\Timestamp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function profile()
    {
        $id = Auth::user()->admin_id;
        $profileData = User::find($id);


        if (empty($profileData->picture_id)) {
            return view('admin.profile', compact('profileData'));
        } else {
            $profileData = User::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                ->select('picture.file', 'picture.filetype', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
                ->find($id);
            return view('admin.profile', compact('profileData'));
        };
    }

    public function profileStore(Request $request)
    {
        $id = Auth::user()->admin_id;
        $profileData = User::find($id);
        $currentTimestamp = Carbon::now();

        if ($request->file('photo')) {
            if (!empty($profileData->picture_id)) {
                $profileData->email = $request->email;
                $data = User::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                    ->select('picture.file', 'admin.picture_id', 'picture.filetype', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
                    ->find($id);

                $oldPic = picture::find($data->picture_id);

                $file = $request->file('photo');

                picture::findOrFail($data->picture_id)->update([
                    'filename' => date('YmDHi') . $file->getClientOriginalName(),
                    'filetype' => $file->getMimeType(),
                    'file' => $file->getContent(),
                    'updated_at' => $currentTimestamp,
                ]);

                $profileData->save();

                $notification = array(
                    'message' => 'Profile Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->back()->with($notification);
            } else {
                $profileData->email = $request->email;
                $file = $request->file('photo');
                $filename = date('YmDHi') . $file->getClientOriginalName();
                $newPicture = new picture;
                $newPicture->filename = $filename;
                $newPicture->file = $file->getContent();
                $newPicture->filetype = $file->getMimeType();
                $newPicture->save();
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
        $profileData = User::find($id);
        if (empty($profileData->picture_id)) {
            return view('admin.change_password', compact('profileData'));
        } else {
            $profileData = User::join('picture', 'admin.picture_id', '=', 'picture.picture_id')
                ->select('picture.file', 'picture.filetype', 'admin.name', 'admin.email', 'admin.created_at', 'admin.updated_at')
                ->find($id);
            return view('admin.change_password', compact('profileData'));
        };
    }

    function updatePassword(Request $request){
        
        // Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed|min:8'
        ]);

        // Match The Old Password
        if(!Hash::check($request->old_password, auth::user()->password)){
            $notification = array(
                'message'=> 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }

        // Update The New Password
        User::where('admin_id', auth()->user()->admin_id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
            'message'=> 'Password Change Successfully',
            'alert-type' => 'success'
        );
        return back()->with($notification);
    }
}
