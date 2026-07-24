<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\FavoriteController; // 🟢 FIX IMPORT NAMESPACE

// 1. REGISTER
Route::post('/register', function (Request $request) {
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6'
    ]);

    $user = User::create([
        'name' => explode('@', $request->email)[0],
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);

    $token = $user->createToken('eatfinder_token')->plainTextToken;

    return response()->json([
        'status' => 'success', 
        'access_token' => $token,
        'user' => $user
    ], 201);
});

// 2. LOGIN
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['status' => 'error', 'message' => 'Email atau Password salah'], 401);
    }

    $token = $user->createToken('eatfinder_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'message' => 'Login Berhasil',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ], 200);
});

// 3. ROUTE FAVORITES LENGKAP (GET, POST, PUT, DELETE)
Route::get('/favorites', [FavoriteController::class, 'index']);
Route::post('/favorites', [FavoriteController::class, 'store']);
Route::put('/favorites/{id}', [FavoriteController::class, 'update']);
Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);