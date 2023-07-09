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

        $request->validate([
            'name' => 'required|max:200',
            'email' => 'required|unique:client',
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
            if ($request->location != null) {
                client::insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'location' => $request->location,
                    'picture_id' => $newPicture->picture_id,
                    'status' => $request->status,
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);
            } else {
                client::insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'location' => '',
                    'picture_id' => $newPicture->picture_id,
                    'status' => $request->status,
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);
            }

            $notification = array(
                'message' => 'Client Create Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('client')->with($notification);
        } else {
            if ($request->location == null) {
                client::insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'location' => '',
                    'picture_id' => null,
                    'status' =>  $request->status,
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);
            } else {
                client::insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'location' => $request->location,
                    'picture_id' => null,
                    'status' =>  $request->status,
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);
            }


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

        if ($request->file('photo')) {
            
            if (!empty($profileData->picture_id)) {
             
                if($profileData->email == $request->email){
                    $request->validate([
                        'name' => 'required|max:200',
                    ]);
                }else{
                    $request->validate([
                        'name' => 'required|max:200',
                        'email' => 'required|unique:client',
                    ]);
                }
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

                if($request->location== null){
                    $profileData->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'location' => '',
                        'updated_at' => $currentTimestamp,
                        'status' => $request->status
                    ]);
                }else{
                    $profileData->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'location' => $request->location,
                        'updated_at' => $currentTimestamp,
                        'status' => $request->status
                    ]);
                }

                $notification = array(
                    'message' => 'Profile Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('client')->with($notification);
            } else {
                if($profileData->email == $request->email){
                    $request->validate([
                        'name' => 'required|max:200',
                    ]);
                }else{
                    $request->validate([
                        'name' => 'required|max:200',
                        'email' => 'required|unique:client',
                    ]);
                }

                $file = $request->file('photo');
                $id = picture::insertGetId([
                    'filename' => date('YmDHi') . $file->getClientOriginalName(),
                    'filetype' => $file->getMimeType(),
                    'file' => $file->getContent(),
                    'created_at' => $currentTimestamp,
                    'updated_at' => $currentTimestamp,
                ]);

                if($request->location== null){
                    $profileData->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'location' => '',
                        'updated_at' => $currentTimestamp,
                        'picture_id' => $id,
                        'status' => $request->status
                    ]);
                }else{
                    $profileData->update([
                        'name' => $request->name,
                        'email' => $request->email,
                        'location' => $request->location,
                        'updated_at' => $currentTimestamp,
                        'picture_id' => $id,
                        'status' => $request->status
                    ]);
                }
                

                $notification = array(
                    'message' => 'Profile Updated Successfully',
                    'alert-type' => 'success'
                );
                return redirect()->route('client')->with($notification);
            }
        } else {
            if($profileData->email == $request->email){
                $request->validate([
                    'name' => 'required|max:200',
                ]);
            }else{
                $request->validate([
                    'name' => 'required|max:200',
                    'email' => 'required|unique:client',
                ]);
            }
            if($request->location == null){
                client::findOrFail($id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => '',
                    'updated_at' => $currentTimestamp,
                    'status' => $request->status
                ]);
            }else{
                client::findOrFail($id)->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => $request->location,
                    'updated_at' => $currentTimestamp,
                    'status' => $request->status
                ]);
            }

            $notification = array(
                'message' => 'Profile Updated Successfully',
                'alert-type' => 'success'
            );
            return redirect()->route('client')->with($notification);
        }
    }
}
