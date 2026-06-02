<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()->categories()->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
        ]);
        $request->user()->categories()->create($validated);
        return redirect()->route('categories.index')->with('success', 'Category added.');
    }

    public function edit(Category $category)
    {
        if ($category->user_id !== auth()->id()) abort(403);
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== auth()->id()) abort(403);
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
        ]);
        $category->update($validated);
        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) abort(403);
        try {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category deleted.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('categories.index')->with('error', 'Category cannot be deleted because it is still used in transactions.');
        }
    }
}
