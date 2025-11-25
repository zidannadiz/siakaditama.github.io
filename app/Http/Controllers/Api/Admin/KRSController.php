<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use Illuminate\Http\Request;

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
        $krs->update(['status' => 'disetujui']);

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil disetujui.',
        ]);
    }

    public function reject(KRS $krs)
    {
        $krs->update(['status' => 'ditolak']);
        $krs->jadwalKuliah->decrement('terisi');

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil ditolak.',
        ]);
    }
}

