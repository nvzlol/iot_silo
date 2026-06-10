<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Models\SensorData;

// halaman login
Route::get('/', function () {
    return view('login');
});

// Proses login
Route::post('/login', function (Request $request) {
    if ($request->username == 'admin' && $request->password == '123') {
        session(['login' => true]);
        return redirect('/dashboard');
    }
    return back()->with('error', 'Username atau password salah');
});

// dashboard (protected TANPA middleware)
Route::get('/dashboard', function () {
    if (!session('login')) {
        return redirect('/');
    }

    return app(DashboardController::class)->index();
});

// Kontrol servo dari dashboard
Route::post('/servo/command', function (Request $request) {
    if (!session('login')) return response()->json(['error' => 'Unauthorized'], 401);
    return app(DashboardController::class)->setServoCommand($request);
});

// logout
Route::get('/logout', function () {
    session()->forget('login');
    return redirect('/');
});
