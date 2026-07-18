<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Mengambil semua data produk beserta nama kategorinya
        $products = Product::with('category')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}