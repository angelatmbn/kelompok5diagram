<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< HEAD
use App\Http\Controllers\PDFController;

Route::get('/presensi/pdf', [PDFController::class, 'presensiPdf'])
    ->name('presensi.pdf');
=======

Route::get('/selamat', function () {
    return view('Selamat',['nama' => 'Joko Susilo']);
});

Route::get('/nama', function () {
    return view('nama',['nama' => 'Joko Susilo']);
});

Route::get('/tes', [App\Http\Controllers\ContohController::class, 'tes']);

Route::resource('coa',App\Http\Controllers\CoaController::class);
// untuk contoh perusahaan
use App\Http\Controllers\PerusahaanController;
Route::resource('perusahaan', PerusahaanController::class);
Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class,'destroy']);

Route::get('/', function () {
    // return view('welcome');
    // diarahkan ke login customer
    return view('login');
});


// login customer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class, 'daftarmenu'])
     ->middleware(\App\Http\Middleware\CustomerMiddleware::class)
     ->name('depan');
Route::get('/login', function () {
    return view('login');
});

// tambahan route untuk proses login
use Illuminate\Http\Request;
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class)
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware(\App\Http\Middleware\CustomerMiddleware::class)
;
// prosesubahpassword
// tambah keranjang
Route::post('/tambah', [App\Http\Controllers\KeranjangController::class, 'tambahKeranjang'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
Route::get('/lihatkeranjang', [App\Http\Controllers\KeranjangController::class, 'lihatkeranjang'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
Route::delete('/hapus/{menu_id}', [App\Http\Controllers\KeranjangController::class, 'hapus'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
Route::get('/lihatriwayat', [App\Http\Controllers\KeranjangController::class, 'lihatriwayat'])->middleware(\App\Http\Middleware\CustomerMiddleware::class);
// untuk autorefresh
Route::get('/cek_status_pembayaran_pg', [App\Http\Controllers\KeranjangController::class, 'cek_status_pembayaran_pg']);
Route::get('/login', function () {
    return view('login');
});

>>>>>>> 3f58c50 (menyelesaikan desain database, struktur migrasi serta trigger transaksi penjualan)
