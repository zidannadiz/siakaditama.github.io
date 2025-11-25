<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\Presensi;
use App\Models\ClassSession;
use App\Models\ClassAttendance;
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
        
        // Ambil semua jadwal kuliah untuk dosen ini, tanpa filter status
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->orderBy('semester_id', 'desc')
            ->orderBy('hari', 'asc')
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
            $presensi = Presensi::updateOrCreate(
                [
                    'jadwal_kuliah_id' => $jadwal_id,
                    'mahasiswa_id' => $presensi_data['mahasiswa_id'],
                    'pertemuan' => $validated['pertemuan'],
                ],
                [
                    'tanggal' => $validated['tanggal'],
                    'status' => $presensi_data['status'],
                    'catatan' => $presensi_data['catatan'] ?? 'Presensi Manual',
                ]
            );

            // Sinkronkan ke ClassAttendance jika ada ClassSession untuk pertemuan dan tanggal yang sama
            $classSession = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
                ->where('pertemuan', $validated['pertemuan'])
                ->where('tanggal', $validated['tanggal'])
                ->first();

            if ($classSession) {
                ClassAttendance::updateOrCreate(
                    [
                        'class_session_id' => $classSession->id,
                        'mahasiswa_id' => $presensi_data['mahasiswa_id'],
                    ],
                    [
                        'status' => $presensi_data['status'],
                        'catatan' => $presensi_data['catatan'] ?? 'Presensi Manual',
                        'waktu_masuk' => $presensi_data['status'] === 'hadir' ? now() : null,
                        'is_kicked' => false,
                    ]
                );
            }
        }

        return redirect()->route('dosen.presensi.index', ['jadwal_id' => $jadwal_id, 'pertemuan' => $validated['pertemuan']])
            ->with('success', 'Presensi berhasil disimpan dan disinkronkan.');
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

    public function edit($jadwal_id, $pertemuan)
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

        // Ambil presensi untuk pertemuan ini
        $presensis = Presensi::where('jadwal_kuliah_id', $jadwal_id)
            ->where('pertemuan', $pertemuan)
            ->with('mahasiswa')
            ->get()
            ->keyBy('mahasiswa_id');

        // Ambil tanggal dari presensi pertama (semua presensi pertemuan sama seharusnya punya tanggal yang sama)
        $tanggal = $presensis->first()?->tanggal ?? now();

        return view('dosen.presensi.edit', compact('jadwal', 'krs_list', 'presensis', 'pertemuan', 'tanggal'));
    }

    public function update(Request $request, $jadwal_id, $pertemuan)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|exists:mahasiswas,id',
            'presensi.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'presensi.*.catatan' => 'nullable|string|max:255',
        ]);

        foreach ($validated['presensi'] as $presensi_data) {
            $presensi = Presensi::updateOrCreate(
                [
                    'jadwal_kuliah_id' => $jadwal_id,
                    'mahasiswa_id' => $presensi_data['mahasiswa_id'],
                    'pertemuan' => $pertemuan,
                ],
                [
                    'tanggal' => $validated['tanggal'],
                    'status' => $presensi_data['status'],
                    'catatan' => $presensi_data['catatan'] ?? 'Presensi Manual',
                ]
            );

            // Sinkronkan ke ClassAttendance jika ada ClassSession untuk pertemuan dan tanggal yang sama
            $classSession = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
                ->where('pertemuan', $pertemuan)
                ->where('tanggal', $validated['tanggal'])
                ->first();

            if ($classSession) {
                ClassAttendance::updateOrCreate(
                    [
                        'class_session_id' => $classSession->id,
                        'mahasiswa_id' => $presensi_data['mahasiswa_id'],
                    ],
                    [
                        'status' => $presensi_data['status'],
                        'catatan' => $presensi_data['catatan'] ?? 'Presensi Manual',
                        'waktu_masuk' => $presensi_data['status'] === 'hadir' ? now() : null,
                        'is_kicked' => false,
                    ]
                );
            }
        }

        return redirect()->route('dosen.presensi.index', ['jadwal_id' => $jadwal_id, 'pertemuan' => $pertemuan])
            ->with('success', 'Presensi berhasil diupdate dan disinkronkan.');
    }
}
