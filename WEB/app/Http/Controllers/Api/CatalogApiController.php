<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\MenuItem;

class CatalogApiController extends Controller
{
    public function getCategories()
    {
        $categories = Category::where('status', 'active')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function getMenuItems(Request $request)
    {
        $query = MenuItem::with('category')->where('is_available', true);

        if ($request->has('category_id') && $request->category_id > 0) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('is_veg')) {
            $query->where('is_veg', filter_var($request->is_veg, FILTER_VALIDATE_BOOLEAN));
        }

        $items = $query->orderBy('rating', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function getMenuItem($id)
    {
        $item = MenuItem::with('category')->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $item]);
    }
}
