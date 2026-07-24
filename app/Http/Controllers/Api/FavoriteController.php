<?php

namespace App\Http\Controllers\API; // 🟢 FIX NAMESPACE

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    // 1. GET FAVORITES (Mengembalikan Array Langsung agar Flutter tidak crash)
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $favorites = Favorite::where('user_id', $userId)->get();

        return response()->json($favorites, 200);
    }

    // 2. SIMPAN / UPDATE FAVORITE (POST)
    public function store(Request $request)
    {
        $isFav = filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $favorite = Favorite::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'restaurant_id' => $request->restaurant_id,
            ],
            [
                'name' => $request->name ?? 'Restoran',
                'address' => $request->address ?? 'Alamat tidak tersedia',
                'rating' => $request->rating ?? 0.0,
                'image_url' => $request->image_url ?? '',
                'is_favorite' => $isFav,
                'note' => $request->note ?? '',
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil disimpan ke MySQL',
            'data' => $favorite
        ], 200);
    }

    // 3. UPDATE CATATAN (PUT /favorites/{id})
    public function update(Request $request, $id)
    {
        $favorite = Favorite::find($id);
        if ($favorite) {
            $favorite->note = $request->note ?? '';
            $favorite->save();
            return response()->json(['status' => 'success', 'data' => $favorite], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }

    // 4. HAPUS FAVORIT (DELETE /favorites/{id})
    public function destroy($id)
    {
        $favorite = Favorite::find($id);
        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'success', 'message' => 'Terhapus'], 200);
        }

        // Jaga-jaga jika ID yang dikirim Flutter adalah restaurant_id
        Favorite::where('restaurant_id', $id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Terhapus'], 200);
    }
}