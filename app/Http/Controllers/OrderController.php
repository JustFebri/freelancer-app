<?php

namespace App\Http\Controllers;

use App\Models\order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function order()
    {
        $type = order::latest()->get();
        return view('layouts.order', compact('type'));
    }
}
