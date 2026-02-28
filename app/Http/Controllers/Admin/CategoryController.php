<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->paginate(15);

        return View::make('admin.categories.index', ['categories' => $categories]);
    }

    public function create()
    {
        return View::make('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return Redirect::route('admin.categories.index')->with('success', 'Category created successfully');
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return Redirect::route('admin.categories.index')->with('error', 'Category not found');
        }

        return View::make('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return Redirect::route('admin.categories.index')->with('error', 'Category not found');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $category->name = $validated['name'];
        $category->description = $validated['description'] ?? null;
        $category->save();

        return Redirect::route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return Redirect::route('admin.categories.index')->with('error', 'Category not found');
        }

        $category->delete();

        return Redirect::route('admin.categories.index')->with('success', 'Category deleted successfully');
    }
}
