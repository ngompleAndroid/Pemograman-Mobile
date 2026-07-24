<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Exception;

class FavoriteController extends Controller
{
    // 1. GET FAVORITES
    public function index(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            $favorites = Favorite::where('user_id', $userId)->get();

            // Mengembalikan list/array langsung agar cocok dengan Flutter
            return response()->json($favorites, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2. STORE / POST FAVORITE (DILENGKAPI CATCH DATABASE ERROR)
    public function store(Request $request)
    {
        try {
            // Tangani is_favorite agar tersimpan 1 atau 0 di MySQL
            $isFav = filter_var($request->is_favorite, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

            $favorite = Favorite::updateOrCreate(
                [
                    'user_id' => (string) $request->user_id,
                    'restaurant_id' => (string) $request->restaurant_id,
                ],
                [
                    'name' => $request->name ?? 'Restoran',
                    'address' => $request->address ?? '',
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

        } catch (Exception $e) {
            // 🟢 MENAMPILKAN DETAIL ERROR MYSQL JIKA SIMPAN GAGAL
            return response()->json([
                'status' => 'error',
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. PUT UPDATE NOTE
    public function update(Request $request, $id)
    {
        try {
            $favorite = Favorite::find($id);
            if (!$favorite) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
            }

            $favorite->note = $request->note ?? '';
            $favorite->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Catatan berhasil diperbarui',
                'data' => $favorite
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error Update: ' . $e->getMessage()
            ], 500);
        }
    }

    // 4. DELETE FAVORITE
    public function destroy($id)
    {
        try {
            $favorite = Favorite::find($id);
            if ($favorite) {
                $favorite->delete();
                return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus'], 200);
            }

            // Fallback jika ID yang dikirim Flutter adalah restaurant_id
            Favorite::where('restaurant_id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus'], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error Delete: ' . $e->getMessage()
            ], 500);
        }
    }
}