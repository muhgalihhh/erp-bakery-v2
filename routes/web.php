<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Standalone POS route (decoupled from Filament)
// This is an alternative to accessing POS via Filament admin panel
Route::get('/pos', function () {
    // Check if user is authenticated
    if (!Auth::check()) {
        return redirect('/admin/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses POS.');
    }

    // Check permission using Filament Shield/Spatie Permission
    $user = Auth::user();

    // Check either page_Pos or view_pos permission
    if (!$user->can('page_Pos') && !$user->can('view_pos')) {
        abort(403, 'Anda tidak memiliki akses ke halaman POS.');
    }

    return view('pos.index');
})->name('pos.index');

// POS Fullscreen mode (without Filament wrapper)
Route::get('/pos/fullscreen', function () {
    if (!Auth::check()) {
        return redirect('/admin/login');
    }

    $user = Auth::user();
    if (!$user->can('page_Pos') && !$user->can('view_pos')) {
        abort(403);
    }

    return view('pos.index');
})->name('pos.fullscreen');
