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
        $cart[$id]['quantity'] += (int) $validated['quantity'];
    } else {
        $cart[$id] = [
            'id' => $id,
            'nama' => $validated['nama'],
            'harga' => (int) $validated['harga'],
            'quantity' => (int) $validated['quantity'],
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

    foreach ($cart as $itemId => $itemData) {
        $menuItem = Menu::find($itemId); // Ambil dari DB berdasarkan id_menu
        if ($menuItem) {
            $menu[] = [
                'id' => $itemId,
                'nama' => $itemData['nama'],
                'harga' => $itemData['harga'],
                'quantity' => $itemData['quantity'],
                'foto' => $itemData['foto'] ?? $menuItem->foto,
            ];
            $total_tagihan += $itemData['harga'] * $itemData['quantity'];
        }
    }

    // Setup pembayaran Midtrans jika ada item
    $snap_token = null;
    if ($total_tagihan >= 100) {
        \Midtrans\Config::$serverKey = 'SB-Mid-server-LlXNqf8UHbv4QtM2XRQeWGur';
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
        'snap_token' => $snap_token,
    ]);
}
public function cek_status_pembayaran_pg(){
        date_default_timezone_set('Asia/Jakarta');
        $pembayaranPending = Pembayaran::where('jenis_pembayaran', 'pg')
        ->where(DB::raw("IFNULL(status_code, '0')"), '<>', '200')
        ->orderBy('tgl_bayar', 'desc')
        ->get();    
        // var_dump($pembayaranPending);
        // dd();
        $id = array();
        $kode_faktur = array();
		foreach($pembayaranPending as $ks){
			array_push($id,$ks->order_id);
            // echo $ks->order_id;
            // untuk mendapatkan no_faktur dari pola F-0000002-20250406 => F-0000002
            $parts = explode('-', $ks->order_id);
            $substring = $parts[0] . '-' . $parts[1];

            array_push($kode_faktur,$substring);
            // echo $substring;
		}

        for($i=0; $i<count($id); $i++){
            // echo "masuk sini";
            $ch = curl_init(); 
            $login = env('MIDTRANS_SERVER_KEY');
            $password = '';
            $orderid = $id[$i];
            // echo $orderid;
            $kode_faktur = $kode_faktur[$i];
            $URL =  'https://api.sandbox.midtrans.com/v2/'.$orderid.'/status';
            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");  
            $output = curl_exec($ch); 
            curl_close($ch);    
            $outputjson = json_decode($output, true); //parsing json dalam bentuk assosiative array
            // var_dump($outputjson);
            // dd();

            // lakukan penanganan jika sudah expired
            if($outputjson['status_code']!=404){
                //diluar 404
                if(in_array($outputjson['transaction_status'], ['expire', 'cancel', 'deny'])){
                    // maka kembalikan posisi ke pemesanan 
                    // hapus snap token dari transaction_id
                    $affected = DB::update(
                        'update pembayaran 
                         set status_code = null,
                             transaction_time = null,
                             gross_amount = 0,
                             transaction_id = null
                         where order_id = ?',
                        [
                            $orderid
                        ]
                    );
    
                }else{
                    // 
                    $affected = DB::update(
                        'update pembayaran 
                         set status_code = ?,
                             transaction_time = ?,
                             settlement_time = ?,
                             status_message = ?,
                             merchant_id = ?
                         where order_id = ?',
                        [
                            $outputjson['status_code'] ?? null, 
                            $outputjson['transaction_time'] ?? null, 
                            $outputjson['settlement_time'] ?? null, 
                            $outputjson['status_message'] ?? null, 
                            $outputjson['merchant_id'] ?? null, 
                            $orderid
                        ]
                    );
        
                    if($outputjson['status_code']=='200'){
                        $affected = DB::update(
                            'update penjualan 
                             set status = "bayar"
                             where no_faktur = ?',
                            [
                                $kode_faktur
                            ]
                        );
                    }
                    // 
                }
                // akhir
            }

            // jika tidak ditemukan
            if($outputjson['status_code']==404){
                // cek apakah ada datanya di pembayaran, jika ada maka hapus
                $dataorderid = Pembayaran::where('order_id',$orderid)
                ->select(DB::raw('order_id'))
                ->first();
                if(isset($dataorderid->order_id)){
                    // jika ditemukan maka kembalikan ke awal
                    $affected = DB::update(
                        'update pembayaran 
                         set status_code = null,
                             transaction_time = null,
                             gross_amount = 0,
                             transaction_id = null,
                             order_id = null
                         where order_id = ?',
                        [
                            $orderid
                        ]
                    );
                }
            }
            
            
        }
        return view('autorefresh');
    }

    public function clearCart(Request $request)
{
    session()->forget('cart');
    return response()->json(['success' => true]);
}

public function bayarSukses()
{
    session()->forget('cart'); // hapus server-side cart

    return redirect('/depan')->with([
        'clear_client_cart' => true,
        'should_autorefresh' => true // tambahkan flag auto reload
    ]);
}

}
