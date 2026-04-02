<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return response()->json($cart);
    }

    public function add($id, $quantity)
    {
        $product = Product::findOrFail($id);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']+= $quantity;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "price" => $product->price,
                "quantity" => $quantity
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'message' => 'Product added to cart',
            'cart' => $cart
        ]);
    }


    public function update(Request $request, $id)
    {
        if (!$request->has('quantity') || $request->quantity < 1) {
            return response()->json(['error' => 'Invalid quantity'], 400);
        }
        
        $cart = session()->get('cart', []); 

        if (isset($cart[$id])) { 
            $cart[$id]['quantity'] = $request->quantity; 
            session()->put('cart', $cart);
        }

        return response()->json($cart);
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'message' => 'Product removed',
            'cart' => $cart
        ]);
    }
}
