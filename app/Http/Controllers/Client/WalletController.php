<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
       

        return view('client.wallet');
    }

    public function show()
    {
        return view('client.detailWallet');
    }

    public function withdrawal()
    {
        return view('client.withdrawal');
    }
}
