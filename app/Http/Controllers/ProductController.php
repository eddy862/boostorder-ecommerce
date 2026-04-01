<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function show($id) // get id from URL
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }
}
