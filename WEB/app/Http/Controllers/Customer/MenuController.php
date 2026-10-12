<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MenuItem;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', 'active')->get();
        $query = MenuItem::where('is_available', true);

        if ($request->has('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('type') && $request->type == 'veg') {
            $query->where('is_veg', true);
        } elseif ($request->has('type') && $request->type == 'nonveg') {
            $query->where('is_veg', false);
        }

        $menuItems = $query->latest()->get();

        return view('customer.menu', compact('categories', 'menuItems'));
    }
}
