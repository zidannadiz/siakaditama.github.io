<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\Presensi;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassPresensiController extends Controller
{
    /**
     * Tampilkan daftar semua session kelas yang pernah dibuat
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

        $classSessions = collect();
        
        if ($jadwal_id) {
            $classSessions = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
                ->where('dosen_id', $dosen->id)
                ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.semester'])
                ->orderBy('tanggal', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return view('dosen.class-presensi.index', compact('jadwals', 'classSessions', 'jadwal_id'));
    }

    /**
     * Tampilkan form untuk membuka kelas baru
     */
    public function create()
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

        $selectedJadwal = null;
        $pertemuan_terakhir = 0;
        
        if ($jadwal_id) {
            $selectedJadwal = JadwalKuliah::where('id', $jadwal_id)
                ->where('dosen_id', $dosen->id)
                ->with(['mataKuliah', 'semester'])
                ->firstOrFail();
            
            // Cek apakah ada kelas yang masih aktif
            $activeSession = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
                ->where('status', 'buka')
                ->whereNull('closed_at')
                ->first();

            if ($activeSession) {
                return redirect()->route('dosen.class-presensi.show', $activeSession->id)
                    ->with('info', 'Kelas untuk jadwal ini masih aktif. Silakan tutup terlebih dahulu jika ingin membuka kelas baru.');
            }

            // Ambil pertemuan terakhir
            $pertemuan_presensi = Presensi::where('jadwal_kuliah_id', $jadwal_id)
                ->max('pertemuan') ?? 0;
            $pertemuan_session = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
                ->max('pertemuan') ?? 0;
            
            $pertemuan_terakhir = max($pertemuan_presensi, $pertemuan_session);
        }

        return view('dosen.class-presensi.create', compact('jadwals', 'selectedJadwal', 'jadwal_id', 'pertemuan_terakhir'));
    }

    /**
     * Simpan kelas baru
     */
    public function store(Request $request)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
            'pertemuan' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        // Pastikan jadwal kuliah milik dosen ini
        $jadwal = JadwalKuliah::where('id', $validated['jadwal_kuliah_id'])
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        // Cek apakah ada kelas yang masih aktif untuk jadwal ini
        $activeSession = ClassSession::where('jadwal_kuliah_id', $validated['jadwal_kuliah_id'])
            ->where('status', 'buka')
            ->whereNull('closed_at')
            ->first();

        if ($activeSession) {
            return back()->withErrors(['error' => 'Kelas untuk jadwal ini masih aktif. Silakan tutup terlebih dahulu.'])
                ->withInput();
        }

        // Cek apakah sudah ada kelas untuk pertemuan dan tanggal yang sama
        $existingSession = ClassSession::where('jadwal_kuliah_id', $validated['jadwal_kuliah_id'])
            ->where('pertemuan', $validated['pertemuan'])
            ->where('tanggal', $validated['tanggal'])
            ->first();

        if ($existingSession) {
            return back()->withErrors(['error' => 'Kelas untuk pertemuan dan tanggal ini sudah pernah dibuat.'])
                ->withInput();
        }

        // Buat session kelas
        $classSession = ClassSession::create([
            'jadwal_kuliah_id' => $validated['jadwal_kuliah_id'],
            'dosen_id' => $dosen->id,
            'pertemuan' => $validated['pertemuan'],
            'tanggal' => $validated['tanggal'],
            'kode_kelas' => ClassSession::generateKodeKelas(),
            'status' => 'buka',
            'started_at' => now(),
        ]);

        return redirect()->route('dosen.class-presensi.show', $classSession->id)
            ->with('success', 'Kelas berhasil dibuka! Bagikan kode kelas kepada mahasiswa.');
    }

    /**
     * Tampilkan detail kelas yang sedang berlangsung
     */
    public function show($id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $classSession = ClassSession::where('id', $id)
            ->where('dosen_id', $dosen->id)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.semester', 'dosen'])
            ->firstOrFail();

        // Ambil semua mahasiswa yang terdaftar di jadwal kuliah ini
        $krs_list = KRS::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        // Ambil semua presensi untuk pertemuan ini
        $presensis = Presensi::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('pertemuan', $classSession->pertemuan)
            ->where('tanggal', $classSession->tanggal)
            ->with('mahasiswa')
            ->get()
            ->keyBy('mahasiswa_id');

        return view('dosen.class-presensi.show', compact('classSession', 'krs_list', 'presensis'));
    }

    /**
     * Tutup kelas
     */
    public function close($id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $classSession = ClassSession::where('id', $id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        if ($classSession->status === 'tutup') {
            return back()->with('info', 'Kelas sudah ditutup.');
        }

        $classSession->close();

        return redirect()->route('dosen.class-presensi.index', ['jadwal_id' => $classSession->jadwal_kuliah_id])
            ->with('success', 'Kelas berhasil ditutup.');
    }

    /**
     * Kick mahasiswa dari kelas (hapus presensi)
     */
    public function kick(Request $request, $id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $classSession = ClassSession::where('id', $id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
        ]);

        // Hapus presensi mahasiswa
        $presensi = Presensi::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('mahasiswa_id', $validated['mahasiswa_id'])
            ->where('pertemuan', $classSession->pertemuan)
            ->where('tanggal', $classSession->tanggal)
            ->first();

        if ($presensi) {
            $presensi->delete();
            return back()->with('success', 'Mahasiswa berhasil dikeluarkan dari kelas.');
        }

        return back()->with('error', 'Presensi mahasiswa tidak ditemukan.');
    }
}
