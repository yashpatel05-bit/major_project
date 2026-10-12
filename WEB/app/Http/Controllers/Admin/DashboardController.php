<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\TableBooking;
use App\Models\DeliveryPerson;
use App\Models\MenuItem;
use App\Models\Feedback;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $activeBookings = TableBooking::whereIn('status', ['pending', 'confirmed'])->count();
        $activeDeliveryStaff = DeliveryPerson::where('status', 'on_delivery')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalMenuItems = MenuItem::count();

        $recentOrders = Order::with(['user', 'deliveryPerson'])->latest()->take(6)->get();
        $recentBookings = TableBooking::with(['user', 'table'])->latest()->take(5)->get();
        $recentFeedbacks = Feedback::with('user')->latest()->take(4)->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'activeBookings',
            'activeDeliveryStaff',
            'totalCustomers',
            'totalMenuItems',
            'recentOrders',
            'recentBookings',
            'recentFeedbacks'
        ));
    }
}
