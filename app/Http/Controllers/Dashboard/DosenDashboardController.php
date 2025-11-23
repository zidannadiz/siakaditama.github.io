<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\Nilai;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenDashboardController extends Controller
{
    public function __invoke()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        
        if (!$dosen) {
            abort(404, 'Data dosen tidak ditemukan');
        }

        $jadwal_hari_ini = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('hari', now()->format('l'))
            ->where('status', 'aktif')
            ->with(['mataKuliah', 'semester'])
            ->get();

        $total_kelas = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('status', 'aktif')
            ->count();

        $nilai_belum_input = Nilai::where('dosen_id', $dosen->id)
            ->where('status', 'belum')
            ->count();

        $pengumuman_terbaru = Pengumuman::where(function($query) {
                $query->where('target', 'semua')
                    ->orWhere('target', 'dosen');
            })
            ->where(function($query) {
                $query->where('published_at', '<=', now())
                    ->orWhereNull('published_at');
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.dosen', compact('dosen', 'jadwal_hari_ini', 'total_kelas', 'nilai_belum_input', 'pengumuman_terbaru'));
    }
}

