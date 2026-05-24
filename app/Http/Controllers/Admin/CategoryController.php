<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'تم إضافة التصنيف');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $category->update($request->only(['name', 'name_ar', 'description', 'parent_id', 'is_active', 'sort_order']));

        return redirect()->route('admin.categories.index')->with('success', 'تم تحديث التصنيف');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'تم حذف التصنيف');
    }
}
