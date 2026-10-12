<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableBooking;
use App\Models\Table;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = TableBooking::with(['user', 'table'])->latest()->get();
        $tables = Table::all();
        return view('admin.bookings.index', compact('bookings', 'tables'));
    }

    public function update(Request $request, $id)
    {
        $booking = TableBooking::findOrFail($id);
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->status = $request->status;
        if ($request->has('table_id') && $request->table_id) {
            $booking->table_id = $request->table_id;
        }
        $booking->save();

        return back()->with('success', 'Table reservation updated successfully!');
    }
}
