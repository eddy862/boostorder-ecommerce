<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Events\ProductUpdated;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ]);

        $product = Product::create($validated);

        event(new ProductUpdated($product));

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ]);

        $product = Product::findOrFail($id);

        $product->update($validated);

        event(new ProductUpdated($product));

        return response()->json($product);
    }

    public function show($id) // get id from URL
    {
        $product = Product::findOrFail($id);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        event(new ProductUpdated($product));

        return response()->json(['message' => 'Deleted']);
    }
}
