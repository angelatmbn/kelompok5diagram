<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu; // Import the Menu model
use Illuminate\Support\Facades\Session; // Import the Session facade

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Barang; //untuk akses kelas model barang
use App\Models\Penjualan; //untuk akses kelas model penjualan
use App\Models\PenjualanBarang; //untuk akses kelas model penjualan
use App\Models\Pembayaran; //untuk akses kelas model pembayaran
use App\Models\Pembeli; //untuk akses kelas model pembeli
use Illuminate\Support\Facades\DB; //untuk menggunakan db
use Illuminate\Support\Facades\Auth; //agar bisa mengakses session user_id dari user yang login

class KeranjangController extends Controller
{
    // tampilan galeri daftar barang
    public function daftarmenu()
    {
        // Ambil data menu
        $menu = Menu::all();

        // Kirim ke halaman view
        return view('galeri', [
            'menu' => $menu,
        ]);
    }

    public function tambahKeKeranjang(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:menu,id_menu',
        'harga' => 'required|numeric|min:0',
        'nama' => 'required|string',
        'quantity' => 'required|integer|min:1',
        'type' => 'nullable|string',
        'foto' => 'nullable|string', // 🆕 tambahkan ini
    ]);

    $id = $validated['id'];
    $cart = session('cart', []);

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] += $validated['quantity'];
    } else {
        $cart[$id] = [
            'id' => $id,
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
            'quantity' => $validated['quantity'],
            'type' => $validated['type'] ?? 'default',
            'foto' => $validated['foto'] ?? null, // 🆕 simpan juga foto
        ];
    }

    session(['cart' => $cart]);

    return response()->json([
        'success' => true,
        'message' => "{$validated['nama']} berhasil ditambahkan ke keranjang!",
        'cart_count' => array_sum(array_column($cart, 'quantity')),
        'cart_total' => array_reduce($cart, fn($total, $item) => $total + ($item['harga'] * $item['quantity']), 0),
    ]);
}


    // Add a method to display the cart
    public function lihatKeranjang()
{
    $cart = session('cart', []);
    $menu = [];
    $total_tagihan = 0;

    foreach ($menu as $itemId => $itemData) {
        $menuItem = \App\Models\Menu::find($itemId);
        if ($menuItem) {
            $menuItem->total_menu = $itemData['quantity'];
            $menuItem->total_belanja = $menuItem->harga * $itemData['quantity'];
            $menuItem->foto = $itemData['foto'] ?? $menuItem->foto; // backup jika tidak ditemukan
            $menu[] = $menuItem;
            $total_tagihan += $menuItem->total_belanja;
        }
    }

    $snap_token = null;

    if ($total_tagihan >= 100) {
        \Midtrans\Config::$serverKey = 'SB-Mid-server-u58u1fs7wrbRlXsRYFXgEfEB';
        \Midtrans\Config::$isProduction = false;

        $params = [
            'transaction_details' => [
                'order_id' => uniqid('ORDER-'),
                'gross_amount' => $total_tagihan,
            ],
            'customer_details' => [
                'first_name' => 'Pelanggan',
                'email' => 'customer@example.com',
            ],
        ];

        $snap_token = \Midtrans\Snap::getSnapToken($params);
    }

    return view('keranjang', [
        'menu' => $menu,
        'total_tagihan' => $total_tagihan,
        'snap_token' => $snap_token, // ✅ sekarang sudah didefinisikan
    ]);
}

}
