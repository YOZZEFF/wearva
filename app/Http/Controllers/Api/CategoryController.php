<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

     $categories = Category::with('subCategories')
                    ->whereNull('parent_id')
                    ->get();

    return response()->json([
        'status' => true,
        'data'   => $categories
    ]);


    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        // name
        'name' => 'required|string|max:255|unique:categories,name',

        // slug (اختياري - ممكن يتبعت أو يتولد)
        'slug' => 'nullable|string|max:255|unique:categories,slug',

        // image
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

        // parent_id
        'parent_id' => 'nullable|integer|exists:categories,id',
    ]);

    $slug = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['name']);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('categories', 'public');
    }

    $category = Category::create([
        'name' => $validated['name'],
        'slug' => $slug,
        'image' => $imagePath,
        'parent_id' => $validated['parent_id'] ?? null,
    ]);

    return response()->json([
        'message' => 'Category created successfully',
        'data' => $category
    ], 201);
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
           $validated = $request->validate([
        'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'parent_id' => 'nullable|integer|exists:categories,id',
    ]);

    $slug = $validated['slug'] ?? Str::slug($validated['name']);

    
    if ($request->hasFile('image')) {


        $imagePath = $request->file('image')->store('categories', 'public');
        $category->image = $imagePath;
    }

    // 4. Update باقي البيانات
    $category->update([
        'name' => $validated['name'],
        'slug' => $slug,
        'parent_id' => $validated['parent_id'] ?? null,
    ]);

    // 5. Response
    return response()->json([
        'message' => 'Category updated successfully',
        'data' => $category
    ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
         $category->delete();

    return response()->json([
        'message' => 'Category deleted successfully'
    ], 200);
    }
}
