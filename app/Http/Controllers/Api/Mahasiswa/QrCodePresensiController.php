<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\QrCodeSession;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrCodePresensiController extends Controller
{
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
                'jadwal_kuliah' => [
                    'id' => $qrSession->jadwalKuliah->id,
                    'mata_kuliah' => [
                        'id' => $qrSession->jadwalKuliah->mataKuliah->id ?? null,
                        'nama' => $qrSession->jadwalKuliah->mataKuliah->nama_mk ?? null,
                    ],
                ],
            ],
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
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        // Ambil semua presensi yang dilakukan via QR code
        $presensis = Presensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('catatan', 'Presensi via QR Code')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.semester'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'presensis' => $presensis->map(function($presensi) {
                    return [
                        'id' => $presensi->id,
                        'pertemuan' => $presensi->pertemuan,
                        'tanggal' => $presensi->tanggal->format('Y-m-d'),
                        'status' => $presensi->status,
                        'catatan' => $presensi->catatan,
                        'created_at' => $presensi->created_at->toISOString(),
                        'jadwal_kuliah' => [
                            'id' => $presensi->jadwalKuliah->id,
                            'mata_kuliah' => [
                                'id' => $presensi->jadwalKuliah->mataKuliah->id ?? null,
                                'nama' => $presensi->jadwalKuliah->mataKuliah->nama_mk ?? null,
                                'kode' => $presensi->jadwalKuliah->mataKuliah->kode_mk ?? null,
                            ],
                            'dosen' => [
                                'id' => $presensi->jadwalKuliah->dosen->id ?? null,
                                'nama' => $presensi->jadwalKuliah->dosen->nama ?? null,
                            ],
                            'semester' => [
                                'id' => $presensi->jadwalKuliah->semester->id ?? null,
                                'nama' => $presensi->jadwalKuliah->semester->nama_semester ?? null,
                            ],
                            'hari' => $presensi->jadwalKuliah->hari,
                            'ruangan' => $presensi->jadwalKuliah->ruangan,
                        ],
                    ];
                }),
                'pagination' => [
                    'current_page' => $presensis->currentPage(),
                    'last_page' => $presensis->lastPage(),
                    'total' => $presensis->total(),
                ],
            ],
        ]);
    }
}
