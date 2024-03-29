<?php

namespace App\Http\Controllers\API;

use App\Events\updateListTicket;
use App\Events\updateTicketChat;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ChangeTypeController;
use App\Http\Requests\API\IssueRequest;
use App\Http\Requests\API\PasswordRequest;
use App\Http\Requests\API\ProfilePicRequest;
use App\Http\Requests\API\TicketMessageRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Http\Requests\API\WithdrawRequest;
use App\Models\freelancer;
use App\Models\picture;
use App\Models\user;
use App\Models\report;
use App\Models\report_chats;
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
        if ($record->profile_type == 'freelancer') {
            return response([
                'message' => 'User is freelancer',
                'user' => $record,
            ], 201);
        } else {
            return response([
                'message' => 'User is not freelancer',
                'user' => $record,
            ], 404);
        }
    }

    public function sendIssue(IssueRequest $request)
    {

        $currentUserId = auth()->id();

        $reportData = [
            'user_id' => $currentUserId,
            'description' => $request->issue,
            'subject' => $request->subject,
            'admin_id' => 1,
        ];

        if ($request->order_id != null) {
            $reportData['order_id'] = $request->order_id;
            $reportData['report_type'] = 'Order Report';
        } else {
            $reportData['report_type'] = 'App Report';
        }

        $report = report::create($reportData);

        return response([
            'message' => 'Your issue has been successfully sent to the admin for review. We appreciate your feedback and will address the matter as soon as possible',
            'report' => $report,
        ], 200);
    }

    public function ticketList()
    {
        $currentUserId = auth()->id();
        $data = report::where('user_id', $currentUserId)->get();
        foreach ($data as $item) {
            $item->lastMessage = report_chats::where('user_id', $currentUserId)
                ->where('report_id', $item->report_id)
                ->orderBy('updated_at', 'DESC')
                ->pluck('message')
                ->first();
        }

        Log::info($data);

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function getTicketMessage(string $report_id)
    {
        $message = report_chats::where('report_id', $report_id)->get();

        Log::info($message);
        return response()->json([
            'data' => $message,
        ], 200);
    }

    public function sendTicketMessage(TicketMessageRequest $request)
    {
        $currentUserId = auth()->id();
        $message = new report_chats;
        $message->report_id = $request->report_id;
        $message->user_id = $currentUserId;
        $message->message = $request->message;
        $message->save();

        broadcast(new updateTicketChat($message))->toOthers();
        broadcast(new updateListTicket('1'))->toOthers();

        return response()->json([
            'data' => $message,
        ], 200);
    }

    public function changeUserData(Request $request)
    {
        $currentUserId = auth()->id();
        $profileData = user::find($currentUserId);

        if ($profileData->email != $request->email) {
            $request->validate([
                'email' => 'required|email|unique:user',
                'name' => 'required|string|max:255',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
        }

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

    public function changePassword(PasswordRequest $request)
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

    public function changeProfilePicture(ProfilePicRequest $request)
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

    public function getTransaction()
    {
        $currentUserId = auth()->id();
        $data = DB::table('transactions as t')
            ->where('t.user_id', $currentUserId)
            ->leftJoin('order as o', 'o.order_id', 't.order_id')
            ->orderBy('t.updated_at', 'desc')
            ->select('t.type', 't.amount')
            ->where(function ($query) {
                $query->where('t.type', 'balance_withdraw')
                    ->orWhere('t.type', 'client_payment_balance');
            })
            ->get();

        LOg::info($data);

        return response()->json([
            'data' => $data,
        ], 200);
    }

    public function withdrawBalance(WithdrawRequest $request)
    {
        $currentUserId = auth()->id();
        $user = user::find($currentUserId);

        if ($user->balance < $request->amount) {
            return response()->json([
                'message' => 'Balance not enough',
            ], 400);
        }

        $user->balance = $user->balance - $request->amount;
        $user->save();

        $transaction = new transactions;
        $transaction->user_id = $currentUserId;
        $transaction->amount = $request->amount;
        $transaction->type = 'balance_withdraw';
        $transaction->description = 'withdraw balance for user_id ' . $currentUserId;
        $transaction->save();

        return response()->json([
            'message' => 'Balance Withdrawed',
        ], 200);
    }
}
