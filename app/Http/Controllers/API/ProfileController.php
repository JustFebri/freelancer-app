<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChangeTypeController;
use App\Http\Requests\API\IssueRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Models\freelancer;
use App\Models\picture;
use App\Models\user;
use App\Models\report;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function getReq()
    {
        $currentUserId = auth()->id();
        $record = freelancer::where('user_id', '=', $currentUserId)->first();
        if ($record == null) {
            return response([
                'message' => 'No record found',
            ], 404);
        } else if ($record->IsApproved == "pending") {
            return response([
                'message' => 'Record found',
            ], 201);
        } else if ($record->IsApproved == "rejected") {
            return response([
                'message' => 'Record Rejected',
            ], 422);
        }
    }

    public function getUserType()
    {
        $currentUserId = auth()->id();
        $record = user::where('user_id', '=', $currentUserId)->first();
        Log::info($record);
        if ($record->profile_type == 'freelancer') {
            Log::info('1');
            return response([
                'message' => 'User is freelancer',
            ], 201);
        } else {
            Log::info('2');
            return response([
                'message' => 'User is not freelancer',
            ], 404); // Or any appropriate status code like 404 Not Found
        }
    }

    public function sendIssue(IssueRequest $request)
    {

        $request->validated();
        $currentUserId = auth()->id();
        $report = report::create([
            'user_id' => $currentUserId,
            'report_type' => 'App Report',
            'description' => $request->issue,
            'subject' => $request->subject,
        ]);

        return response([
            'message' => 'Your issue has been successfully sent to the admin for review. We appreciate your feedback and will address the matter as soon as possible',
            'report' => $report,
        ], 200);
    }

    public function changeUserData(Request $request)
    {
        $currentUserId = auth()->id();
        if ($currentUserId) {
            $user = user::find($currentUserId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return response([
                'message' => 'User Data Updated',
                'userData' => $user,
            ], 200);
        }
    }

    public function changePassword(Request $request)
    {
        $currentUserId = auth()->id();

        $user = user::find($currentUserId);
        if (!Hash::check($request->current_password, $user->password)) {
            return response([
                'message' => 'Current password is incorrect',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response([
            'message' => 'Password changed successfully',
        ], 200);
    }

    public function ticketList(Request $request)
    {
        $currentUserId = auth()->id();
        $data = report::where('user_id', $currentUserId)->get();
        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function changeProfilePicture(Request $request)
    {
        $currentUserId = auth()->id();
        $user = user::find($currentUserId);

        if (!empty($user->picture_id)) {
            $data = user::join('picture', 'user.picture_id', '=', 'picture.picture_id')
                ->select('user.picture_id', 'picture.picasset', 'user.name', 'user.email', 'user.created_at', 'user.updated_at')
                ->find($currentUserId);

            $filename = basename($data->picasset);
            Storage::delete('public/images/' . $filename);

            $file = $request->file('profilePicture');
            $file->store('public/images');
            $filename = $request->file('profilePicture')->hashName();
            $path = 'public/images/' . $filename;
            $url = asset(Storage::url($path));

            $picData = picture::findOrFail($data->picture_id)->update([
                'piclink' => $url,
                'picasset' => Storage::url($path),
            ]);

            $user->save();

            return response()->json([
                'message' => 'Success changing profile picture',
                'picture' => Storage::url($path),
            ], 200);
        } else {
            Log::info($request);
            $request->file('profilePicture')->store('public/images');
            $filename = $request->file('profilePicture')->hashName();
            $path = 'public/images/' . $filename;
            $url = asset(Storage::url($path));

            $newPicture = new picture;
            $newPicture->piclink = $url;
            $newPicture->picasset = Storage::url($path);
            $newPicture->save();

            $user->picture_id = $newPicture->picture_id;
            $user->save();

            return response()->json([
                'message' => 'Success changing profile picture',
                'picture' =>  $newPicture->picasset,
            ], 200);
        }
    }

    public function getBalance()
    {
        $currentUserId = auth()->id();
        $user = user::find($currentUserId);

        return response()->json([
            'balance' => abs($user->balance),
        ], 200);
    }
}
