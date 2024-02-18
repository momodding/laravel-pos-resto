<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        // fetch latest products paginated
        $products = Product::latest()->when($request->input('name'), function($users) use ($request) {
            $users = $users->where('name', 'like', '%'.$request->input('name').'%');
        })->paginate(10);
        return view('pages.products.index', compact('products'));
    }

    // create
    public function create()
    {
        $categories = Category::all();
        return view('pages.products.create', compact('categories'));
    }

    // store
    public function store(Request $request)
    {
        //validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        //store the product request
        $product = new Product;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->category_id = $request->category_id;

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $product->image = $name;
        }

        $product->save();

        //redirect to users.index
        return redirect()->route('products.index')->with('success', 'User created successfully');
    }

    // show
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('pages.products.show', compact('product'));
    }


    // edit
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('pages.products.edit', compact('product', 'categories'));
    }

    // update
    public function update(Request $request, $id)
    {
        //validate the request
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        //find the product
        $product = Product::findOrFail($id);

        //update the product
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->status = $request->status;
        $product->category_id = $request->category_id;

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);
            $product->image = $name;
        }

        $product->save();

        //redirect to products.index
        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    //destroy
    public function destroy($id)
    {
        //find the product
        $product = Product::findOrFail($id);

        //delete the product
        $product->delete();

        //redirect to products.index
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }


}
