<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //index category api
    public function index()
    {
        //get all categories and response api
        $categories = Category::latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }
}
