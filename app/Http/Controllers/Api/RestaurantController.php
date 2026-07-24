<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant; // 👈 Pastikan model Restaurant kamu sudah ada

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        // 🔍 Ambil kata kunci pencarian dari parameter '?search=' di Flutter
        $keyword = $request->query('search');

        // Jika user mengetik sesuatu, filter berdasarkan Nama ATAU Alamat
        $restaurants = Restaurant::when($keyword, function ($query) use ($keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%')
                         ->orWhere('address', 'like', '%' . $keyword . '%');
        })->get();

        return response()->json($restaurants, 200);
    }
}