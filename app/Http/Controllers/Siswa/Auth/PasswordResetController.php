<?php

namespace App\Http\Controllers\Siswa\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Menampilkan form untuk meminta link reset
    public function create()
    {
        return view('siswa.auth.forgot-password');
    }

    // Mengirim link reset ke email
    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Kita menggunakan 'siswas' broker yang akan kita definisikan nanti
        $status = Password::broker('siswas')->sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }

    // Menampilkan form untuk reset password
    public function edit(Request $request)
    {
        return view('siswa.auth.reset-password', ['request' => $request]);
    }

    // Menyimpan password baru
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::broker('siswas')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('siswa.login')->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    }
}