<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //index
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('pages.category.index', compact('categories'));
    }

    //create
    public function create()
    {
        return view('pages.category.create');
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // store request to database
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->slug = Str::slug($request->name);
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    //edit
    public function edit(Category $category)
    {
        return view('pages.category.edit', compact('category'));
    }

    //update
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        // update request to database
        $category->fill($request->all());
        $category->slug = Str::slug($request->name);
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    //destroy
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
