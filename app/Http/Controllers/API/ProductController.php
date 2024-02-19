<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    //index product api
    public function index()
    {
        //get latest 10 products and response paginate api
        $products = Product::latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }
}
