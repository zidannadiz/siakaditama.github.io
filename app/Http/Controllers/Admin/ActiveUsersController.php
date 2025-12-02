<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActiveUsersController extends Controller
{
    public function index()
    {
        // Ambil session lifetime dari config (dalam menit)
        $sessionLifetime = config('session.lifetime', 120); // default 120 menit (2 jam)
        $sessionLifetimeSeconds = $sessionLifetime * 60;
        
        // Hitung waktu minimum untuk dianggap aktif (dalam detik timestamp Unix)
        $minLastActivity = now()->timestamp - $sessionLifetimeSeconds;
        
        // Ambil semua session yang aktif (user_id tidak null dan last_activity masih dalam waktu lifetime)
        // Filter hanya untuk dosen dan mahasiswa
        $activeSessions = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->whereNotNull('sessions.user_id')
            ->where('sessions.last_activity', '>=', $minLastActivity)
            ->whereIn('users.role', ['dosen', 'mahasiswa'])
            ->select(
                'sessions.id as session_id',
                'sessions.user_id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name',
                'users.email',
                'users.role'
            )
            ->orderBy('sessions.last_activity', 'desc')
            ->get();
        
        // Ambil informasi dosen dan mahasiswa untuk setiap user
        $activeUsers = [];
        
        foreach ($activeSessions as $session) {
            // Cek apakah user adalah dosen atau mahasiswa dan ambil data tambahannya
            $additionalInfo = null;
            
            if ($session->role === 'dosen') {
                $dosen = DB::table('dosens')
                    ->where('user_id', $session->user_id)
                    ->first();
                
                if ($dosen) {
                    $additionalInfo = [
                        'nidn' => $dosen->nidn ?? '-',
                        'prodi' => '-' // Dosen tidak memiliki prodi_id langsung
                    ];
                }
            } elseif ($session->role === 'mahasiswa') {
                $mahasiswa = DB::table('mahasiswas')
                    ->leftJoin('prodis', 'mahasiswas.prodi_id', '=', 'prodis.id')
                    ->where('mahasiswas.user_id', $session->user_id)
                    ->select('mahasiswas.nim', 'prodis.nama as prodi_nama')
                    ->first();
                
                if ($mahasiswa) {
                    $additionalInfo = [
                        'nim' => $mahasiswa->nim ?? '-',
                        'prodi' => $mahasiswa->prodi_nama ?? '-'
                    ];
                }
            }
            
            // Konversi last_activity dari Unix timestamp ke Carbon instance
            $lastActivity = Carbon::createFromTimestamp($session->last_activity);
            
            // Format tanggal dengan nama bulan dalam bahasa Indonesia
            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            $hari = [
                'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            
            $lastActivityFormatted = sprintf(
                '%s, %d %s %d, %02d:%02d:%02d',
                $hari[$lastActivity->format('l')] ?? $lastActivity->format('l'),
                $lastActivity->day,
                $bulan[(int)$lastActivity->month] ?? $lastActivity->format('F'),
                $lastActivity->year,
                $lastActivity->hour,
                $lastActivity->minute,
                $lastActivity->second
            );
            
            $activeUsers[] = [
                'session_id' => $session->session_id,
                'user_id' => $session->user_id,
                'name' => $session->name,
                'email' => $session->email,
                'role' => $session->role,
                'ip_address' => $session->ip_address,
                'user_agent' => $session->user_agent,
                'last_activity' => $lastActivity,
                'last_activity_formatted' => $lastActivityFormatted,
                'additional_info' => $additionalInfo,
            ];
        }
        
        // Hitung total active users per role
        $stats = [
            'total' => count($activeUsers),
            'dosen' => collect($activeUsers)->where('role', 'dosen')->count(),
            'mahasiswa' => collect($activeUsers)->where('role', 'mahasiswa')->count(),
        ];
        
        return view('admin.active-users.index', compact('activeUsers', 'stats'));
    }
}

