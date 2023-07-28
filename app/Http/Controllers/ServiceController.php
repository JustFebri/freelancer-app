<?php

namespace App\Http\Controllers;

use App\Models\service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function service()
    {
        

     
        return view('layouts.service');
    }
}
