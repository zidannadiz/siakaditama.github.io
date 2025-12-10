<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use Illuminate\Http\Request;
use App\Services\NotifikasiService;

class KRSController extends Controller
{
    public function index()
    {
        $krs_list = KRS::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'semester'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'krs_list' => $krs_list->map(function($krs) {
                    return [
                        'id' => $krs->id,
                        'mahasiswa' => [
                            'id' => $krs->mahasiswa->id ?? null,
                            'nim' => $krs->mahasiswa->nim ?? null,
                            'nama' => $krs->mahasiswa->nama ?? null,
                        ],
                        'mata_kuliah' => $krs->jadwalKuliah->mataKuliah->nama ?? null,
                        'dosen' => $krs->jadwalKuliah->dosen->nama ?? null,
                        'semester' => $krs->semester->nama ?? null,
                        'status' => $krs->status,
                        'created_at' => $krs->created_at->toISOString(),
                    ];
                }),
            ],
        ]);
    }

    public function approve(KRS $krs)
    {
        $krs->load(['mahasiswa', 'jadwalKuliah.mataKuliah']);
        
        $krs->update(['status' => 'disetujui']);

        // Increment terisi
        $krs->jadwalKuliah->increment('terisi');

        // Buat notifikasi untuk mahasiswa
        if ($krs->mahasiswa && $krs->mahasiswa->user_id) {
            NotifikasiService::create(
                $krs->mahasiswa->user_id,
                'KRS Disetujui',
                "KRS mata kuliah {$krs->jadwalKuliah->mataKuliah->nama} telah disetujui.",
                'success',
                '/mahasiswa/krs'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil disetujui.',
        ]);
    }

    public function reject(Request $request, KRS $krs)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        $krs->load(['mahasiswa', 'jadwalKuliah.mataKuliah']);

        $krs->update([
            'status' => 'ditolak',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Kurangi terisi
        $krs->jadwalKuliah->decrement('terisi');

        // Buat notifikasi untuk mahasiswa
        if ($krs->mahasiswa && $krs->mahasiswa->user_id) {
            $pesan = "KRS mata kuliah {$krs->jadwalKuliah->mataKuliah->nama} ditolak.";
            if ($validated['catatan'] ?? null) {
                $pesan .= " Alasan: {$validated['catatan']}";
            }

            NotifikasiService::create(
                $krs->mahasiswa->user_id,
                'KRS Ditolak',
                $pesan,
                'error',
                '/mahasiswa/krs'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil ditolak.',
        ]);
    }

    /**
     * Get KRS details
     */
    public function show(KRS $krs)
    {
        $krs->load([
            'mahasiswa',
            'jadwalKuliah.mataKuliah',
            'jadwalKuliah.dosen',
            'semester',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $krs->id,
                'mahasiswa' => [
                    'id' => $krs->mahasiswa->id ?? null,
                    'nim' => $krs->mahasiswa->nim ?? null,
                    'nama' => $krs->mahasiswa->nama ?? null,
                    'prodi' => $krs->mahasiswa->prodi->nama ?? null,
                ],
                'mata_kuliah' => [
                    'id' => $krs->jadwalKuliah->mataKuliah->id ?? null,
                    'kode_mk' => $krs->jadwalKuliah->mataKuliah->kode_mk ?? null,
                    'nama' => $krs->jadwalKuliah->mataKuliah->nama ?? null,
                    'sks' => $krs->jadwalKuliah->mataKuliah->sks ?? null,
                ],
                'dosen' => [
                    'id' => $krs->jadwalKuliah->dosen->id ?? null,
                    'nama' => $krs->jadwalKuliah->dosen->nama ?? null,
                ],
                'semester' => [
                    'id' => $krs->semester->id ?? null,
                    'nama' => $krs->semester->nama ?? null,
                ],
                'status' => $krs->status,
                'catatan' => $krs->catatan,
                'created_at' => $krs->created_at->toISOString(),
            ],
        ]);
    }
}

