<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;



Auth::routes(['verify' => true]);
Route::get('/', [App\Http\Controllers\Front\FrontPagesController::class, 'index'])->name('fronthomepage');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');


// admin routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminController::class, 'Adminlogin'])->name('adminLogin');
    Route::post('post-login', [AdminController::class, 'VerifyAdminlogin'])->name('adminLoginpost');
});

Route::get('logout', function () {
    Auth::guard('admin')->logout();
    return redirect()->to('admin/login');
})->name('admin.logout');

Route::group(['prefix' => 'admin','middleware' => ['auth:admin']], function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});


Route::get('/terminal-access', [App\Http\Controllers\Front\TermicalController::class, 'index']);
Route::post('/terminal', [App\Http\Controllers\Front\TermicalController::class, 'execute'])->name('terminal.execute');
