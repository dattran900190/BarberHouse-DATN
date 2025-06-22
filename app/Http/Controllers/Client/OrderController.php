<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Branch;
use App\Models\Review;
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
