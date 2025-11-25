<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\KRS;
use App\Models\ClassSession;
use App\Models\ClassAttendance;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresensiKelasController extends Controller
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

        // Ambil kelas aktif yang bisa di-join
        $activeClasses = ClassSession::where('status', 'buka')
            ->whereNull('closed_at')
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.semester', 'dosen'])
            ->get()
            ->filter(function($class) use ($mahasiswa) {
                // Cek apakah mahasiswa terdaftar di jadwal kuliah ini
                $krs = KRS::where('mahasiswa_id', $mahasiswa->id)
                    ->where('jadwal_kuliah_id', $class->jadwal_kuliah_id)
                    ->where('status', 'disetujui')
                    ->first();
                return $krs !== null;
            });

        return view('mahasiswa.presensi-kelas.index', compact('mahasiswa', 'activeClasses'));
    }

    /**
     * Join kelas dengan kode kelas
     */
    public function joinKelas(Request $request)
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

        // Cari session kelas dengan kode
        $classSession = ClassSession::where('kode_kelas', strtoupper($validated['kode_kelas']))
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

        // Cek apakah sudah pernah join kelas ini
        $attendance_existing = ClassAttendance::where('class_session_id', $classSession->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if ($attendance_existing) {
            if ($attendance_existing->is_kicked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda telah dikeluarkan dari kelas ini'
                ], 400);
            }
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah bergabung dengan kelas ini'
            ], 400);
        }

        // Buat attendance (auto-absensi)
        $attendance = ClassAttendance::create([
            'class_session_id' => $classSession->id,
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'hadir',
            'waktu_masuk' => now(),
            'catatan' => 'Bergabung dengan kelas',
        ]);

        // Sinkronkan langsung ke Presensi
        Presensi::updateOrCreate(
            [
                'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                'mahasiswa_id' => $mahasiswa->id,
                'pertemuan' => $classSession->pertemuan,
            ],
            [
                'tanggal' => $classSession->tanggal,
                'status' => 'hadir',
                'catatan' => 'Presensi via Kelas Presensi',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Berhasil bergabung dengan kelas!',
            'data' => [
                'attendance_id' => $attendance->id,
                'pertemuan' => $classSession->pertemuan,
                'tanggal' => $classSession->tanggal->format('d/m/Y'),
                'status' => $attendance->status,
                'mata_kuliah' => $classSession->jadwalKuliah->mataKuliah->nama_mk ?? '',
            ]
        ]);
    }

    /**
     * Tampilkan riwayat presensi kelas
     */
    public function history()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        // Ambil semua attendance mahasiswa ini
        $attendances = ClassAttendance::where('mahasiswa_id', $mahasiswa->id)
            ->with(['classSession.jadwalKuliah.mataKuliah', 'classSession.jadwalKuliah.semester', 'classSession.dosen'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('mahasiswa.presensi-kelas.history', compact('attendances'));
    }

    /**
     * Konfirmasi izin (untuk yang tidak hadir)
     */
    public function konfirmasiIzin(Request $request, $class_attendance_id)
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
            'catatan' => 'nullable|string|max:500',
        ]);

        $attendance = ClassAttendance::where('id', $class_attendance_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        // Hanya bisa konfirmasi jika status masih alpa atau belum ada attendance
        if ($attendance->status === 'izin' || $attendance->status === 'sakit') {
            return response()->json([
                'success' => false,
                'message' => 'Status sudah pernah dikonfirmasi'
            ], 400);
        }

        $attendance->updateStatus('izin', $validated['catatan'] ?? null);

        // Sinkronkan ke Presensi
        $classSession = $attendance->classSession;
        Presensi::updateOrCreate(
            [
                'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                'mahasiswa_id' => $attendance->mahasiswa_id,
                'pertemuan' => $classSession->pertemuan,
            ],
            [
                'tanggal' => $classSession->tanggal,
                'status' => 'izin',
                'catatan' => $validated['catatan'] ?? 'Izin dikonfirmasi mahasiswa',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Izin berhasil dikonfirmasi dan disinkronkan'
        ]);
    }

    /**
     * Konfirmasi sakit (untuk yang tidak hadir)
     */
    public function konfirmasiSakit(Request $request, $class_attendance_id)
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
            'catatan' => 'nullable|string|max:500',
        ]);

        $attendance = ClassAttendance::where('id', $class_attendance_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        // Hanya bisa konfirmasi jika status masih alpa atau belum ada attendance
        if ($attendance->status === 'izin' || $attendance->status === 'sakit') {
            return response()->json([
                'success' => false,
                'message' => 'Status sudah pernah dikonfirmasi'
            ], 400);
        }

        $attendance->updateStatus('sakit', $validated['catatan'] ?? null);

        // Sinkronkan ke Presensi
        $classSession = $attendance->classSession;
        Presensi::updateOrCreate(
            [
                'jadwal_kuliah_id' => $classSession->jadwal_kuliah_id,
                'mahasiswa_id' => $attendance->mahasiswa_id,
                'pertemuan' => $classSession->pertemuan,
            ],
            [
                'tanggal' => $classSession->tanggal,
                'status' => 'sakit',
                'catatan' => $validated['catatan'] ?? 'Sakit dikonfirmasi mahasiswa',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Sakit berhasil dikonfirmasi dan disinkronkan'
        ]);
    }
}

