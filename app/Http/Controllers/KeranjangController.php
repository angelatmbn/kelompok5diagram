<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\menu; //untuk akses kelas model menu
use App\Models\Penjualan; //untuk akses kelas model penjualan
use App\Models\DetailPenjualan; //untuk akses kelas model penjualan
use App\Models\Pembayaran; //untuk akses kelas model pembayaran
use App\Models\Pelanggan; //untuk akses kelas model pelanggan
use Illuminate\Support\Facades\DB; //untuk menggunakan db
use Illuminate\Support\Facades\Auth; //agar bisa mengakses session user_id dari user yang login

class KeranjangController extends Controller
{
    // tampilan galeri daftar menu
    public function daftarmenu()
    {
        $id_user = Auth::user()->id;

        // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
        $pelanggan = Pelanggan::where('user_id', $id_user)
                        ->select(DB::raw('id'))
                        ->first();
        $id_pelanggan = $pelanggan->id;

        // ambil data menu
        $menu = menu::all();

        // query total belanja yang belum terbayar
        // $menudibeli = Penjualan::where('pelanggan_id', $id_pelanggan)
        //                 ->where('pembayaran', 0)
        //                 ->first();
        // $menudibeli = DB::table('penjualan')
        //                 ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
        //                 ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
        //                 ->where(function($query) {
        //                     $query->where('pembayaran.gross_amount', 0)
        //                           ->orWhere(function($q) {
        //                               $q->where('pembayaran.status_code', '!=', 200)
        //                                 ->where('pembayaran.jenis_pembayaran', 'pg');
        //                           });
        //                 })
        //                 ->selectRaw('IFNULL(COUNT(penjualan.tagihan), 0) as tagihan')
        //                 ->value('tagihan');

        // dd(var_dump($menudibeli));
        // jumlah menu dibeli
        $jumlahmenudibeli = DB::table('penjualan')
                            ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                            ->select(DB::raw('COUNT(DISTINCT menu_id) as total'))
                            ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
                            ->where(function($query) {
                                $query->where('pembayaran.gross_amount', 0)
                                      ->orWhere(function($q) {
                                          $q->where('pembayaran.status_code', '!=', 200)
                                            ->where('pembayaran.jenis_pembayaran', 'pg');
                                      });
                            })
                            ->get();

        $t = DB::table('penjualan')
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
        ->select(DB::raw('SUM(subtotal * jumlah) as total'))
        ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
        ->where(function($query) {
            $query->where('pembayaran.gross_amount', 0)
                  ->orWhere(function($q) {
                      $q->where('pembayaran.status_code', '!=', 200)
                        ->where('pembayaran.jenis_pembayaran', 'pg');
                  });
        })
        ->first();

        // kirim ke halaman view
        return view('galeri',
                        [
                            'menu'=>$menu,
                            'total_belanja' => $t->total ?? 0,
                            'jumlahmenudibeli' => $jumlahmenudibeli[0]->total ?? 0
                        ]
                    );
    }

