<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KRS;
use App\Services\AuditLogService;
use App\Services\EmailNotificationService;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;

class KRSController extends Controller
{
    public function index()
    {
        $krs_list = KRS::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'semester'])
            ->latest()
            ->paginate(15);
        return view('admin.krs.index', compact('krs_list'));
    }

    public function approve(KRS $krs)
    {
        $oldStatus = $krs->status;
        $krs->update(['status' => 'disetujui']);

        // Log audit
        AuditLogService::logApprove(
            $krs,
            "Menyetujui KRS mahasiswa {$krs->mahasiswa->nama} untuk mata kuliah {$krs->jadwalKuliah->mataKuliah->nama_mk}"
        );

        // Buat notifikasi untuk mahasiswa
        NotifikasiService::create(
            $krs->mahasiswa->user_id,
            'KRS Disetujui',
            "KRS mata kuliah {$krs->jadwalKuliah->mataKuliah->nama_mk} telah disetujui.",
            'success',
            route('mahasiswa.krs.index')
        );

        // Kirim email notification
        $krs->load(['mahasiswa', 'jadwalKuliah.mataKuliah', 'semester']);
        EmailNotificationService::sendKrsApproved($krs, $krs->mahasiswa->user_id);

        return back()->with('success', 'KRS berhasil disetujui.');
    }

    public function reject(Request $request, KRS $krs)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        $krs->update([
            'status' => 'ditolak',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Kurangi terisi
        $krs->jadwalKuliah->decrement('terisi');

        // Log audit
        $reason = $validated['catatan'] ?? 'Tidak ada catatan';
        AuditLogService::logReject(
            $krs,
            "Menolak KRS mahasiswa {$krs->mahasiswa->nama} untuk mata kuliah {$krs->jadwalKuliah->mataKuliah->nama_mk}. Alasan: {$reason}"
        );

        // Buat notifikasi untuk mahasiswa
        $pesan = "KRS mata kuliah {$krs->jadwalKuliah->mataKuliah->nama_mk} ditolak.";
        if ($validated['catatan'] ?? null) {
            $pesan .= " Alasan: {$validated['catatan']}";
        }

        NotifikasiService::create(
            $krs->mahasiswa->user_id,
            'KRS Ditolak',
            $pesan,
            'error',
            route('mahasiswa.krs.index')
        );

        // Kirim email notification
        $krs->load(['mahasiswa', 'jadwalKuliah.mataKuliah', 'semester']);
        EmailNotificationService::sendKrsRejected(
            $krs, 
            $krs->mahasiswa->user_id, 
            $validated['catatan'] ?? null
        );

        return back()->with('success', 'KRS berhasil ditolak.');
    }
}

