<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\Semester;
use Illuminate\Http\Request;

class JadwalKuliahController extends Controller
{
    public function index()
    {
        $jadwalKuliahs = JadwalKuliah::with(['mataKuliah', 'dosen', 'semester'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'jadwal_kuliahs' => $jadwalKuliahs->map(function($jadwal) {
                    return [
                        'id' => $jadwal->id,
                        'mata_kuliah' => $jadwal->mataKuliah->nama ?? null,
                        'dosen' => $jadwal->dosen->nama ?? null,
                        'semester' => $jadwal->semester->nama ?? null,
                        'hari' => $jadwal->hari,
                        'jam_mulai' => $jadwal->jam_mulai,
                        'jam_selesai' => $jadwal->jam_selesai,
                        'ruangan' => $jadwal->ruangan,
                        'kuota' => $jadwal->kuota,
                        'terisi' => $jadwal->terisi,
                        'status' => $jadwal->status,
                    ];
                }),
                'pagination' => [
                    'current_page' => $jadwalKuliahs->currentPage(),
                    'last_page' => $jadwalKuliahs->lastPage(),
                    'total' => $jadwalKuliahs->total(),
                ],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['terisi'] = 0;

        $jadwal = JadwalKuliah::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal Kuliah berhasil ditambahkan.',
            'data' => [
                'id' => $jadwal->id,
            ],
        ], 201);
    }

    public function show(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load(['mataKuliah', 'dosen', 'semester']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jadwalKuliah->id,
                'mata_kuliah' => $jadwalKuliah->mataKuliah->nama ?? null,
                'dosen' => $jadwalKuliah->dosen->nama ?? null,
                'semester' => $jadwalKuliah->semester->nama ?? null,
                'hari' => $jadwalKuliah->hari,
                'jam_mulai' => $jadwalKuliah->jam_mulai,
                'jam_selesai' => $jadwalKuliah->jam_selesai,
                'ruangan' => $jadwalKuliah->ruangan,
                'kuota' => $jadwalKuliah->kuota,
                'terisi' => $jadwalKuliah->terisi,
                'status' => $jadwalKuliah->status,
            ],
        ]);
    }

    public function update(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $jadwalKuliah->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal Kuliah berhasil diperbarui.',
        ]);
    }

    public function destroy(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal Kuliah berhasil dihapus.',
        ]);
    }
}

