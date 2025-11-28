<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes(['verify' => true]);

Route::get('/', [App\Http\Controllers\Front\FrontPagesController::class, 'index'])->name('fronthomepage');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('home');



Route::get('/terminal-access', [App\Http\Controllers\Front\TermicalController::class, 'index']);
Route::post('/terminal', [App\Http\Controllers\Front\TermicalController::class, 'execute'])->name('terminal.execute');
