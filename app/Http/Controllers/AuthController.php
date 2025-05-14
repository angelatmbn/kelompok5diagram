<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// tambahan untuk proses authentikasi
use Illuminate\Support\Facades\Auth;
use App\Models\User; //untuk akses kelas model user

// untuk bisa menggunakan hash
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // method untuk menampilkan halaman awal login
    public function showLoginForm()
    {
        return view('login');
    }

    // proses validasi data login
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->user_group === 'admin') {
            return redirect('/admin'); // halaman admin
        } elseif ($user->user_group === 'customer') {
            return redirect('/depan'); // halaman customer
        } else {
            Auth::logout(); // kalau user_group tidak dikenal
            return redirect('/login')->withErrors(['user_group' => 'Role tidak dikenal.']);
        }
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
}


    // method untuk menangani logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // ubah password
    public function ubahpassword(){
        return view('ubahpassword');
    }

    // ubah password
    public function prosesubahpassword(Request $request){
        // echo $request->password ;
        $request->validate([
            'password' => 'required|string|min:5',
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('depan')->with('success', 'Password berhasil diperbarui!');
    }
}