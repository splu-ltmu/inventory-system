<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class ClientDashboardController extends Controller
{
    public function index()
    {
        return view('client.dashboard');
    }
}
