<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'penjual'
                ? redirect('/penjual/dashboard')
                : redirect('/');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'penjual') {
                return redirect('/penjual/dashboard')->with('message', [
                    'message' => 'Selamat datang kembali, Penjual!',
                    'type' => 'success'
                ]);
            }

            return redirect('/')->with('message', [
                'message' => 'Berhasil masuk akun.',
                'type' => 'success'
            ]);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'password_confirm' => 'required|same:password',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        // Custom password strength validation to match native PHP code
        $password = $validated['password'];
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            return back()->withErrors([
                'password' => 'Password harus mengandung minimal satu huruf besar, satu huruf kecil, dan satu angka.',
            ])->withInput();
        }

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'pembeli', // Locked as pembeli per native code
            'no_hp' => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'kota' => $validated['kota'] ?? null,
            'kode_pos' => $validated['kode_pos'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')->with('message', [
            'message' => 'Pendaftaran berhasil dan Anda telah masuk akun.',
            'type' => 'success'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', [
            'message' => 'Anda telah berhasil keluar.',
            'type' => 'success'
        ]);
    }
}
