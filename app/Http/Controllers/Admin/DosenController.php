<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use App\Rules\ValidEmail;
use App\Rules\StrongPassword;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DosenController extends Controller
{
    public function index()
    {
        $query = Dosen::with(['user', 'prodi'])->latest();
        
        if (auth()->user()->isAdminProdi()) {
            $query->where('prodi_id', auth()->user()->prodi_id);
        }
        
        $dosens = $query->paginate(15);
        return view('admin.dosen.index', compact('dosens'));
    }

    public function create()
    {
        if (auth()->user()->role === 'kaprodi') {
            abort(403, 'Kaprodi hanya memiliki hak akses melihat data Dosen.');
        }

        $prodis = auth()->user()->isAdminProdi()
            ? \App\Models\Prodi::where('id', auth()->user()->prodi_id)->get()
            : \App\Models\Prodi::orderBy('nama_prodi')->get();
            
        return view('admin.dosen.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'kaprodi') {
            abort(403, 'Kaprodi hanya memiliki hak akses melihat data Dosen.');
        }

        if (auth()->user()->isAdminProdi() && $request->prodi_id != auth()->user()->prodi_id) {
            abort(403, 'Anda hanya bisa menambahkan dosen di prodi Anda sendiri.');
        }

        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodis,id',
            'nidn' => 'required|string|max:20|unique:dosens,nidn',
            'nama' => 'required|string|max:255',
            'email' => ['required', new ValidEmail(), 'unique:users,email'],
            'password' => ['required', 'string', new StrongPassword()],
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'dosen',
            ]);

            $dosen = Dosen::create([
                'user_id' => $user->id,
                'prodi_id' => $validated['prodi_id'],
                'nidn' => $validated['nidn'],
                'nama' => $validated['nama'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'email' => $validated['email'],
                'status' => $validated['status'],
            ]);

            DB::commit();

            // Log audit
            AuditLogService::logCreate(
                $dosen,
                "Menambahkan dosen baru: {$dosen->nama} (NIDN: {$dosen->nidn})"
            );

            return redirect()->route('admin.dosen.index')
                ->with('success', 'Dosen berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating dosen: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'nidn' => $validated['nidn'] ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Gagal menambahkan dosen: ' . $e->getMessage()]);
        }
    }

    public function edit(Dosen $dosen)
    {
        if (auth()->user()->role === 'kaprodi') {
            abort(403, 'Kaprodi hanya memiliki hak akses melihat data Dosen.');
        }

        $this->authorizeProdi($dosen);
        $dosen->load(['user', 'prodi']);
        $prodis = auth()->user()->isAdminProdi()
            ? \App\Models\Prodi::where('id', auth()->user()->prodi_id)->get()
            : \App\Models\Prodi::orderBy('nama_prodi')->get();
            
        return view('admin.dosen.edit', compact('dosen', 'prodis'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        if (auth()->user()->role === 'kaprodi') {
            abort(403, 'Kaprodi hanya memiliki hak akses melihat data Dosen.');
        }

        $this->authorizeProdi($dosen);
        
        if (auth()->user()->isAdminProdi() && $request->prodi_id != auth()->user()->prodi_id) {
            abort(403, 'Anda tidak bisa memindahkan dosen ke prodi lain.');
        }

        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodis,id',
            'nidn' => 'required|string|max:20|unique:dosens,nidn,' . $dosen->id,
            'nama' => 'required|string|max:255',
            'email' => ['required', new ValidEmail(), 'unique:users,email,' . $dosen->user_id],
            'password' => ['nullable', 'string', new StrongPassword()],
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $oldValues = $dosen->toArray();
        
        $dosen->user->update([
            'name' => $validated['nama'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $dosen->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $dosen->update([
            'prodi_id' => $validated['prodi_id'],
            'nidn' => $validated['nidn'],
            'nama' => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'no_hp' => $validated['no_hp'] ?? null,
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        // Log audit
        AuditLogService::logUpdate(
            $dosen->fresh(),
            $oldValues,
            $dosen->fresh()->toArray(),
            "Mengubah data dosen: {$dosen->nama} (NIDN: {$dosen->nidn})"
        );

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        if (auth()->user()->role === 'kaprodi') {
            abort(403, 'Kaprodi hanya memiliki hak akses melihat data Dosen.');
        }

        $this->authorizeProdi($dosen);
        
        $dosenData = $dosen->toArray();
        $dosenName = $dosen->nama;
        $dosenNidn = $dosen->nidn;
        
        $dosen->user->delete();
        $dosen->delete();
        
        // Log audit (use array since model is deleted)
        AuditLogService::log(
            'delete',
            null,
            $dosenData,
            null,
            "Menghapus dosen: {$dosenName} (NIDN: {$dosenNidn})"
        );

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil dihapus.');
    }

    private function authorizeProdi(Dosen $dosen): void
    {
        if (auth()->user()->isAdminProdi() && $dosen->prodi_id !== auth()->user()->prodi_id) {
            abort(403, 'Anda tidak berhak mengakses data dosen prodi lain.');
        }
    }
}

