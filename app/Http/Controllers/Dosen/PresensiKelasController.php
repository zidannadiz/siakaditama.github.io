<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\KRS;
use App\Models\ClassSession;
use App\Models\ClassAttendance;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresensiKelasController extends Controller
{
    /**
     * Tampilkan daftar jadwal untuk membuka kelas
     */
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        // Ambil semua jadwal kuliah untuk dosen ini
        $jadwals = JadwalKuliah::where('dosen_id', $dosen->id)
            ->with(['mataKuliah', 'semester'])
            ->orderBy('semester_id', 'desc')
            ->orderBy('hari', 'asc')
            ->get();

        // Ambil kelas yang sedang aktif
        $activeClasses = ClassSession::where('dosen_id', $dosen->id)
            ->where('status', 'buka')
            ->whereNull('closed_at')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.semester'])
            ->get();

        return view('dosen.presensi-kelas.index', compact('jadwals', 'activeClasses'));
    }

    /**
     * Buka kelas baru
     */
    public function bukaKelas(Request $request, $jadwal_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $validated = $request->validate([
            'pertemuan' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);

        // Pastikan jadwal kuliah milik dosen ini
        $jadwal = JadwalKuliah::where('id', $jadwal_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        // Cek apakah ada kelas yang masih aktif untuk jadwal ini
        $activeSession = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
            ->where('status', 'buka')
            ->whereNull('closed_at')
            ->first();

        if ($activeSession) {
            return back()->withErrors(['error' => 'Kelas untuk jadwal ini masih aktif. Silakan tutup terlebih dahulu.'])
                ->withInput();
        }

        // Cek apakah sudah ada kelas untuk pertemuan dan tanggal yang sama
        $existingSession = ClassSession::where('jadwal_kuliah_id', $jadwal_id)
            ->where('pertemuan', $validated['pertemuan'])
            ->where('tanggal', $validated['tanggal'])
            ->first();

        if ($existingSession) {
            return back()->withErrors(['error' => 'Kelas untuk pertemuan dan tanggal ini sudah pernah dibuat.'])
                ->withInput();
        }

        // Buat session kelas
        $classSession = ClassSession::create([
            'jadwal_kuliah_id' => $jadwal_id,
            'dosen_id' => $dosen->id,
            'pertemuan' => $validated['pertemuan'],
            'tanggal' => $validated['tanggal'],
            'kode_kelas' => ClassSession::generateKodeKelas(),
            'status' => 'buka',
            'started_at' => now(),
        ]);

        return redirect()->route('dosen.presensi-kelas.show', $classSession->id)
            ->with('success', 'Kelas berhasil dibuka! Bagikan kode kelas kepada mahasiswa.');
    }

    /**
     * Tampilkan kelas yang sedang aktif
     */
    public function showKelas($class_session_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $classSession = ClassSession::where('id', $class_session_id)
            ->where('dosen_id', $dosen->id)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.semester', 'dosen'])
            ->firstOrFail();

        // Ambil semua mahasiswa yang terdaftar di jadwal kuliah ini
        $krs_list = KRS::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->with('mahasiswa')
            ->get();

        // Ambil semua attendance untuk kelas ini
        $attendances = ClassAttendance::where('class_session_id', $classSession->id)
            ->with('mahasiswa')
            ->orderBy('waktu_masuk', 'desc')
            ->get()
            ->keyBy('mahasiswa_id');

        return view('dosen.presensi-kelas.show', compact('classSession', 'krs_list', 'attendances'));
    }

    /**
     * Tutup kelas
     */
    public function tutupKelas($class_session_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $classSession = ClassSession::where('id', $class_session_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        if ($classSession->status === 'tutup') {
            return back()->with('info', 'Kelas sudah ditutup.');
        }

        // Buat presensi untuk semua yang hadir
        $attendances = ClassAttendance::where('class_session_id', $classSession->id)
            ->where('status', 'hadir')
            ->where('is_kicked', false)
            ->get();

        foreach ($attendances as $attendance) {
            // Cek apakah sudah ada presensi
            $existingPresensi = Presensi::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
                ->where('mahasiswa_id', $attendance->mahasiswa_id)
                ->where('pertemuan', $classSession->pertemuan)
                ->where('tanggal', $classSession->tanggal)
                ->first();

            if (!$existingPresensi) {
                Presensi::create([
                    'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                    'mahasiswa_id' => $attendance->mahasiswa_id,
                    'pertemuan' => $classSession->pertemuan,
                    'tanggal' => $classSession->tanggal,
                    'status' => $attendance->status,
                    'catatan' => 'Presensi via Kelas Presensi',
                ]);
            }
        }

        // Mark yang tidak hadir sebagai alpa
        $krs_list = KRS::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
            ->where('status', 'disetujui')
            ->pluck('mahasiswa_id');

        $attended_ids = ClassAttendance::where('class_session_id', $classSession->id)
            ->where('is_kicked', false)
            ->pluck('mahasiswa_id');

        $absent_ids = $krs_list->diff($attended_ids);

        foreach ($absent_ids as $mahasiswa_id) {
            $existingPresensi = Presensi::where('jadwal_kuliah_id', $classSession->jadwal_kuliah_id)
                ->where('mahasiswa_id', $mahasiswa_id)
                ->where('pertemuan', $classSession->pertemuan)
                ->where('tanggal', $classSession->tanggal)
                ->first();

            if (!$existingPresensi) {
                Presensi::create([
                    'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                    'mahasiswa_id' => $mahasiswa_id,
                    'pertemuan' => $classSession->pertemuan,
                    'tanggal' => $classSession->tanggal,
                    'status' => 'alpa',
                    'catatan' => 'Tidak hadir di kelas',
                ]);
            }
        }

        $classSession->close();

        return redirect()->route('dosen.presensi-kelas.index')
            ->with('success', 'Kelas berhasil ditutup dan presensi telah disimpan.');
    }

    /**
     * Kick mahasiswa dari kelas
     */
    public function kickMahasiswa($class_session_id, $mahasiswa_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $classSession = ClassSession::where('id', $class_session_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $attendance = ClassAttendance::where('class_session_id', $classSession->id)
            ->where('mahasiswa_id', $mahasiswa_id)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Mahasiswa tidak ditemukan di kelas ini.');
        }

        $attendance->markAsKicked('Dikeluarkan oleh dosen karena tidak hadir di ruangan');

        // Sinkronkan ke Presensi - update status menjadi alpa
        Presensi::updateOrCreate(
            [
                'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                'mahasiswa_id' => $mahasiswa_id,
                'pertemuan' => $classSession->pertemuan,
            ],
            [
                'tanggal' => $classSession->tanggal,
                'status' => 'alpa',
                'catatan' => 'Dikeluarkan dari kelas - tidak hadir di ruangan',
            ]
        );

        return back()->with('success', 'Mahasiswa berhasil dikeluarkan dari kelas.');
    }

    /**
     * Update status presensi (dari alpa menjadi izin/sakit)
     */
    public function updateStatus(Request $request, $class_attendance_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $validated = $request->validate([
            'status' => 'required|in:izin,sakit',
            'catatan' => 'nullable|string',
        ]);

        $attendance = ClassAttendance::findOrFail($class_attendance_id);
        
        // Pastikan attendance milik kelas dosen ini
        $classSession = ClassSession::where('id', $attendance->class_session_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $attendance->updateStatus($validated['status'], $validated['catatan'] ?? null);

        // Sinkronkan ke Presensi
        Presensi::updateOrCreate(
            [
                'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                'mahasiswa_id' => $attendance->mahasiswa_id,
                'pertemuan' => $classSession->pertemuan,
            ],
            [
                'tanggal' => $classSession->tanggal,
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? 'Presensi via Kelas Presensi',
            ]
        );

        return back()->with('success', 'Status presensi berhasil diupdate dan disinkronkan.');
    }

    /**
     * Get peserta kelas (AJAX)
     */
    public function getPeserta($class_session_id)
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $classSession = ClassSession::where('id', $class_session_id)
            ->where('dosen_id', $dosen->id)
            ->firstOrFail();

        $attendances = ClassAttendance::where('class_session_id', $classSession->id)
            ->with('mahasiswa')
            ->orderBy('waktu_masuk', 'desc')
            ->get();

        return response()->json($attendances);
    }
}

