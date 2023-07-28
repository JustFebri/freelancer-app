<?php

namespace App\Http\Controllers;

use App\Models\client;
use App\Models\picture;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function client()
    {
        $type = DB::table('client')
            ->leftJoin('picture', 'client.picture_id', '=', 'picture.picture_id')
            ->select('picture.file', 'picture.filetype', 'client.picture_id', 'client.client_id', 'client.name', 'client.email', 'client.location', 'client.created_at', 'client.updated_at', 'client.status')
            ->latest()
            ->get();
        return view('layouts.client', compact('type'));
    }

    public function clientStore(request $request)
    {
        $currentTimestamp = Carbon::now();
        $location = $request->location ?? '';

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:client',
            'password' => 'required|min:8',
        ]);

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $filename = date('YmDHi') . $file->getClientOriginalName();
            $newPicture = new picture;
            $newPicture->filename = $filename;
            $newPicture->file = $file->getContent();
            $newPicture->filetype = $file->getMimeType();
            $newPicture->save();

            client::insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'location' => $location,
                'picture_id' => $newPicture->picture_id,
                'status' => $request->status,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                'email_verified_at' => null,
                'remember_token' => null
            ]);

            $notification = array(
                'message' => 'Client Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('client')->with($notification);
        } else {

            client::insert([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'location' => $location,
                'picture_id' => null,
                'status' =>  $request->status,
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp,
                'email_verified_at' => null,
                'remember_token' => null
            ]);

            $notification = array(
                'message' => 'Client Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('client')->with($notification);
        }
    }

    public function clientDelete($client_id)
    {
        client::findOrFail($client_id)->delete();

        $notification = array(
            'message' => 'Client Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function clientDeletePic($client_id, $picture_id)
    {
        client::findOrFail($client_id)->delete();

        picture::findOrFail($picture_id)->delete();


        $notification = array(
            'message' => 'Client Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function clientEdit(Request $request)
    {
        $id = $request->id;
        $profileData = client::find($id);
        $currentTimestamp = Carbon::now();
        $location = $request->location ?? '';

        if ($profileData->email == $request->email) {
            $request->validate([
                'name' => 'required|max:200',
            ]);
        } else {
            $request->validate([
                'name' => 'required|max:200',
                'email' => 'required|unique:client',
            ]);
        }

        if ($request->file('photo')) {

            if (!empty($profileData->picture_id)) {
                $data = client::join('picture', 'client.picture_id', '=', 'picture.picture_id')
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
                    'message' => 'Client Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('client')->with($notification);
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
                    'message' => 'Client Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('client')->with($notification);
            }
        } else {
            client::findOrFail($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'location' => $location,
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
