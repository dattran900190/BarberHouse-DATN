<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
       

        return view('client.orderHistory');
    }

    public function show()
    {
        return view('client.detailOrderHistory');
        
    }
}
