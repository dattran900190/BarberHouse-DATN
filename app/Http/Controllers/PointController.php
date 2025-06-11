<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class PointController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->with('pointHistories')->paginate(10);
        return view('admin.points.index', compact('users'));
    }
}