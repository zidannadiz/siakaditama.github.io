<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\QrCodeSession;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QrCodePresensiController extends Controller
{
    /**
     * Tampilkan halaman scan QR code
     */
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        return view('mahasiswa.qr-presensi.index', compact('mahasiswa'));
    }

    /**
     * Scan dan validasi QR code untuk presensi
     */
    public function scan(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'token' => 'required|string|exists:qr_code_sessions,token',
        ]);

        $token = $validated['token'];

        // Cari QR session
        $qrSession = QrCodeSession::where('token', $token)
            ->where('is_active', true)
            ->first();

        if (!$qrSession) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau sudah tidak aktif'
            ], 400);
        }

        // Cek apakah QR code masih valid (belum expired)
        if (!$qrSession->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code sudah kadaluarsa. Silakan minta QR code baru kepada dosen.'
            ], 400);
        }

        // Cek apakah mahasiswa terdaftar di jadwal kuliah ini
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $qrSession->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar di kelas ini'
            ], 403);
        }

        // Cek apakah sudah pernah absen di pertemuan ini
        $presensi_existing = Presensi::where('jadwal_kuliah_id', $qrSession->jadwal_kuliah_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('pertemuan', $qrSession->pertemuan)
            ->first();

        if ($presensi_existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi untuk pertemuan ini'
            ], 400);
        }

        // Buat presensi
        $presensi = Presensi::create([
            'jadwal_kuliah_id' => $qrSession->jadwal_kuliah_id,
            'mahasiswa_id' => $mahasiswa->id,
            'pertemuan' => $qrSession->pertemuan,
            'tanggal' => $qrSession->tanggal,
            'status' => 'hadir',
            'catatan' => 'Presensi via QR Code',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil!',
            'data' => [
                'presensi_id' => $presensi->id,
                'pertemuan' => $presensi->pertemuan,
                'tanggal' => $presensi->tanggal->format('d/m/Y'),
                'status' => $presensi->status,
            ]
        ]);
    }

    /**
     * Tampilkan riwayat presensi QR code
     */
    public function history()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        // Ambil semua presensi yang dilakukan via QR code
        $presensis = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('catatan', 'Presensi via QR Code')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.semester'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('mahasiswa.qr-presensi.history', compact('presensis'));
    }

    /**
     * Public scan QR code (bisa diakses tanpa login)
     * Akan redirect ke login jika belum login, atau langsung proses jika sudah login
     */
    public function publicScan($token)
    {
        // Cek apakah token valid
        $qrSession = QrCodeSession::where('token', $token)
            ->where('is_active', true)
            ->first();

        if (!$qrSession) {
            return redirect()->route('login')
                ->with('error', 'QR Code tidak valid atau sudah tidak aktif. Silakan hubungi dosen untuk QR code baru.');
        }

        // Cek apakah QR code masih valid (belum expired)
        if (!$qrSession->isValid()) {
            return redirect()->route('login')
                ->with('error', 'QR Code sudah kadaluarsa. Silakan minta QR code baru kepada dosen.');
        }

        // Jika user sudah login sebagai mahasiswa
        if (Auth::check() && Auth::user()->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
            
            if ($mahasiswa) {
                // Cek apakah sudah pernah absen
                $presensi_existing = Presensi::where('jadwal_kuliah_id', $qrSession->jadwal_kuliah_id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->where('pertemuan', $qrSession->pertemuan)
                    ->first();

                if ($presensi_existing) {
                    return redirect()->route('mahasiswa.qr-presensi.history')
                        ->with('info', 'Anda sudah melakukan presensi untuk pertemuan ini.');
                }

                // Cek apakah terdaftar di kelas
                $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
                    ->where('jadwal_kuliah_id', $qrSession->jadwal_kuliah_id)
                    ->where('status', 'disetujui')
                    ->first();

                if (!$krs) {
                    return redirect()->route('mahasiswa.qr-presensi.index')
                        ->with('error', 'Anda tidak terdaftar di kelas ini.');
                }

                // Buat presensi
                Presensi::create([
                    'jadwal_kuliah_id' => $qrSession->jadwal_kuliah_id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'pertemuan' => $qrSession->pertemuan,
                    'tanggal' => $qrSession->tanggal,
                    'status' => 'hadir',
                    'catatan' => 'Presensi via QR Code',
                ]);

                return redirect()->route('mahasiswa.qr-presensi.history')
                    ->with('success', 'Presensi berhasil! Pertemuan ' . $qrSession->pertemuan . ' pada ' . $qrSession->tanggal->format('d F Y'));
            }
        }

        // Jika belum login atau bukan mahasiswa, simpan token di session dan redirect ke login
        session(['qr_token' => $token, 'redirect_after_login' => 'qr-presensi']);
        
        return redirect()->route('login')
            ->with('info', 'Silakan login sebagai mahasiswa untuk melakukan presensi via QR Code.');
    }
}