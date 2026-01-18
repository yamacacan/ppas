<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\TaggingController;
use App\Http\Controllers\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    
// ============================================
// Kategori API'leri
// ============================================
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // Tüm kategorileri listele
    Route::get('/type/{type}', [CategoryController::class, 'byType']); // Tipine göre kategoriler
    Route::get('/{id}', [CategoryController::class, 'show']); // Kategori detayı
    Route::post('/', [CategoryController::class, 'store']); // Yeni kategori oluştur
    Route::put('/{id}', [CategoryController::class, 'update']); // Kategori güncelle
    Route::delete('/{id}', [CategoryController::class, 'destroy']); // Kategori sil
    Route::get('/{id}/children', [CategoryController::class, 'children']); // Alt kategorileri getir
});

// ============================================
// Keyword API'leri
// ============================================
Route::prefix('keywords')->group(function () {
    Route::get('/', [KeywordController::class, 'index']); // Tüm keyword'leri listele
    Route::get('/category/{id}', [KeywordController::class, 'byCategoryId']); // Kategoriye ait keyword'ler
    Route::post('/', [KeywordController::class, 'store']); // Yeni keyword ekle
    Route::put('/{id}', [KeywordController::class, 'update']); // Keyword güncelle
    Route::delete('/{id}', [KeywordController::class, 'destroy']); // Keyword sil
    Route::post('/bulk-import', [KeywordController::class, 'bulkImport']); // Toplu keyword import
    Route::post('/test', [KeywordController::class, 'test']); // Keyword test et
});

// ============================================
// Tagleme API'leri
// ============================================
Route::prefix('activities')->group(function () {
    Route::post('/{id}/tag', [TaggingController::class, 'tagManually']); // Manuel kategori tagle
    Route::delete('/{id}/tag/{categoryId}', [TaggingController::class, 'removeTag']); // Tag kaldır
    Route::post('/auto-tag', [TaggingController::class, 'autoTagAll']); // Tüm taglenmemiş aktiviteleri tagle
    Route::post('/auto-tag/{id}', [TaggingController::class, 'autoTagSingle']); // Belirli aktiviteyi tagle
    Route::get('/tagged', [TaggingController::class, 'getTagged']); // Taglenmiş aktiviteleri getir
    Route::get('/untagged', [TaggingController::class, 'getUntagged']); // Taglenmemiş aktiviteleri getir
    Route::get('/category/{categoryId}', [TaggingController::class, 'getByCategory']); // Kategoriye göre aktiviteler
});

// ============================================
// İstatistik API'leri
// ============================================
Route::prefix('statistics')->group(function () {
    Route::get('/categories', [StatisticsController::class, 'categories']); // Kategori istatistikleri
    Route::get('/category/{id}', [StatisticsController::class, 'category']); // Belirli kategori istatistikleri
    Route::get('/tagging-rate', [StatisticsController::class, 'taggingRate']); // Tagleme başarı oranı
    Route::get('/time-distribution', [StatisticsController::class, 'timeDistribution']); // Zaman dağılımı
    Route::get('/work-other-ratio', [StatisticsController::class, 'workOtherRatio']); // İş/Diğer oranı
});

});