    // halaman tambah keranjang
    public function tambahKeranjang(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        try {
            $request->validate([
                'product_id' => 'required|exists:menu,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $id_user = Auth::user()->id;

            // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
            $pelanggan = Pelanggan::where('user_id', $id_user)
                            ->select(DB::raw('id'))
                            ->first();
            $id_pelanggan = $pelanggan->id;

            // cek di database apakah ada nomor faktur yang masih aktif
            // dilihat dari pembayaran yg masih 0

            try{
                $product = menu::find($request->product_id); //ambi data menu simpan di tabel product
                if (!$product) {
                    return response()->json(['success' => false, 'message' => 'menu tidak ditemukan!']);
                }
                $harga = $product->harga;
                $jumlah = (int) $request->quantity;
                $menu_id = $request->product_id;

               // Cek apakah ada penjualan dengan gross_amount = 0
                $penjualanExist = DB::table('penjualan')
                ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                ->where('penjualan.pelanggan_id', $id_pelanggan)
                ->where(function($query) {
                    $query->where('pembayaran.gross_amount', 0)
                          ->orWhere(function($q) {
                              $q->where('pembayaran.status_code', '!=', 200)
                                ->where('pembayaran.jenis_pembayaran', 'pg');
                          });
                })
                ->select('penjualan.id') // Ambil ID saja untuk dicek
                ->first();

                if (!$penjualanExist) {
                    // Buat penjualan baru jika tidak ada
                    $penjualan = Penjualan::create([
                        'no_faktur'   => Penjualan::getKodeFaktur(),
                        'tgl'         => now(),
                        'pelanggan_id'  => $id_pelanggan,
                        'tagihan'     => 0,
                        'status'      => 'pesan',
                    ]);

                    // Buat pembayaran baru
                    $pembayaran = Pembayaran::create([
                        'penjualan_id'      => $penjualan->id,
                        'tgl_bayar'         => now(),
                        'jenis_pembayaran'  => 'pg',
                        'gross_amount'      => 0,
                    ]);
                }else{
                    $penjualan = Penjualan::find($penjualanExist->id);
                }


                // Tambahkan menu ke detail_penjualan
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'menu_id' => $menu_id,
                    'jumlah' => $jumlah,
                    'harga'=>$harga,
                    'subtotal'=>$harga*1.2,
                    'tgl'=>date('Y-m-d')
                ]);

                // Update total tagihan pada tabel penjualan
                // $penjualan->tagihan = Penjualanmenu::where('penjualan_id', $penjualan->id)->sum('total');
                $tagihan = DB::table('penjualan')
                ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                ->select(DB::raw('SUM(subtotal * jumlah) as total'))
                ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
                ->where(function($query) {
                    $query->where('pembayaran.gross_amount', 0)
                          ->orWhere(function($q) {
                              $q->where('pembayaran.status_code', '!=', 200)
                                ->where('pembayaran.jenis_pembayaran', 'pg');
                          });
                })
                ->first();
                $penjualan->tagihan = $tagihan->total;
                $penjualan->save();

                // update stok menu kurangi 1
                menu::where('id', $menu_id)->decrement('stok', $jumlah);

                // hitung total menu
                $jumlahmenudibeli = DB::table('penjualan')
                            ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                            ->select(DB::raw('COUNT(DISTINCT menu_id) as total'))
                            ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
                            ->where(function($query) {
                                $query->where('pembayaran.gross_amount', 0)
                                      ->orWhere(function($q) {
                                          $q->where('pembayaran.status_code', '!=', 200)
                                            ->where('pembayaran.jenis_pembayaran', 'pg');
                                      });
                            })
                            ->get();

                // DB::commit(); //commit ke database
                return response()->json(['success' => true, 'message' => 'Transaksi berhasil ditambahkan!',
                'total' => $penjualan->tagihan, 'jumlahmenudibeli'=>$jumlahmenudibeli[0]->total ?? 0]);

            }catch(\Exception $e){
                // DB::rollBack(); //rollback jika ada error
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // halaman lihat keranjang
    public function lihatkeranjang(){
        date_default_timezone_set('Asia/Jakarta');
        $id_user = Auth::user()->id;

        // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
        $pelanggan = Pelanggan::where('user_id', $id_user)
                        ->select(DB::raw('id'))
                        ->first();
        $id_pelanggan = $pelanggan->id;
        // dd(var_dump($id_pelanggan));

        $menu = DB::table('penjualan')
                        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                        ->join('menu', 'detail_penjualan.menu_id', '=', 'menu.id')
                        ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                        ->select('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan', 'detail_penjualan.menu_id', 'menu.nama_menu','detail_penjualan.subtotal',
                                 'menu.foto','pembayaran.order_id',
                                  DB::raw('SUM(detail_penjualan.jumlah) as total_menu'),
                                  DB::raw('SUM(detail_penjualan.subtotal * detail_penjualan.jumlah) as total_belanja'))
                        ->where('penjualan.pelanggan_id', '=',$id_pelanggan)
                        ->where(function($query) {
                            $query->where('pembayaran.gross_amount', 0)
                                  ->orWhere(function($q) {
                                      $q->where('pembayaran.status_code', '!=', 200)
                                        ->where('pembayaran.jenis_pembayaran', 'pg');
                                  });
                        })
                        ->groupBy('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan','detail_penjualan.menu_id', 'menu.nama_menu','detail_penjualan.subtotal',
                                  'menu.foto','pembayaran.order_id',
                                 )
                        ->get();

        // hitung jumlah total tagihan
        $ttl = 0; $jumlah_menu = 0; $kode_faktur = '';
        foreach($menu as $p){
            $ttl += $p->total_belanja;
            $jumlah_menu += 1;
            $kode_faktur = $p->no_faktur;
            $idpenjualan = $p->id;
            $odid = $p->order_id;
        }

        // cek dulu apakah sudah ada di midtrans dan belum expired
        $ch = curl_init();
        $login = env('MIDTRANS_SERVER_KEY');
        $password = '';
        if(isset($odid)){
            $parts = explode('-', $odid);
            $substring = $parts[0] . '-' . $parts[1];
            $orderid = $substring;
        }else{
            $orderid =$kode_faktur.'-'.date('YmdHis'); //FORMAT
        }

        $URL =  'https://api.sandbox.midtrans.com/v2/'.$orderid.'/status';
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        $output = curl_exec($ch);
        curl_close($ch);
        $outputjson = json_decode($output, true); //parsing json dalam bentuk assosiative array
        // return $outputjson;

        // ambil statusnya
        if($outputjson['status_code']==404 or in_array($outputjson['transaction_status'], ['expire', 'cancel', 'deny'])){
            // echo "transaksi tidak ditemukan diserver midtrans ";
            // cek jika jumlah datanya 0 maka jangan menjalankan payment gateway
            if($ttl>0){
                // proses generate token payment gateway
                $order_id = $kode_faktur.'-'.date('YmdHis');


                $myArray = array(); //untuk menyimpan objek array
                $i = 1;
                foreach($menu as $k):
                    // untuk data item detail
                    // kita perlu membuat objek dulu kemudian di masukkan ke array
                    $foo = array(
                            'id'=> $i,
                            'price' => $k->subtotal,
                            'quantity' => $k->total_menu,
                            'name' => $k->nama_menu,

                    );
                    $i++;
                    // tambahkan ke myarray
                    array_push($myArray,$foo);
                endforeach;

                \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
                // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                \Midtrans\Config::$isProduction = false;
                // Set sanitization on (default)
                \Midtrans\Config::$isSanitized = true;
                // Set 3DS transaction for credit card to true
                \Midtrans\Config::$is3ds = true;

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order_id,
                        'gross_amount' => $ttl, //gross amount diisi total tagihan
                    ),
                    'item_details' => $myArray,
                    'expiry' => [
                            'start_time' => date("Y-m-d H:i:s O"), // sekarang
                            'unit' => 'minutes', // bisa 'minutes', 'hours', atau 'days'
                            'duration' => 2 // expired dalam 60 menit
                    ]
                );

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                $pembayaran = Pembayaran::updateOrCreate(
                    ['penjualan_id' => $idpenjualan], // Cek apakah id penjualan sudah ada
                    [
                        'tgl_bayar'        => now(),
                        'jenis_pembayaran' => 'pg', // Payment Gateway
                        'order_id'         => $order_id,
                        'gross_amount'     => $ttl,
                        'status_code'      => '201', // 201 = Pending
                        'status_message'   => 'Pending payment', // Status awal
                        'transaction_id' => $snapToken, //snap tokennya di simpan di transaction id

                    ]
                );

                return view( 'keranjang',
                            [
                                'menu' => $menu,
                                'tagihan' => $ttl,
                                'jumlah_menu' => $jumlah_menu,
                                'snap_token' => $snapToken,
                            ]
                );
            }else{
                // kalau transaksi kosong diarahkan saja ke depan
                return redirect('/depan');
            }
        }else{
            // echo "transaksi ditemukan diserver midtrans, maka tinggal bayar";

            $tagihan = DB::table('penjualan')
            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
            ->select(DB::raw('transaction_id'))
            ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
            ->where(function($query) {
                $query->where('pembayaran.gross_amount', 0)
                      ->orWhere(function($q) {
                          $q->where('pembayaran.status_code', '!=', 200)
                            ->where('pembayaran.jenis_pembayaran', 'pg');
                      });
            })
            ->first();

            $menu = DB::table('penjualan')
                        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                        ->join('menu', 'detail_penjualan.menu_id', '=', 'menu.id')
                        ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                        ->select('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan', 'detail_penjualan.menu_id', 'menu.nama_menu','detail_penjualan.subtotal',
                                 'menu.foto',
                                  DB::raw('SUM(detail_penjualan.jumlah) as total_menu'),
                                  DB::raw('SUM(detail_penjualan.subtotal * detail_penjualan.jumlah) as total_belanja'))
                        ->where('penjualan.pelanggan_id', '=',$id_pelanggan)
                        ->where(function($query) {
                            $query->where('pembayaran.gross_amount', 0)
                                  ->orWhere(function($q) {
                                      $q->where('pembayaran.status_code', '!=', 200)
                                        ->where('pembayaran.jenis_pembayaran', 'pg');
                                  });
                        })
                        ->groupBy('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan','detail_penjualan.menu_id', 'menu.nama_menu','detail_penjualan.subtotal',
                                  'menu.foto',
                                 )
                        ->get();

            $ttl = 0; $jumlah_menu = 0; $kode_faktur = '';
            foreach($menu as $p){
                $ttl += $p->total_belanja;
                $jumlah_menu += 1;
                $kode_faktur = $p->no_faktur;
                $idpenjualan = $p->id;
            }

            return view('keranjang', [
                'menu' => $menu,
                'tagihan' => $ttl,
                'jumlah_menu' => $jumlah_menu,
                'snap_token' => $tagihan->transaction_id
            ]);
        }


    }

    // untuk menghapus
    public function hapus($menu_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $id_user = Auth::user()->id;

        // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
        $pelanggan = Pelanggan::where('user_id', $id_user)
                        ->select(DB::raw('id'))
                        ->first();
        $id_pelanggan = $pelanggan->id;


        $sql = "DELETE FROM detail_penjualan WHERE menu_id = ? AND penjualan_id = (SELECT penjualan.id FROM penjualan join pembayaran on (penjualan.id=pembayaran.penjualan_id) WHERE penjualan.pelanggan_id = ? AND ((pembayaran.gross_amount = 0) or (pembayaran.jenis_pembayaran='pg' and pembayaran.status_code<>'200')))";
        $deleted = DB::delete($sql, [$menu_id,$id_pelanggan]);

        $penjualan = DB::table('penjualan')
            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
            ->select('penjualan.id')
            ->where('penjualan.pelanggan_id', $id_pelanggan)
            ->where(function($query) {
                $query->where('pembayaran.gross_amount', 0)
                      ->orWhere(function($q) {
                          $q->where('pembayaran.status_code', '!=', 200)
                            ->where('pembayaran.jenis_pembayaran', 'pg');
                      });
            })
            ->first();

        // Update total tagihan pada tabel penjualan
        $tagihan = DB::table('penjualan')
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
        ->select(DB::raw('SUM(subtotal * jumlah) as total'))
        ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
        ->where(function($query) {
            $query->where('pembayaran.gross_amount', 0)
                  ->orWhere(function($q) {
                      $q->where('pembayaran.status_code', '!=', 200)
                        ->where('pembayaran.jenis_pembayaran', 'pg');
                  });
        })
        ->first();

        if ($penjualan) {
            DB::table('penjualan')
                ->where('id', $penjualan->id)
                ->update(['tagihan' => $tagihan->total]);
        }



        $jumlahmenudibeli = DB::table('penjualan')
                            ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                            ->select(DB::raw('COUNT(DISTINCT menu_id) as total'))
                            ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
                            ->where(function($query) {
                                $query->where('pembayaran.gross_amount', 0)
                                      ->orWhere(function($q) {
                                          $q->where('pembayaran.status_code', '!=', 200)
                                            ->where('pembayaran.jenis_pembayaran', 'pg');
                                      });
                            })
                            ->get();


        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus', 'total' => $tagihan->total, 'jumlahmenudibeli'=>$jumlahmenudibeli[0]->total ?? 0]);
    }

    // untuk autorefresh dari server midtrans yang sudah terbayarkan akan diupdatekan ke database
    // termasuk menangani ketika sudah expired
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

    // melihat riwayat pesanan
    public function lihatriwayat(){
        date_default_timezone_set('Asia/Jakarta');
        $id_user = Auth::user()->id;

        // dapatkan id_pelanggan dari user_id di tabel users sesuai data yang login
        $pelanggan = Pelanggan::where('user_id', $id_user)
                        ->select(DB::raw('id'))
                        ->first();
        $id_pelanggan = $pelanggan->id;

        // dd(var_dump($menudibeli));
        // jumlah menu dibeli
        $jumlahmenudibeli = DB::table('penjualan')
                            ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                            ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                            ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                            ->select(DB::raw('COUNT(DISTINCT menu_id) as total'))
                            ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
                            ->where(function($query) {
                                $query->where('pembayaran.gross_amount', 0)
                                      ->orWhere(function($q) {
                                          $q->where('pembayaran.status_code', '!=', 200)
                                            ->where('pembayaran.jenis_pembayaran', 'pg');
                                      });
                            })
                            ->get();

        $t = DB::table('penjualan')
        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
        ->select(DB::raw('SUM(subtotal * jumlah) as total'))
        ->where('penjualan.pelanggan_id', '=', $id_pelanggan)
        ->where(function($query) {
            $query->where('pembayaran.gross_amount', 0)
                  ->orWhere(function($q) {
                      $q->where('pembayaran.status_code', '!=', 200)
                        ->where('pembayaran.jenis_pembayaran', 'pg');
                  });
        })
        ->first();

        $menu = DB::table('penjualan')
                        ->join('detail_penjualan', 'penjualan.id', '=', 'detail_penjualan.penjualan_id')
                        ->join('pembayaran', 'penjualan.id', '=', 'pembayaran.penjualan_id')
                        ->join('menu', 'detail_penjualan.menu_id', '=', 'menu.id')
                        ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                        ->select('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan', 'detail_penjualan.menu_id', 'menu.nama_menu','detail_penjualan.subtotal',
                                 'menu.foto',
                                  DB::raw('SUM(detail_penjualan.jumlah) as total_menu'),
                                  DB::raw('SUM(detail_penjualan.subtotal * detail_penjualan.jumlah) as total_belanja'))
                        ->where('penjualan.pelanggan_id', '=',$id_pelanggan)
                        ->where(function($query) {
                            $query->where('pembayaran.gross_amount', 0)
                                  ->orWhere(function($q) {
                                      $q->where('pembayaran.status_code', '!=', 200)
                                        ->where('pembayaran.jenis_pembayaran', 'pg');
                                  });
                        })
                        ->groupBy('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan','detail_penjualan.menu_id', 'menu.nama_menu','detail_penjualan.subtotal',
                                  'menu.foto',
                                 )
                        ->get();

        // hitung jumlah total tagihan
        $ttl = 0; $jumlah_menu = 0; $kode_faktur = '';
        foreach($menu as $p){
            $ttl += $p->total_belanja;
            $jumlah_menu += 1;
            $kode_faktur = $p->no_faktur;
            $idpenjualan = $p->id;
        }

        // DATA RIWAYAT PEMESANAN
        $transaksi = DB::select("
                              SELECT * FROM penjualan
                              WHERE pelanggan_id = ?
                    ", [$id_pelanggan]);

        // Ambil semua id penjualan
        $penjualan_ids = array_column($transaksi, 'id');

        // Ambil detail menu sekaligus
        $detail_menu = DB::table('detail_penjualan')
            ->join('menu', 'detail_penjualan.menu_id', '=', 'menu.id')
            ->whereIn('penjualan_id', $penjualan_ids)
            ->get()
            ->groupBy('penjualan_id'); // dikelompokkan per faktur

        return view('riwayat',
                        [
                            'transaksi' => $transaksi,
                            'detail_menu' => $detail_menu ,
                            'tagihan' => $ttl,
                            'total_belanja' => $t->total ?? 0,
                            'jumlahmenudibeli' => $jumlahmenudibeli[0]->total ?? 0
                        ]
                    );
    }

}