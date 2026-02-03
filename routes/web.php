<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VeriffController;
use App\Http\Controllers\Front\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\VeriffWebhookController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\MyProfileController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Front\UploadDocumentController;
use App\Http\Controllers\Front\ScheduleMeetingController;

Auth::routes(['verify' => true]);
Route::get('/', [App\Http\Controllers\Front\FrontPagesController::class, 'index'])->name('fronthomepage');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'verified']);


// admin routes
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminController::class, 'Adminlogin'])->name('adminLogin');
    Route::post('post-login', [AdminController::class, 'VerifyAdminlogin'])->name('adminLoginpost');

    Route::get('/verify-two-factor', [AdminController::class, 'verifyTwoFactorForm'])->name('admin.verify.two.factor');
    Route::post('/verify-two-factor-post', [AdminController::class, 'verifyTwoFactor'])->name('verifyTwoFactPost');

    Route::get('/2fa/recover', [TwoFactorController::class, 'showRecoveryForm'])
        ->name('admin.2fa.recover');

    Route::post('/2fa/recover', [TwoFactorController::class, 'sendRecoveryLink'])
        ->name('admin.2fa.recover.send');

    Route::get('/2fa/reset/{token}', [TwoFactorController::class, 'resetTwoFactor'])
        ->name('admin.2fa.reset');
});

Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/account-dashboard', [MyProfileController::class, 'accountDashboard'])->name('account-dashboard');
    Route::get('/notarise-documents', [MyProfileController::class, 'notariseDocuments'])->name('notarise-documents');
    Route::get('/get-documents', [MyProfileController::class, 'getDocuments'])->name('get-documents');
    Route::post('/checkout', [MyProfileController::class, 'processCheckout'])->name('checkout');
    Route::get('/payment-success', [MyProfileController::class, 'paymentSuccess'])->name('payment-success');
    Route::get('/update-profile', [MyProfileController::class, 'updateUserProfileForm'])->name('update-profile.user-form');
    Route::post('/update-profile', [MyProfileController::class, 'updateUserProfile'])->name('update-profile');

    Route::match(['get', 'post'], '/2fa/generate', [UserController::class, 'generate'])
        ->name('2fa.generate');

    Route::get('/verify-two-factor', [UserController::class, 'verifyTwoFactorForm'])->name('verify.two.factor');

    Route::post('/2fa/verify', [UserController::class, 'verify'])
        ->name('2fa.verify');

    Route::get('/2fa/recover', [TwoFactorController::class, 'showRecoveryForm'])
        ->name('2fa.recover');

    Route::post('/2fa/recover', [TwoFactorController::class, 'sendRecoveryLink'])
        ->name('2fa.recover.send');

    Route::get('/2fa/reset/{token}', [TwoFactorController::class, 'resetTwoFactor'])
        ->name('2fa.reset');

    Route::get('/documents-list/{id}', [UploadDocumentController::class, 'documentList'])
        ->name('documentList');

    Route::get('/documents-list/{order_id}/{document_id}/{upload_document_id}', [UploadDocumentController::class, 'uploadDocument'])
        ->name('uploadDocument');

    Route::post('/upload-documents-store/{order_id}/{document_id}/{upload_document_id}', [UploadDocumentController::class, 'storeUploadDocument'])
        ->name('storeUploadDocument');

    Route::post('/delete-upload-document', [UploadDocumentController::class, 'deleteUploadDocument'])
        ->name('deleteUploadDocument');

    Route::post('/submit-document-for-verification', [UploadDocumentController::class, 'submitDocumentForVerification'])
        ->name('submitDocumentForVerification');


    Route::get('/schedule-meeting/{order_id}', [ScheduleMeetingController::class, 'scheduleMeetingForm'])
        ->name('scheduleMeetingForm');
    Route::post('/schedule-meeting', [ScheduleMeetingController::class, 'store'])->name('schedule.meeting.store');


    Route::get('/verification/{order_id}', [VeriffController::class, 'verificationPage'])
        ->name('verification.page');

    Route::post('/start-verification/{order_id}', [VeriffController::class, 'startVerification'])
        ->name('veriff.start');


});

// Route::post('/', [MyProfileController::class, 'updateProfile'])->name('admin.update.profile');

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

    Route::match(['get', 'post'], '/2fa/generate', [AdminController::class, 'generate'])
        ->name('admin.2fa.generate');

    Route::post('/2fa/verify', [AdminController::class, 'verify'])
        ->name('admin.2fa.verify');

    // orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders-detail/{id}', [App\Http\Controllers\Admin\OrderController::class, 'orderDetial'])->name('admin.orders.detail');
    Route::get('/orders-show/{id}', [App\Http\Controllers\Admin\OrderController::class, 'orderShow'])->name('admin.orders.show');
    Route::post('/change-doc-status/{id}', [App\Http\Controllers\Admin\OrderController::class, 'changeDocumentStatus'])->name('admin.change.doc.status');

    // testimonial
    Route::resource('testimonials', TestimonialController::class);
    Route::resource('notary-service-types', \App\Http\Controllers\Admin\NotaryServiceTypeController::class);
    Route::resource('documents', \App\Http\Controllers\Admin\DocumentController::class);
    Route::resource('upload-documents', \App\Http\Controllers\Admin\UploadDocumentsController::class);

    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings-store', [App\Http\Controllers\Admin\SettingsController::class, 'store'])->name('admin.settings.store');

    // schedule meeting
    Route::get('/schedule-meetings', [App\Http\Controllers\Admin\MeetingController::class, 'index'])->name('admin.schedule.meetings.index');
    Route::get('/schedule-meeting/{id}', [App\Http\Controllers\Admin\MeetingController::class, 'edit'])->name('admin.schedule.meetings.edit');
    Route::put('/schedule-meeting/{id}', [App\Http\Controllers\Admin\MeetingController::class, 'update'])->name('admin.schedule.meetings.update');
    Route::get('/calendar/events', [App\Http\Controllers\Admin\MeetingController::class, 'events'])->name('admin.calendar.events');

    Route::get('/google/auth', [SettingsController::class, 'redirectToGoogle'])
        ->name('admin.google.auth');

    // Route::get('/google/callback', [SettingsController::class, 'handleGoogleCallback'])
    //     ->name('admin.google.callback');
});


Route::get('/terminal-access', [App\Http\Controllers\Front\TermicalController::class, 'index']);
Route::post('/terminal', [App\Http\Controllers\Front\TermicalController::class, 'execute'])->name('terminal.execute');


Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);

Route::get('/google/callback', [SettingsController::class, 'googleCallback'])->name('google.callback');

 Route::any('/veriff/callback/{order_id}', [VeriffController::class, 'callback'])
        ->name('veriff.callback');

Route::post('/veriff/webhook', [VeriffWebhookController::class, 'handle'])
    ->name('veriff.webhook');
