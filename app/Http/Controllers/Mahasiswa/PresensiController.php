<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Models\Mahasiswa;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $jadwal_id = request('jadwal_id');
        
        // Ambil semua KRS yang sudah disetujui
        $krs_list = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'disetujui')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.semester'])
            ->get();

        $presensis = collect();
        $statistik = null;
        
        if ($jadwal_id) {
            // Pastikan jadwal ini milik mahasiswa ini
            $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
                ->where('jadwal_kuliah_id', $jadwal_id)
                ->where('status', 'disetujui')
                ->firstOrFail();

            // Ambil semua presensi untuk jadwal ini
            $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->orderBy('pertemuan')
                ->get();

            // Hitung statistik
            $statistik = [
                'hadir' => $presensis->where('status', 'hadir')->count(),
                'izin' => $presensis->where('status', 'izin')->count(),
                'sakit' => $presensis->where('status', 'sakit')->count(),
                'alpa' => $presensis->where('status', 'alpa')->count(),
                'total' => $presensis->count(),
            ];
        }

        return view('mahasiswa.presensi.index', compact('krs_list', 'presensis', 'statistik', 'jadwal_id'));
    }
}
