<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.admin.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
        ]);

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        return view('admin.admin.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $admin->name = $validated['name'];
        $admin->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admin.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $admin->delete();

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}
