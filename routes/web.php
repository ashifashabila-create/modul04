<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Models\Book;


// ================== AUTH (BELUM LOGIN) ==================
Route::get('/', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);

// REGISTER
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ================== HARUS LOGIN ==================
Route::middleware('auth')->group(function () {

    Route::get('/home', function () {
        $books = Book::all();
        return view('home', compact('books'));
    })->name('home');

    Route::resource('books', BookController::class);

});