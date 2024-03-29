<?php

namespace App\Http\Controllers;

use App\Models\freelancer;
use App\Models\order;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = user::count();
        $freelancer = freelancer::count();
        $order = order::where('order_status', 'completed')->count();

        return view('admin.index', compact('user', 'freelancer', 'order'));
    }
}
