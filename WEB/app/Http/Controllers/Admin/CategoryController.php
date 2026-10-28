<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menuItems')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:51200',
        ], [
            'image_file.max' => 'The category image must not exceed 50MB.',
            'image_file.image' => 'The uploaded file must be a valid image format.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);
            $imagePath = 'uploads/categories/' . $filename;
        } elseif ($request->filled('image')) {
            $imagePath = $request->image;
        } else {
            $imagePath = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=600&auto=format&fit=crop&q=60';
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'image' => $imagePath,
            'description' => $request->description,
            'status' => $request->status ?: 'active'
        ]);

        return back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:51200',
        ], [
            'image_file.max' => 'The category image must not exceed 50MB.',
            'image_file.image' => 'The uploaded file must be a valid image format.',
        ]);

        $imagePath = $category->getRawOriginal('image');
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $filename);

            if ($imagePath && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }
            $imagePath = 'uploads/categories/' . $filename;
        } elseif ($request->filled('image')) {
            $imagePath = $request->image;
        }

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->status ?: $category->status,
        ]);

        return back()->with('success', 'Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $rawImage = $category->getRawOriginal('image');
        if ($rawImage && file_exists(public_path($rawImage))) {
            @unlink(public_path($rawImage));
        }
        $category->delete();
        return back()->with('success', 'Category deleted successfully!');
    }
}
