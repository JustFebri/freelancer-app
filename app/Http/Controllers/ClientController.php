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
            ->select('picture.file', 'picture.filetype', 'client.client_id', 'client.name', 'client.email', 'client.location', 'client.created_at', 'client.updated_at', 'client.status')
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
            'password' => 'required',
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
                    'status' => 'active',
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
                    'status' => 'active',
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
                    'status' =>  'active',
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
                    'status' =>  'active',
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
}
