<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\TableBooking;

class BookingApiController extends Controller
{
    public function getTables()
    {
        $tables = Table::where('status', 'available')->get();
        return response()->json([
            'success' => true,
            'data' => $tables
        ]);
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string',
            'guests_count' => 'required|integer|min:1',
        ]);

        $booking = TableBooking::create([
            'user_id' => $request->user_id,
            'table_id' => $request->table_id ?: null,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'guests_count' => $request->guests_count,
            'special_request' => $request->special_request,
            'status' => 'pending'
        ]);

        $booking->load('table');

        return response()->json([
            'success' => true,
            'message' => 'Table reservation requested successfully',
            'data' => $booking
        ], 201);
    }

    public function getUserBookings($userId)
    {
        $bookings = TableBooking::with('table')->where('user_id', $userId)->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    public function cancelBooking($id)
    {
        $booking = TableBooking::find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Reservation cancelled'
        ]);
    }
}
