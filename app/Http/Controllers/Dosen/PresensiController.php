<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal_id = request('jadwal_id');
        $pertemuan = request('pertemuan');
        
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('status', 'aktif')
            ->with(['mataKuliah', 'semester'])
            ->get();

        $presensis = collect();
        $krs_list = collect();
        
        if ($jadwal_id) {
            $jadwal = JadwalKuliah::findOrFail($jadwal_id);
            
            // Ambil daftar mahasiswa yang terdaftar di jadwal ini
            $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
                ->where('status', 'disetujui')
                ->with('mahasiswa')
                ->get();

            // Ambil presensi jika pertemuan dipilih
            if ($pertemuan) {
                $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                    ->where('pertemuan', $pertemuan)
                    ->with('mahasiswa')
                    ->get()
                    ->keyBy('mahasiswa_id');
            }
        }

        return view('dosen.presensi.index', compact('jadwals', 'krs_list', 'presensis', 'jadwal_id', 'pertemuan'));
    }

    public function create($jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        // Ambil pertemuan terakhir untuk suggest pertemuan berikutnya
        $pertemuan_terakhir = Presensi::where('jadwal_kuliah_id', $jadwal_id)
            ->max('pertemuan') ?? 0;

        return view('dosen.presensi.create', compact('jadwal', 'krs_list', 'pertemuan_terakhir'));
    }

    public function store(Request $request, $jadwal_id)
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
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|exists:mahasiswas,id',
            'presensi.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'presensi.*.catatan' => 'nullable|string|max:255',
        ]);

        foreach ($validated['presensi'] as $presensi_data) {
            Presensi::updateOrCreate(
                [
                    'jadwal_kuliah_id' => $jadwal_id,
                    'mahasiswa_id' => $presensi_data['mahasiswa_id'],
                    'pertemuan' => $validated['pertemuan'],
                ],
                [
                    'tanggal' => $validated['tanggal'],
                    'status' => $presensi_data['status'],
                    'catatan' => $presensi_data['catatan'] ?? null,
                ]
            );
        }

        return redirect()->route('dosen.presensi.index', ['jadwal_id' => $jadwal_id, 'pertemuan' => $validated['pertemuan']])
            ->with('success', 'Presensi berhasil disimpan.');
    }

    public function show($jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->firstOrFail();

        // Ambil semua mahasiswa yang terdaftar
        $krs_list = KRS::where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        // Ambil semua presensi untuk jadwal ini
        $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
            ->with('mahasiswa')
            ->orderBy('pertemuan')
            ->orderBy('mahasiswa_id')
            ->get()
            ->groupBy('pertemuan');

        // Hitung statistik per mahasiswa
        $statistik = [];
        foreach ($krs_list as $krs) {
            $mahasiswa_id = $krs->mahasiswa_id;
            $presensi_mahasiswa = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                ->where('mahasiswa_id', $mahasiswa_id)
                ->get();

            $statistik[$mahasiswa_id] = [
                'mahasiswa' => $krs->mahasiswa,
                'hadir' => $presensi_mahasiswa->where('status', 'hadir')->count(),
                'izin' => $presensi_mahasiswa->where('status', 'izin')->count(),
                'sakit' => $presensi_mahasiswa->where('status', 'sakit')->count(),
                'alpa' => $presensi_mahasiswa->where('status', 'alpa')->count(),
                'total' => $presensi_mahasiswa->count(),
            ];
        }

        return view('dosen.presensi.show', compact('jadwal', 'krs_list', 'presensis', 'statistik'));
    }
}
