<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\HolidayHomeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\PropertyReservationController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Owner\AuthController as OwnerAuthController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\ReservationController as OwnerReservationController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response('ok'));
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');

    return response('Application cache cleared.');
})->name('cache.clear');

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');
Route::post('/service-enquiries', [PublicPageController::class, 'storeServiceEnquiry'])->name('service-enquiries.store');
Route::post('/submit-query', [PublicPageController::class, 'storeSubmitQuery'])->name('submit-query.store');

Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.store');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::get('/owner/login', [OwnerAuthController::class, 'showLogin'])->name('owner.login');
Route::post('/owner/login', [OwnerAuthController::class, 'login'])->name('owner.login.store');
Route::post('/owner/logout', [OwnerAuthController::class, 'logout'])->name('owner.logout');
Route::middleware('owner')->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', OwnerDashboardController::class)->name('dashboard');
    Route::resource('reservations', OwnerReservationController::class)
        ->except(['index', 'show'])
        ->parameters(['reservations' => 'reservation']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/test-mail', function (Request $request) {
        $to = $request->query('to', config('mail.from.address'));

        validator(['to' => $to], [
            'to' => ['required', 'email'],
        ])->validate();

        Mail::raw('This is a test email from Hola Santana sent at '.now()->toDateTimeString().'.', function ($mail) use ($to) {
            $mail->to($to)->subject('Hola Santana test email');
        });

        return response()->json([
            'sent' => true,
            'to' => $to,
            'mailer' => config('mail.default'),
        ]);
    })->name('test-mail');
    Route::get('/footer-settings', [FooterSettingController::class, 'edit'])->name('footer-settings.edit');
    Route::put('/footer-settings', [FooterSettingController::class, 'update'])->name('footer-settings.update');
    Route::post('/media/upload-image', [PageController::class, 'uploadImage'])->name('media.upload-image');
    Route::post('/translations/settings', [TranslationController::class, 'updateSettings'])->name('translations.settings');
    Route::post('/translations/translate', [TranslationController::class, 'translate'])->name('translations.translate');
    Route::post('/translations/translate-block', [TranslationController::class, 'translateBlock'])->name('translations.translate-block');
    Route::resource('owners', OwnerController::class)->except(['show']);
    Route::resource('properties', PropertyController::class)->except(['show']);
    Route::resource('reservations', PropertyReservationController::class)->except(['show'])->parameters(['reservations' => 'reservation']);
    Route::resource('activities', ActivityController::class)->except(['show']);
    Route::resource('holiday-homes', HolidayHomeController::class)
        ->except(['show'])
        ->parameters(['holiday-homes' => 'holidayHome']);
    Route::resource('languages', LanguageController::class)->except(['show', 'create']);
    Route::resource('pages', PageController::class)->except(['show']);
});

Route::get('/{locale}/{slug?}', [PublicPageController::class, 'show'])->where('slug', '.*')->name('pages.show');
