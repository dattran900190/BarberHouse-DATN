<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class ClientBranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('client.branch', compact('branches'));
    }
    public function detail($id)
    {
        $branch = Branch::findOrFail($id);
        return view('client.detailBranch', compact('branch'));
    }
}
