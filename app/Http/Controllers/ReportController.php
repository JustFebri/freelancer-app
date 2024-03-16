<?php

namespace App\Http\Controllers;

use App\Models\report;
use App\Models\report_chats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function report()
    {
        $type = DB::table('report as r')
            ->leftJoin('user as u', 'u.user_id', '=', 'r.user_id')
            ->leftJoin('order as o', 'o.order_id', '=', 'r.order_id')
            ->leftJoin('picture as p', 'p.picture_id', '=', 'u.picture_id')
            ->select('r.report_id', 'r.order_id', 'r.report_type', 'r.user_id', 'r.description', 'r.status', 'r.created_at', 'r.updated_at', 'u.name', 'u.email', 'p.picasset')
            ->latest()
            ->get();

        foreach ($type as $item) {
            $item->message = report_chats::where('report_id', $item->report_id)->get();
        }

        return view('layouts.report', compact('type'));
    }

    public function sendMessage(Request $request)
    {
        Log::info('send');
        $user_id = Auth::id();

        $data = new report_chats;
        $data->admin_id = $user_id;
        $data->message = $request->message;
        $data->report_id = $request->report_id;
        $data->save();

        $updatedChat = report_chats::where('report_id', $request->report_id)->latest('created_at')->first();;

        return response()->json(['Success' => 'Message Sent', 'updatedReport' => $updatedChat], 200);
    }

    public function changeReportStatus(Request $request)
    {
        Log::info($request->all());
        $report = Report::find($request->report_id);

        if ($report) {
            $report->status = $request->status;
            $report->save();

            $notification = array(
                'message' => 'Report status updated successfully',
                'alert-type' => 'success'
            );
            return response()->json(['message' => 'Report status updated successfully']);
        } else {
            $notification = array(
                'message' => 'Report not found',
                'alert-type' => 'error'
            );
            return response()->json(['error' => 'Report not found'], 404);
        }
    }
}
