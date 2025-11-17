<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // نمایش همه دسته‌ها
    public function index()
    {
        $categories = Category::orderBy('position')->get();
        return response()->json($categories);
    }

    // ایجاد دسته جدید
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'position' => $validated['position'] ?? 0,
            'is_visible' => $validated['is_visible'] ?? false,
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    // نمایش یک دسته
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // بروزرسانی دسته
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
        ]);

        if (isset($validated['name'])) {
            $category->name = $validated['name'];
            $category->slug = Str::slug($validated['name']);
        }

        $category->parent_id = $validated['parent_id'] ?? $category->parent_id;
        $category->description = $validated['description'] ?? $category->description;
        $category->position = $validated['position'] ?? $category->position;
        $category->is_visible = $validated['is_visible'] ?? $category->is_visible;
        $category->seo_title = $validated['seo_title'] ?? $category->seo_title;
        $category->seo_description = $validated['seo_description'] ?? $category->seo_description;

        $category->save();

        return response()->json($category);
    }

    // حذف دسته
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
