<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\QrCodeSession;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QrCodePresensiController extends Controller
{
    /**
     * Tampilkan halaman generate QR code untuk presensi
     */
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal_id = request('jadwal_id');
        
        // Ambil semua jadwal kuliah untuk dosen ini
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->orderBy('semester_id', 'desc')
            ->orderBy('hari', 'asc')
            ->get();

        $qrSession = null;
        $pertemuan_terakhir = 0;
        $selectedJadwal = null;
        
        if ($jadwal_id) {
            $selectedJadwal = JadwalKuliah::where('id', $jadwal_id)
                ->where('dosen_id', $dosen->id)
                ->with(['mataKuliah', 'semester'])
                ->firstOrFail();
            
            // Cek apakah ada QR session aktif
            $qrSession = QrCodeSession::where('jadwal_kuliah_id', $jadwal_id)
                ->where('is_active', true)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            // Ambil pertemuan terakhir dari presensi atau QR session
            $pertemuan_presensi = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                ->max('pertemuan') ?? 0;
            $pertemuan_qr = QrCodeSession::where('jadwal_kuliah_id', $jadwal_id)
                ->max('pertemuan') ?? 0;
            
            $pertemuan_terakhir = max($pertemuan_presensi, $pertemuan_qr);
        }

        return view('dosen.qr-presensi.index', compact('jadwals', 'qrSession', 'jadwal_id', 'pertemuan_terakhir', 'selectedJadwal'));
    }

    /**
     * Generate QR code baru untuk presensi
     */
    public function generate(Request $request, $jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $validated = $request->validate([
            'pertemuan' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'duration_minutes' => 'nullable|integer|min:5|max:120',
        ]);

        $duration = $validated['duration_minutes'] ?? 30;

        // Buat QR session baru
        $qrSession = QrCodeSession::createSession(
            $jadwal_id,
            $validated['pertemuan'],
            $validated['tanggal'],
            $duration
        );

        return redirect()->route('dosen.qr-presensi.show', ['jadwal_id' => $jadwal_id, 'token' => $qrSession->token])
            ->with('success', 'QR Code berhasil digenerate!');
    }

    /**
     * Tampilkan QR code yang aktif
     */
    public function show($jadwal_id, $token)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        $qrSession = QrCodeSession::where('token', $token)
            ->where('jadwal_kuliah_id', $jadwal_id)
            ->firstOrFail();

        return view('dosen.qr-presensi.show', compact('jadwal', 'qrSession'));
    }

    /**
     * Nonaktifkan QR code (stop presensi)
     */
    public function stop($token)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $qrSession = QrCodeSession::where('token', $token)
            ->whereHas('jadwalKuliah', function($query) use ($dosen) {
                $query->where('dosen_id', $dosen->id);
            })
            ->firstOrFail();

        $qrSession->update(['is_active' => false]);

        return redirect()->route('dosen.qr-presensi.index', ['jadwal_id' => $qrSession->jadwal_kuliah_id])
            ->with('success', 'QR Code telah dinonaktifkan.');
    }

    /**
     * API: Get QR session status (untuk auto-refresh)
     */
    public function status($token)
    {
        $qrSession = QrCodeSession::where('token', $token)
            ->with('jadwalKuliah.mataKuliah')
            ->firstOrFail();

        return response()->json([
            'is_active' => $qrSession->isValid(),
            'expires_at' => $qrSession->expires_at->toIso8601String(),
            'expires_in_seconds' => max(0, Carbon::now()->diffInSeconds($qrSession->expires_at, false)),
            'mata_kuliah' => $qrSession->jadwalKuliah->mataKuliah->nama_mk ?? 'N/A',
            'pertemuan' => $qrSession->pertemuan,
        ]);
    }
}