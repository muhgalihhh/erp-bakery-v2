<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Standalone POS route (decoupled from Filament)
// Redirect to Filament admin login if not authenticated
Route::get('/pos', function () {
    // Check if user is authenticated
    if (!Auth::check()) {
        return redirect('/admin/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses POS.');
    }

    // Check permission using Filament Shield/Spatie Permission
    $user = Auth::user();
    if (!$user->can('view_pos')) {
        abort(403, 'Anda tidak memiliki akses ke halaman POS.');
    }

    return view('pos.index');
})->name('pos.index');
