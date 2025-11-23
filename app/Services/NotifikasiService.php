<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiService
{
    /**
     * Buat notifikasi untuk user tertentu
     */
    public static function create($user_id, $judul, $pesan, $tipe = 'info', $link = null)
    {
        return Notifikasi::create([
            'user_id' => $user_id,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Buat notifikasi untuk multiple users
     */
    public static function createForUsers($user_ids, $judul, $pesan, $tipe = 'info', $link = null)
    {
        $notifikasis = [];
        foreach ($user_ids as $user_id) {
            $notifikasis[] = [
                'user_id' => $user_id,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
                'link' => $link,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return Notifikasi::insert($notifikasis);
    }

    /**
     * Buat notifikasi untuk semua user dengan role tertentu
     */
    public static function createForRole($role, $judul, $pesan, $tipe = 'info', $link = null)
    {
        $user_ids = User::where('role', $role)->pluck('id')->toArray();
        return self::createForUsers($user_ids, $judul, $pesan, $tipe, $link);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public static function markAsRead($notifikasi_id, $user_id)
    {
        return Notifikasi::where('id', $notifikasi_id)
            ->where('user_id', $user_id)
            ->update(['is_read' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca
     */
    public static function markAllAsRead($user_id)
    {
        return Notifikasi::where('user_id', $user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Hapus notifikasi yang sudah dibaca lebih dari 30 hari
     */
    public static function cleanOldNotifications($days = 30)
    {
        return Notifikasi::where('is_read', true)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}

