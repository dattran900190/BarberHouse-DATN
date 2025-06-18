<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Branch;
use App\Models\Review;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index(Request $request)
    {
        $query = Barber::with('branch')->where('status', 1);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', (float) $request->rating);
        }

        $barbers = $query->paginate(9);
        $branches = Branch::all();

        return view('client.listBarber', compact('barbers', 'branches'));
    }

    public function show($id)
    {
        $barber = Barber::with('branch')->findOrFail($id);

        // Lấy review hiển thị phân trang riêng
        $reviews = Review::with('user')
            ->where('barber_id', $id)
            ->where('is_visible', true)
            ->orderByDesc('created_at')
            ->paginate(5);

        return view('client.detailBarber', compact('barber', 'reviews'));
    }
}
