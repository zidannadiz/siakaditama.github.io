<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\Presensi;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassPresensiController extends Controller
{
    /**
     * Tampilkan halaman untuk join kelas
     */
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        return view('mahasiswa.class-presensi.index', compact('mahasiswa'));
    }

    /**
     * Join kelas dengan kode kelas
     */
    public function join(Request $request)
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
            'kode_kelas' => 'required|string|exists:class_sessions,kode_kelas',
        ]);

        // Cari session kelas
        $classSession = ClassSession::where('kode_kelas', $validated['kode_kelas'])
            ->where('status', 'buka')
            ->whereNull('closed_at')
            ->first();

        if (!$classSession) {
            return response()->json([
                'success' => false,
                'message' => 'Kode kelas tidak valid atau kelas sudah ditutup'
            ], 400);
        }

        // Cek apakah mahasiswa terdaftar di jadwal kuliah ini
        $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->first();

        if (!$krs) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar di kelas ini'
            ], 403);
        }

        // Cek apakah sudah pernah absen di pertemuan ini
        $presensi_existing = Presensi::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('pertemuan', $classSession->pertemuan)
            ->where('tanggal', $classSession->tanggal)
            ->first();

        if ($presensi_existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi untuk pertemuan ini'
            ], 400);
        }

        // Buat presensi
        $presensi = Presensi::create([
            'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
            'mahasiswa_id' => $mahasiswa->id,
            'pertemuan' => $classSession->pertemuan,
            'tanggal' => $classSession->tanggal,
            'status' => 'hadir',
            'catatan' => 'Presensi via Kelas Presensi',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil! Selamat datang di kelas.',
            'data' => [
                'presensi_id' => $presensi->id,
                'pertemuan' => $presensi->pertemuan,
                'tanggal' => $presensi->tanggal->format('d/m/Y'),
                'status' => $presensi->status,
                'mata_kuliah' => $classSession->jadwalKuliah->mataKuliah->nama_mk ?? '',
            ]
        ]);
    }
}
