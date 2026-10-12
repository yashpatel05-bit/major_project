<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Category;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with('category')->latest()->get();
        $categories = Category::where('status', 'active')->get();
        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        MenuItem::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image ?: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=60',
            'is_available' => $request->has('is_available') ? true : false,
            'is_veg' => $request->has('is_veg') ? true : false,
            'rating' => $request->rating ?: 4.5
        ]);

        return back()->with('success', 'Menu item added successfully!');
    }

    public function update(Request $request, $id)
    {
        $item = MenuItem::findOrFail($id);
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
        ]);

        $item->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->image ?: $item->image,
            'is_available' => $request->has('is_available') ? true : false,
            'is_veg' => $request->has('is_veg') ? true : false,
            'rating' => $request->rating ?: $item->rating
        ]);

        return back()->with('success', 'Menu item updated successfully!');
    }

    public function destroy($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Menu item removed successfully!');
    }
}
