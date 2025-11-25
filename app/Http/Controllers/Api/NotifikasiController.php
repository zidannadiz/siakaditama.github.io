<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Auth::user()->notifikasis()
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'notifikasis' => $notifikasis->map(function($notifikasi) {
                    return [
                        'id' => $notifikasi->id,
                        'judul' => $notifikasi->judul,
                        'isi' => $notifikasi->isi,
                        'tipe' => $notifikasi->tipe,
                        'is_read' => $notifikasi->is_read,
                        'link' => $notifikasi->link,
                        'created_at' => $notifikasi->created_at->toISOString(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $notifikasis->currentPage(),
                    'last_page' => $notifikasis->lastPage(),
                    'per_page' => $notifikasis->perPage(),
                    'total' => $notifikasis->total(),
                ],
            ],
        ]);
    }

    public function markAsRead($id)
    {
        $notifikasi = Auth::user()->notifikasis()->findOrFail($id);
        $notifikasi->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }

    public function markAllAsRead()
    {
        Auth::user()->notifikasis()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sebagai sudah dibaca.',
        ]);
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->notifikasis()
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    public function getRecent()
    {
        $notifikasis = Auth::user()->notifikasis()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifikasis->map(function($notifikasi) {
                return [
                    'id' => $notifikasi->id,
                    'judul' => $notifikasi->judul,
                    'isi' => $notifikasi->isi,
                    'tipe' => $notifikasi->tipe,
                    'is_read' => $notifikasi->is_read,
                    'link' => $notifikasi->link,
                    'created_at' => $notifikasi->created_at->toISOString(),
                ];
            }),
        ]);
    }
}

