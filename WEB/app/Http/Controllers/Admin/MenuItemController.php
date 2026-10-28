<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Support\Str;

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
            'rating' => 'nullable|numeric|min:1|max:5',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:51200',
        ], [
            'image_file.max' => 'The menu dish image must not exceed 50MB.',
            'image_file.image' => 'The uploaded file must be a valid image format.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/menu');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/menu/' . $filename;
        } elseif ($request->filled('image')) {
            $imagePath = $request->image;
        } else {
            $imagePath = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=60';
        }

        MenuItem::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? true : false,
            'is_veg' => $request->has('is_veg') ? true : false,
            'rating' => $request->rating ?: 4.8
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
            'rating' => 'nullable|numeric|min:1|max:5',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:51200',
        ], [
            'image_file.max' => 'The menu dish image must not exceed 50MB.',
            'image_file.image' => 'The uploaded file must be a valid image format.',
        ]);

        $imagePath = $item->getRawOriginal('image');
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/menu');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);

            if ($imagePath && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }
            $imagePath = 'uploads/menu/' . $filename;
        } elseif ($request->filled('image')) {
            $imagePath = $request->image;
        }

        $item->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available') ? true : false,
            'is_veg' => $request->has('is_veg') ? true : false,
            'rating' => $request->rating ?: $item->rating
        ]);

        return back()->with('success', 'Menu item updated successfully!');
    }

    public function destroy($id)
    {
        $item = MenuItem::findOrFail($id);
        $rawImage = $item->getRawOriginal('image');
        if ($rawImage && file_exists(public_path($rawImage))) {
            @unlink(public_path($rawImage));
        }
        $item->delete();
        return back()->with('success', 'Menu item removed successfully!');
    }
}
