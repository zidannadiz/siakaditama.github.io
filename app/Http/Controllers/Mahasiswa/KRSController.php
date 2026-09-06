<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Models\JadwalKuliah;
use App\Models\Mahasiswa;
use App\Models\Semester;
use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KRSController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        $krs_list = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif->id)
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])
            ->get();

        $total_sks = $krs_list->sum(function($krs) {
            return $krs->jadwalKuliah->mataKuliah->sks ?? 0;
        });

        return view('mahasiswa.krs.index', compact('krs_list', 'semester_aktif', 'total_sks', 'mahasiswa'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        // Ambil jadwal yang sesuai dengan prodi dan semester mahasiswa
        $jadwal_available = JadwalKuliah::where('semester_id', $semester_aktif->id)
            ->where('status', 'aktif')
            ->whereHas('mataKuliah', function($query) use ($mahasiswa) {
                $query->where('prodi_id', $mahasiswa->prodi_id)
                      ->where('semester', $mahasiswa->semester); // Filter by student's semester
            })
            ->with(['mataKuliah', 'dosen'])
            ->get()
            ->filter(function($jadwal) {
                return $jadwal->terisi < $jadwal->kuota;
            });

        // Ambil jadwal yang sudah diambil
        $krs_terambil = KRS::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester_id', $semester_aktif->id)
            ->pluck('jadwal_kuliah_id')
            ->toArray();

        $jadwal_available = $jadwal_available->reject(function($jadwal) use ($krs_terambil) {
            return in_array($jadwal->id, $krs_terambil);
        });

        // Pass mataKuliah mapping to view to match user instructions implicitly if they look for $mataKuliah
        // But passing $jadwal_available is what the view needs.

        return view('mahasiswa.krs.create', compact('jadwal_available', 'semester_aktif', 'mahasiswa'));
    }

    public function store(Request $request)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if (!$mahasiswa) {
            abort(404, 'Data mahasiswa tidak ditemukan');
        }

        $semester_aktif = Semester::where('status', 'aktif')->first();

        if (!$semester_aktif) {
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Tidak ada semester aktif.');
        }

        // Validate multiple checkboxes
        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|array|min:1',
            'jadwal_kuliah_id.*' => 'exists:jadwal_kuliahs,id',
        ], [
            'jadwal_kuliah_id.required' => 'Pilih minimal satu mata kuliah untuk diambil.',
        ]);

        $berhasil = 0;
        $ditolak = [];

        foreach ($validated['jadwal_kuliah_id'] as $id) {
            // Cek apakah sudah pernah mengambil
            $existing = KRS::where('mahasiswa_id', $mahasiswa->id)
                ->where('jadwal_kuliah_id', $id)
                ->where('semester_id', $semester_aktif->id)
                ->first();

            if ($existing) {
                continue; // Skip jika sudah diambil
            }

            // Cek kuota
            $jadwal = JadwalKuliah::with('mataKuliah')->findOrFail($id);
            if ($jadwal->terisi >= $jadwal->kuota) {
                $ditolak[] = $jadwal->mataKuliah->nama_mk . ' (Penuh)';
                continue;
            }

            // Create KRS
            KRS::create([
                'mahasiswa_id' => $mahasiswa->id,
                'jadwal_kuliah_id' => $id,
                'semester_id' => $semester_aktif->id,
                'status' => 'pending',
            ]);

            // Update terisi
            $jadwal->increment('terisi');
            $berhasil++;
        }

        // Notifikasi ke admin (satu kali)
        if ($berhasil > 0) {
            try {
                $adminUsers = \App\Models\User::whereIn('role', ['admin', 'admin_pt', 'admin_prodi'])->get();
                foreach ($adminUsers as $admin) {
                    // Jika admin_prodi, pastikan prodi-nya sama
                    if ($admin->role === 'admin_prodi' && $admin->prodi_id !== $mahasiswa->prodi_id) {
                        continue;
                    }
                    
                    \App\Models\Notifikasi::create([
                        'user_id' => $admin->id,
                        'judul'   => 'Pengajuan KRS Baru',
                        'pesan'   => "Mahasiswa {$mahasiswa->nama} ({$mahasiswa->nim}) mengajukan {$berhasil} mata kuliah baru.",
                        'tipe'    => 'info',
                        'link'    => route('admin.krs.index'),
                        'is_read' => false,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error creating notification for KRS: ' . $e->getMessage());
            }
        }

        $message = "KRS berhasil diajukan ({$berhasil} mata kuliah). Menunggu persetujuan.";
        if (count($ditolak) > 0) {
            $message .= " Beberapa mata kuliah gagal ditambahkan: " . implode(', ', $ditolak);
        }

        return redirect()->route('mahasiswa.krs.index')->with('success', $message);
    }

    public function destroy(KRS $krs)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        
        if ($krs->mahasiswa_id !== $mahasiswa->id) {
            abort(403, 'Unauthorized');
        }

        // Update terisi
        $krs->jadwalKuliah->decrement('terisi');

        $krs->delete();

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'KRS berhasil dihapus.');
    }
}

