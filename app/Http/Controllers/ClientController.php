<?php

namespace App\Http\Controllers;

use App\Models\client;
use App\Models\picture;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function client()
    {
        $db_client = DB::table('client as c')
            ->leftJoin('user as u', 'c.user_id', '=', 'u.user_id')
            ->leftJoin('picture as p', 'u.picture_id', '=', 'p.picture_id')
            ->select('c.client_id', 'u.user_id', 'c.total_spent', 'c.orders_made', 'u.picture_id', 'p.picasset', 'u.name', 'u.email', 'u.created_at', 'u.updated_at', 'u.status', 'u.profile_type')
            ->latest()
            ->get();

        return view('layouts.client', compact('db_client'));
    }

    public function clientStore(Request $request)
    {
        $currentTimestamp = Carbon::now();

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:8',
            'status' => 'required|in:active,suspended',
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
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
                'profile_type' => 'client',
                'last_login' => null,
            ];

            $user_id = DB::table('user')->insertGetId($userData);

            client::insert([
                'user_id' => $user_id,
                'orders_made' => 0,
                'total_spent' => 0,
            ]);

            $notification = array(
                'message' => 'Client Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('client')->with($notification);
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
                'profile_type' => 'client',
                'last_login' => null,
            ];
            $user_id = DB::table('user')->insertGetId($userData);

            client::insert([
                'user_id' => $user_id,
                'orders_made' => 0,
                'total_spent' => 0,
            ]);


            $notification = array(
                'message' => 'Client Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('client')->with($notification);
        }
    }

    public function clientDelete($client_id, $user_id)
    {
        client::findOrFail($client_id)->delete();
        user::findOrFail($user_id)->delete();

        $notification = array(
            'message' => 'Client Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function clientDeletePic($client_id, $user_id, $picture_id)
    {
        client::findOrFail($client_id)->delete();
        user::findOrFail($user_id)->delete();

        $picture = picture::findOrFail($picture_id);
        $filename = basename($picture->picasset);
        Storage::delete('public/images/' . $filename);

        $picture->delete();

        $notification = array(
            'message' => 'Client Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function clientEdit(Request $request)
    {
        $id = $request->id;
        $profileData = user::find($id);
        $currentTimestamp = Carbon::now();

        if ($profileData->email == $request->email) {
            $request->validate([
                'name' => 'required|string|max:200',
                'status' => 'required|in:active,suspended',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:200',
                'email' => 'required|email|unique:user',
                'status' => 'required|in:active,suspended',
                'photo' => 'image|mimes:jpeg,png,jpg,gif,webp',
            ]);
        }

        if ($request->file('photo')) {

            if (!empty($profileData->picture_id)) {
                $data = user::join('picture', 'user.picture_id', '=', 'picture.picture_id')
                    ->select('picture.picasset', 'user.picture_id', 'user.name', 'user.email', 'user.created_at', 'user.updated_at')
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

                $notification = array(
                    'message' => 'Client Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('client')->with($notification);
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

                $notification = array(
                    'message' => 'Client Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('client')->with($notification);
            }
        } else {
            user::findOrFail($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => $currentTimestamp,
                'status' => $request->status
            ]);

            $notification = array(
                'message' => 'Client Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('client')->with($notification);
        }
    }
}
