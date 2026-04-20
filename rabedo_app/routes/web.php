<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/articles/{idOrSlug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/articles/{idOrSlug}/chapter-{chapterNumber}', [ArticleController::class, 'showChapter'])->name('articles.chapter');

Route::get('/proxy/image', [App\Http\Controllers\AiController::class, 'imageProxy'])->name('proxy.image');

// Static Pages
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('page.about');
Route::get('/privacy-policy', [App\Http\Controllers\PageController::class, 'privacy'])->name('page.privacy');
Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('page.contact');
Route::get('/disclaimer', [App\Http\Controllers\PageController::class, 'disclaimer'])->name('page.disclaimer');

// Admin Routes (protected by auth middleware and no-cache)
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'cache.headers:nocache,no_store,max_age=0,must_revalidate']], function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/editor', [AdminController::class, 'editor'])->name('admin.editor');
    // Save routes
    Route::post('/editor', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
    Route::get('/media', [AdminController::class, 'getMedia'])->name('admin.media');
    Route::post('/upload-image', [AdminController::class, 'uploadImage'])->name('admin.upload_image');
    
    // Chapter Management Routes
    Route::get('/articles/{articleId}/chapters/create', [App\Http\Controllers\AdminChapterController::class, 'create'])->name('admin.chapters.create');
    Route::post('/articles/{articleId}/chapters', [App\Http\Controllers\AdminChapterController::class, 'store'])->name('admin.chapters.store');
    Route::get('/chapters/{id}/edit', [App\Http\Controllers\AdminChapterController::class, 'edit'])->name('admin.chapters.edit');
    Route::put('/chapters/{id}', [App\Http\Controllers\AdminChapterController::class, 'update'])->name('admin.chapters.update');
    Route::delete('/chapters/{id}', [App\Http\Controllers\AdminChapterController::class, 'destroy'])->name('admin.chapters.destroy');
    // AI Route
    Route::get('/generate-ai', [App\Http\Controllers\AiController::class, 'generate'])->name('admin.generate_ai');
    
    // User Account Management
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\AdminUserController::class);
    });
});

// Since the user asked to use their existing users table, we just provide a basic manual login fallback
// If Breeze is installed later, it will override this, but for now we provide basic auth scaffolding
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// TEMPORARY ROUTE FOR CPANEL UPDATE (DELETE AFTER USE)
Route::get('/cpanel-update-database', function() {
    try {
        // 1. Chạy cập nhật tự động thêm 3 cột Database mới (migrate)
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        
        // 2. Dọn dẹp bộ nhớ đệm cũ (clear cache)
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        // 3. Đánh dấu tài khoản thành Admin vĩnh viễn
        \App\Models\User::where('email', 'superadmin@rabedo.com')->update(['is_admin' => true]);
        
        return "Tất cả mọi thứ đã được cập nhật thành công! Hãy QUAY LẠI FILE ROUTES VÀ XOÁ ĐOẠN CODE NÀY ĐI BẢO MẬT!";
    } catch (\Exception $e) {
        return "Gặp lỗi: " . $e->getMessage();
    }
});
