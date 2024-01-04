<?php

namespace App\Http\Controllers;

use App\Models\report;
use Illuminate\Http\Request;
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

        return view('layouts.report', compact('type'));
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
