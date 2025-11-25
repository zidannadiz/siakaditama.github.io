<?php

namespace App\Http\Controllers;

use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->notifikasis();
        
        // Filter berdasarkan status baca
        if ($request->has('filter')) {
            if ($request->filter === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->filter === 'read') {
                $query->where('is_read', true);
            }
        }
        
        // Filter berdasarkan tipe
        if ($request->has('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        $notifikasis = $query->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('notifikasi.index', compact('notifikasis'));
    }

    public function markAsRead($id)
    {
        NotifikasiService::markAsRead($id, Auth::id());

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        NotifikasiService::markAllAsRead(Auth::id());

        return back()->with('success', 'Semua notifikasi ditandai sebagai sudah dibaca.');
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->notifikasis()
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifikasis = Auth::user()->notifikasis()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($notifikasis);
    }
}
