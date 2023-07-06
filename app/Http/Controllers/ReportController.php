<?php

namespace App\Http\Controllers;

use App\Models\report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function report()
    {
        $type = report::latest()->get();
        return view('layouts.report', compact('type'));
    }
}
