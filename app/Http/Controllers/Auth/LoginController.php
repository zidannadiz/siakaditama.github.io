<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Regenerate session ID for security
            $request->session()->regenerate();
            
            // Ensure session is saved to database (for database driver)
            $request->session()->save();
            
            // Check if there's a QR token to process after login
            if (session('qr_token') && $user->role === 'mahasiswa') {
                $qrToken = session('qr_token');
                session()->forget(['qr_token', 'redirect_after_login']);
                
                // Redirect to public scan route to process the QR code
                return redirect()->route('qr-presensi.public-scan', $qrToken);
            }
            
            return match($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'dosen' => redirect()->route('dosen.dashboard'),
                'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
                default => redirect()->route('dashboard'),
            };
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

