<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.⚡home')->name('home');
Route::view('/projects', 'pages.⚡projects')->name('projects');
Route::view('/about', 'pages.⚡about')->name('about');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'pages.me.⚡dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
