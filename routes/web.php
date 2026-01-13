<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MyProfileController;
use App\Http\Controllers\Admin\TestimonialController;

Auth::routes(['verify' => true]);
Route::get('/', [App\Http\Controllers\Front\FrontPagesController::class, 'index'])->name('fronthomepage');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'verified']);


// admin routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminController::class, 'Adminlogin'])->name('adminLogin');
    Route::post('post-login', [AdminController::class, 'VerifyAdminlogin'])->name('adminLoginpost');
});

Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/account-dashboard', [MyProfileController::class, 'accountDashboard'])->name('account-dashboard');
    Route::get('/notarise-documents', [MyProfileController::class, 'notariseDocuments'])->name('notarise-documents');
    Route::get('/get-documents', [MyProfileController::class, 'getDocuments'])->name('get-documents');
    Route::post('/checkout', [MyProfileController::class, 'processCheckout'])->name('checkout');
    Route::get('/payment-success', [MyProfileController::class, 'paymentSuccess'])->name('payment-success');
});

Route::post('/', [MyProfileController::class, 'updateProfile'])->name('admin.update.profile');

// Route::get('logout', function () {
//     Auth::guard('admin')->logout();
//     return redirect()->to('admin/login');
// })->name('admin.logout');

Route::get('logout', function () {

    // If admin is logged in
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
        return redirect()->to('admin/login');
    }

    // If normal user is logged in
    if (Auth::check()) {
        Auth::logout();
        return redirect()->to('/');
    }

    return redirect()->to('/');
})->name('logout');

Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin']], function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // my proflie
    Route::get('/edit-profile', [MyProfileController::class, 'editProfile'])->name('admin.edit.profile');
    Route::post('/update-profile', [MyProfileController::class, 'updateProfile'])->name('admin.update.profile');

    // orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');

    // testimonial
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('notary-service-types', \App\Http\Controllers\Admin\NotaryServiceTypeController::class);
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);

    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings-store', [App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('admin.settings.store');
});


Route::get('/terminal-access', [App\Http\Controllers\Front\TermicalController::class, 'index']);
Route::post('/terminal', [App\Http\Controllers\Front\TermicalController::class, 'execute'])->name('terminal.execute');
