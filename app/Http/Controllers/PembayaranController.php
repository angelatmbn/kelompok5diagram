<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    use Illuminate\Http\Request;
use App\Models\Pembayaran;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Models\Pembayaran;

public function simpan(Request $request)
{
    Pembayaran::create([
        'penjualan_id'     => $request->penjualan_id,
        'tgl_bayar'        => now(),
        'jenis_pembayaran' => 'pg',
        'transaction_time' => $request->transaction_time,
        'gross_amount'     => $request->gross_amount,
        'order_id'         => $request->order_id,
        'payment_type'     => $request->payment_type,
        'status_code'      => $request->status_code,
        'transaction_id'   => $request->transaction_id,
        'settlement_time'  => $request->settlement_time,
        'status_message'   => $request->status_message,
        'merchant_id'      => $request->merchant_id
    ]);

    return response()->json(['success' => true]);
}

}
