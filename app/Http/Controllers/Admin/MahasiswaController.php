<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Rules\ValidEmail;
use App\Rules\StrongPassword;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::with(['prodi', 'user'])->latest()->paginate(15);
        return view('admin.mahasiswa.index', compact('mahasiswas'));
    }

    public function create()
    {
        $prodis = Prodi::all();
        return view('admin.mahasiswa.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswas,nim',
            'nama' => 'required|string|max:255',
            'email' => ['required', new ValidEmail(), 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', new StrongPassword()],
            'prodi_id' => 'required|exists:prodis,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'semester' => 'required|integer|min:1|max:14',
            'status' => 'required|in:aktif,nonaktif,lulus',
        ]);

        // Gunakan DB transaction untuk memastikan data konsisten
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
            
            // Set role setelah create (karena role tidak di fillable untuk security)
            $user->role = 'mahasiswa';
            $user->save();

            $mahasiswa = Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $validated['nim'],
            'nama' => $validated['nama'],
            'prodi_id' => $validated['prodi_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
                'semester' => $validated['semester'],
                'status' => $validated['status'],
            ]);

            DB::commit();
            
            // Log audit
            AuditLogService::logCreate(
                $mahasiswa,
                "Menambahkan mahasiswa baru: {$mahasiswa->nama} (NIM: {$mahasiswa->nim})"
            );
            
            return redirect()->route('admin.mahasiswa.index')
                ->with('success', 'Mahasiswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating mahasiswa: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'nim' => $validated['nim'] ?? null,
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menambahkan mahasiswa: ' . $e->getMessage()]);
        }
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['prodi', 'user']);
        $prodis = Prodi::all();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'prodis'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . $mahasiswa->id,
            'nama' => 'required|string|max:255',
            'email' => ['required', new ValidEmail(), 'unique:users,email,' . $mahasiswa->user_id],
            'password' => ['nullable', 'string', new StrongPassword()],
            'prodi_id' => 'required|exists:prodis,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'semester' => 'required|integer|min:1|max:14',
            'status' => 'required|in:aktif,nonaktif,lulus',
        ]);

        $mahasiswa->user->update([
            'name' => $validated['nama'],
            'email' => $validated['email'],
        ]);

        $oldValues = $mahasiswa->toArray();
        
        if ($request->filled('password')) {
            $mahasiswa->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $mahasiswa->update([
            'nim' => $validated['nim'],
            'nama' => $validated['nama'],
            'prodi_id' => $validated['prodi_id'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'semester' => $validated['semester'],
            'status' => $validated['status'],
        ]);

        // Log audit
        AuditLogService::logUpdate(
            $mahasiswa->fresh(),
            $oldValues,
            $mahasiswa->fresh()->toArray(),
            "Mengubah data mahasiswa: {$mahasiswa->nama} (NIM: {$mahasiswa->nim})"
        );

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswaData = $mahasiswa->toArray();
        $mahasiswaName = $mahasiswa->nama;
        $mahasiswaNim = $mahasiswa->nim;
        
        $mahasiswa->user->delete();
        $mahasiswa->delete();

        // Log audit (use array since model is deleted)
        AuditLogService::log(
            'delete',
            null,
            $mahasiswaData,
            null,
            "Menghapus mahasiswa: {$mahasiswaName} (NIM: {$mahasiswaNim})"
        );

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }
}

