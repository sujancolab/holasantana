<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.store');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/media/upload-image', [PageController::class, 'uploadImage'])->name('media.upload-image');
    Route::resource('languages', LanguageController::class)->except(['show', 'create']);
    Route::resource('pages', PageController::class)->except(['show']);
});

Route::get('/{locale}/{slug?}', [PublicPageController::class, 'show'])->where('slug', '.*')->name('pages.show');
