<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/articles/{idOrSlug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/proxy/image', [App\Http\Controllers\AiController::class, 'imageProxy'])->name('proxy.image');

// Static Pages
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('page.about');
Route::get('/privacy-policy', [App\Http\Controllers\PageController::class, 'privacy'])->name('page.privacy');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('page.contact');
Route::get('/disclaimer', [App\Http\Controllers\PageController::class, 'disclaimer'])->name('page.disclaimer');

// Admin Routes (protected by auth middleware)
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/editor', [AdminController::class, 'editor'])->name('admin.editor');
    // Save routes
    Route::post('/editor', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
    Route::post('/upload-image', [AdminController::class, 'uploadImage'])->name('admin.upload_image');
    
    // AI Route
    Route::get('/generate-ai', [App\Http\Controllers\AiController::class, 'generate'])->name('admin.generate_ai');
});

// Since the user asked to use their existing users table, we just provide a basic manual login fallback
// If Breeze is installed later, it will override this, but for now we provide basic auth scaffolding
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
