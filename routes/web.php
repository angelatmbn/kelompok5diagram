<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// =====================
// View Default & Login
// =====================

Route::get('/', function () {
    return view('/login'); // default diarahkan ke login customer
});

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// =====================
// View Tes dan Nama
// =====================

Route::get('/selamat', function () {
    return view('Selamat', ['nama' => 'Joko Susilo']);
});

Route::get('/nama', function () {
    return view('nama', ['nama' => 'Joko Susilo']);
});

Route::get('/tes', [App\Http\Controllers\AuthController::class, 'tes']);

// =====================
// PDF Presensi
// =====================

use App\Http\Controllers\PDFController;
Route::get('/presensi/pdf', [PDFController::class, 'presensiPdf'])->name('presensi.pdf');

// =====================
// Resource Routes
// =====================

Route::resource('coa', App\Http\Controllers\CoaController::class);

// =====================
// Customer Area (with Middleware)
// =====================

use App\Http\Middleware\CustomerMiddleware;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\AuthController;

Route::middleware([CustomerMiddleware::class])->group(function () {
    Route::get('/depan', [KeranjangController::class, 'daftarmenu'])->name('depan');
    Route::get('/ubahpassword', [AuthController::class, 'ubahpassword'])->name('ubahpassword');
    Route::post('/prosesubahpassword', [AuthController::class, 'prosesubahpassword']);

    Route::post('/tambah', [KeranjangController::class, 'tambahKeranjang']);
    Route::get('/lihatkeranjang', [KeranjangController::class, 'lihatkeranjang']);
    Route::delete('/hapus/{menu_id}', [KeranjangController::class, 'hapus']);
    Route::get('/lihatriwayat', [KeranjangController::class, 'lihatriwayat']);
});

// =====================
// Autorefresh / Cek Status
// =====================

Route::get('/cek_status_pembayaran_pg', [KeranjangController::class, 'cek_status_pembayaran_pg']);
Route::post('/keranjang/tambah', [KeranjangController::class, 'tambahKeKeranjang']);

Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/keranjang/clear', [KeranjangController::class, 'clearCart']);

Route::get('/bayar/sukses', [KeranjangController::class, 'bayarSukses']);

Route::post('/midtrans/callback', [KeranjangController::class, 'midtransCallback']);

Route::post('/pembayaran/sukses', [PembayaranController::class, 'simpanDariSnap']);

Route::post('/pembayaran/simpan', [PembayaranController::class, 'simpan']);
