<?php

namespace App\Http\Controllers\Api\Dosen;

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
     * Get list of jadwal kuliah and active QR session
     */
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan'
            ], 404);
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
                ->first();
            
            if ($selectedJadwal) {
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
        }

        return response()->json([
            'success' => true,
            'data' => [
                'jadwals' => $jadwals->map(function($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'mata_kuliah' => [
                            'id' => $jadwal->mataKuliah->id ?? null,
                            'nama' => $jadwal->mataKuliah->nama_mk ?? null,
                            'kode' => $jadwal->mataKuliah->kode_mk ?? null,
                        ],
                        'semester' => [
                            'id' => $jadwal->semester->id ?? null,
                            'nama' => $jadwal->semester->nama_semester ?? null,
                        ],
                        'hari' => $jadwal->hari,
                        'jam_mulai' => $jadwal->jam_mulai?->format('H:i'),
                        'jam_selesai' => $jadwal->jam_selesai?->format('H:i'),
                        'ruangan' => $jadwal->ruangan,
                    ];
                }),
                'selected_jadwal' => $selectedJadwal ? [
                    'id' => $selectedJadwal->id,
                    'mata_kuliah' => [
                        'id' => $selectedJadwal->mataKuliah->id ?? null,
                        'nama' => $selectedJadwal->mataKuliah->nama_mk ?? null,
                        'kode' => $selectedJadwal->mataKuliah->kode_mk ?? null,
                    ],
                    'semester' => [
                        'id' => $selectedJadwal->semester->id ?? null,
                        'nama' => $selectedJadwal->semester->nama_semester ?? null,
                    ],
                    'hari' => $selectedJadwal->hari,
                    'jam_mulai' => $selectedJadwal->jam_mulai?->format('H:i'),
                    'jam_selesai' => $selectedJadwal->jam_selesai?->format('H:i'),
                    'ruangan' => $selectedJadwal->ruangan,
                ] : null,
                'qr_session' => $qrSession ? [
                    'id' => $qrSession->id,
                    'token' => $qrSession->token,
                    'pertemuan' => $qrSession->pertemuan,
                    'tanggal' => $qrSession->tanggal->format('Y-m-d'),
                    'expires_at' => $qrSession->expires_at->toISOString(),
                    'expires_in_seconds' => max(0, Carbon::now()->diffInSeconds($qrSession->expires_at, false)),
                    'is_active' => $qrSession->is_active,
                    'is_valid' => $qrSession->isValid(),
                ] : null,
                'pertemuan_terakhir' => $pertemuan_terakhir,
            ],
        ]);
    }

    /**
     * Generate QR code baru untuk presensi
     */
    public function generate(Request $request, $jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan'
            ], 404);
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal kuliah tidak ditemukan'
            ], 404);
        }

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

        return response()->json([
            'success' => true,
            'message' => 'QR Code berhasil digenerate!',
            'data' => [
                'id' => $qrSession->id,
                'token' => $qrSession->token,
                'pertemuan' => $qrSession->pertemuan,
                'tanggal' => $qrSession->tanggal->format('Y-m-d'),
                'expires_at' => $qrSession->expires_at->toISOString(),
                'expires_in_seconds' => max(0, Carbon::now()->diffInSeconds($qrSession->expires_at, false)),
                'is_active' => $qrSession->is_active,
                'is_valid' => $qrSession->isValid(),
            ],
        ]);
    }

    /**
     * Get QR session details
     */
    public function show($token)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan'
            ], 404);
        }

        $qrSession = QrCodeSession::where('token', $token)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.semester'])
            ->first();

        if (!$qrSession) {
            return response()->json([
                'success' => false,
                'message' => 'QR Session tidak ditemukan'
            ], 404);
        }

        // Cek apakah dosen adalah pemilik jadwal
        if ($qrSession->jadwalKuliah->dosen_id !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke QR code ini'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $qrSession->id,
                'token' => $qrSession->token,
                'pertemuan' => $qrSession->pertemuan,
                'tanggal' => $qrSession->tanggal->format('Y-m-d'),
                'expires_at' => $qrSession->expires_at->toISOString(),
                'expires_in_seconds' => max(0, Carbon::now()->diffInSeconds($qrSession->expires_at, false)),
                'is_active' => $qrSession->is_active,
                'is_valid' => $qrSession->isValid(),
                'jadwal_kuliah' => [
                    'id' => $qrSession->jadwalKuliah->id,
                    'mata_kuliah' => [
                        'id' => $qrSession->jadwalKuliah->mataKuliah->id ?? null,
                        'nama' => $qrSession->jadwalKuliah->mataKuliah->nama_mk ?? null,
                        'kode' => $qrSession->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    ],
                    'semester' => [
                        'id' => $qrSession->jadwalKuliah->semester->id ?? null,
                        'nama' => $qrSession->jadwalKuliah->semester->nama_semester ?? null,
                    ],
                    'hari' => $qrSession->jadwalKuliah->hari,
                    'ruangan' => $qrSession->jadwalKuliah->ruangan,
                ],
            ],
        ]);
    }

    /**
     * Get QR session status (untuk auto-refresh)
     */
    public function status($token)
    {
        $qrSession = QrCodeSession::where('token', $token)
            ->with('jadwalKuliah.mataKuliah')
            ->first();

        if (!$qrSession) {
            return response()->json([
                'success' => false,
                'message' => 'QR Session tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_active' => $qrSession->isValid(),
                'expires_at' => $qrSession->expires_at->toISOString(),
                'expires_in_seconds' => max(0, Carbon::now()->diffInSeconds($qrSession->expires_at, false)),
                'mata_kuliah' => $qrSession->jadwalKuliah->mataKuliah->nama_mk ?? 'N/A',
                'pertemuan' => $qrSession->pertemuan,
            ],
        ]);
    }

    /**
     * Stop QR code (nonaktifkan)
     */
    public function stop($token)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Data dosen tidak ditemukan'
            ], 404);
        }

        $qrSession = QrCodeSession::where('token', $token)
            ->whereHas('jadwalKuliah', function($query) use ($dosen) {
                $query->where('dosen_id', $dosen->id);
            })
            ->first();

        if (!$qrSession) {
            return response()->json([
                'success' => false,
                'message' => 'QR Session tidak ditemukan'
            ], 404);
        }

        $qrSession->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code telah dinonaktifkan',
        ]);
    }
}
