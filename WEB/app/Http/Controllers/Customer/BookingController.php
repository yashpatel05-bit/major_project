<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TableBooking;
use App\Models\Table;

class BookingController extends Controller
{
    public function index()
    {
        $tables = Table::where('status', 'available')->get();
        $myBookings = TableBooking::with('table')->where('user_id', Auth::id())->latest()->get();
        return view('customer.booking', compact('tables', 'myBookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'guests_count' => 'required|integer|min:1|max:20',
        ]);

        TableBooking::create([
            'user_id' => Auth::id(),
            'table_id' => $request->table_id ?: null,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'guests_count' => $request->guests_count,
            'special_request' => $request->special_request,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Table reservation submitted successfully!');
    }
}
