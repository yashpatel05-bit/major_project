<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Feedback;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'active')->take(6)->get();
        $popularItems = MenuItem::where('is_available', true)->orderBy('rating', 'desc')->take(8)->get();
        $feedbacks = Feedback::with('user')->where('rating', '>=', 4)->latest()->take(3)->get();

        return view('customer.home', compact('categories', 'popularItems', 'feedbacks'));
    }
}
