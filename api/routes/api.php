<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Email hoặc mật khẩu không chính xác.'], 401);
    }

    return response()->json([
        'token' => $user->createToken('admin-token')->plainTextToken,
        'user' => $user
    ]);
});

use App\Http\Controllers\ArticleController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout
    Route::post('/logout', function (Request $request) {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Đăng xuất thành công']);
    });

    Route::post('/upload-image', [ArticleController::class, 'uploadImage']);

    // Admin Article Management
    Route::apiResource('articles', ArticleController::class);
});

// Cho phép public lấy danh sách bài viết (Hiển thị ra trang cho khách)
Route::get('/public/articles', [ArticleController::class, 'index']);
Route::get('/public/articles/{article:slug}', [ArticleController::class, 'show']);
