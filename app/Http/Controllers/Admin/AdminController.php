<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidEmail;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    protected $adminRoles = ['admin', 'admin_pt', 'admin_biak', 'admin_biku', 'kaprodi', 'admin_prodi'];

    public function index()
    {
        $admins = User::whereIn('role', $this->adminRoles)
            ->with('prodi')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.admin.index', compact('admins'));
    }

    public function create()
    {
        $prodis = \App\Models\Prodi::all();
        $roles = [
            'admin_pt' => 'Admin PT',
            'admin_biak' => 'Admin Biro Akademik',
            'admin_biku' => 'Admin Biro Keuangan',
            'kaprodi' => 'Kepala Program Studi',
            'admin_prodi' => 'Admin Program Studi',
        ];
        return view('admin.admin.create', compact('prodis', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'max:255', new ValidEmail(), 'unique:users'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword()],
            'role' => 'required|in:admin_pt,admin_biak,admin_biku,kaprodi,admin_prodi',
            'prodi_id' => 'nullable|exists:prodis,id',
        ]);

        if (in_array($validated['role'], ['kaprodi', 'admin_prodi']) && empty($validated['prodi_id'])) {
            return back()->withInput()->withErrors(['prodi_id' => 'Program Studi wajib diisi untuk role ini.']);
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'prodi_id' => in_array($validated['role'], ['kaprodi', 'admin_prodi']) ? $validated['prodi_id'] : null,
        ]);

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin/Pengguna berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        $admin = User::whereIn('role', $this->adminRoles)->findOrFail($id);
        return view('admin.admin.show', compact('admin'));
    }

    public function edit(string $id)
    {
        $admin = User::whereIn('role', $this->adminRoles)->findOrFail($id);
        $prodis = \App\Models\Prodi::all();
        $roles = [
            'admin_pt' => 'Admin PT',
            'admin_biak' => 'Admin Biro Akademik',
            'admin_biku' => 'Admin Biro Keuangan',
            'kaprodi' => 'Kepala Program Studi',
            'admin_prodi' => 'Admin Program Studi',
        ];
        return view('admin.admin.edit', compact('admin', 'prodis', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $admin = User::whereIn('role', $this->adminRoles)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'max:255', new ValidEmail(), Rule::unique('users')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'confirmed', new StrongPassword()],
            'role' => 'required|in:admin_pt,admin_biak,admin_biku,kaprodi,admin_prodi,admin',
            'prodi_id' => 'nullable|exists:prodis,id',
        ]);

        if (in_array($validated['role'], ['kaprodi', 'admin_prodi']) && empty($validated['prodi_id'])) {
            return back()->withInput()->withErrors(['prodi_id' => 'Program Studi wajib diisi untuk role ini.']);
        }

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        // Do not update role if the current user is modifying themselves (prevent accidental lockout)
        if ($admin->id !== auth()->id()) {
            $admin->role = $validated['role'];
            $admin->prodi_id = in_array($validated['role'], ['kaprodi', 'admin_prodi']) ? $validated['prodi_id'] : null;
        }
        
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin/Pengguna berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $admin = User::whereIn('role', $this->adminRoles)->findOrFail($id);

        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admin.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $admin->delete();

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin/Pengguna berhasil dihapus.');
    }
}
