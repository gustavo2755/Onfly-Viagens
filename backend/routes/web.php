<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/login', fn () => redirect(config('app.frontend_url', 'http://localhost:5173') . '/login'))->name('login');
