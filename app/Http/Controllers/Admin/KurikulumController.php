<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kurikulum;
use App\Models\KurikulumDetail;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    public function index()
    {
        $kurikulums = Kurikulum::with('prodi')
            ->when(auth()->user()->isAdminProdi(), function ($q) {
                $q->where('prodi_id', auth()->user()->prodi_id);
            })
            ->orderBy('tahun', 'desc')
            ->paginate(10);

        return view('admin.kurikulum.index', compact('kurikulums'));
    }

    public function create()
    {
        abort_if(auth()->user()->role === 'kaprodi', 403, 'Anda tidak memiliki wewenang untuk menambah data ini.');

        $prodis = auth()->user()->isAdminProdi()
            ? Prodi::where('id', auth()->user()->prodi_id)->get()
            : Prodi::orderBy('nama_prodi')->get();

        return view('admin.kurikulum.create', compact('prodis'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role === 'kaprodi', 403, 'Anda tidak memiliki wewenang untuk menyimpan data ini.');

        $validated = $request->validate([
            'prodi_id'   => 'required|exists:prodis,id',
            'nama'       => 'required|string|max:100',
            'tahun'      => 'required|digits:4|integer',
            'status'     => 'required|in:aktif,nonaktif',
            'keterangan' => 'nullable|string',
        ]);

        // Jika aktif, nonaktifkan kurikulum lain di prodi yang sama
        if ($validated['status'] === 'aktif') {
            Kurikulum::where('prodi_id', $validated['prodi_id'])
                     ->where('status', 'aktif')
                     ->update(['status' => 'nonaktif']);
        }

        Kurikulum::create($validated);

        return redirect()->route('admin.kurikulum.index')
                         ->with('success', 'Kurikulum berhasil ditambahkan.');
    }

    public function show(Kurikulum $kurikulum)
    {
        $this->authorizeProdi($kurikulum);

        $kurikulum->load(['prodi', 'details.mataKuliah']);
        $mataKuliahs = MataKuliah::where('prodi_id', $kurikulum->prodi_id)
                                 ->orderBy('semester')
                                 ->get();

        return view('admin.kurikulum.show', compact('kurikulum', 'mataKuliahs'));
    }

    public function edit(Kurikulum $kurikulum)
    {
        $this->authorizeProdi($kurikulum);

        $prodis = auth()->user()->isAdminProdi()
            ? Prodi::where('id', auth()->user()->prodi_id)->get()
            : Prodi::orderBy('nama_prodi')->get();

        return view('admin.kurikulum.edit', compact('kurikulum', 'prodis'));
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeProdi($kurikulum);

        $validated = $request->validate([
            'prodi_id'   => 'required|exists:prodis,id',
            'nama'       => 'required|string|max:100',
            'tahun'      => 'required|digits:4|integer',
            'status'     => 'required|in:aktif,nonaktif',
            'keterangan' => 'nullable|string',
        ]);

        if ($validated['status'] === 'aktif') {
            Kurikulum::where('prodi_id', $validated['prodi_id'])
                     ->where('status', 'aktif')
                     ->where('id', '!=', $kurikulum->id)
                     ->update(['status' => 'nonaktif']);
        }

        $kurikulum->update($validated);

        return redirect()->route('admin.kurikulum.index')
                         ->with('success', 'Kurikulum berhasil diperbarui.');
    }

    public function destroy(Kurikulum $kurikulum)
    {
        $this->authorizeProdi($kurikulum);

        if ($kurikulum->details()->count() > 0) {
            return redirect()->route('admin.kurikulum.index')
                             ->with('error', 'Kurikulum tidak bisa dihapus karena masih memiliki mata kuliah terdaftar.');
        }

        $kurikulum->delete();

        return redirect()->route('admin.kurikulum.index')
                         ->with('success', 'Kurikulum berhasil dihapus.');
    }

    /** Tambah mata kuliah ke kurikulum */
    public function addDetail(Request $request, Kurikulum $kurikulum)
    {
        $this->authorizeProdi($kurikulum);

        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'semester_ke'    => 'required|integer|min:1|max:14',
            'jenis'          => 'required|in:wajib,pilihan',
        ]);
        $validated['kurikulum_id'] = $kurikulum->id;

        KurikulumDetail::updateOrCreate(
            ['kurikulum_id' => $kurikulum->id, 'mata_kuliah_id' => $validated['mata_kuliah_id']],
            ['semester_ke' => $validated['semester_ke'], 'jenis' => $validated['jenis']]
        );

        return redirect()->route('admin.kurikulum.show', $kurikulum)
                         ->with('success', 'Mata kuliah berhasil ditambahkan ke kurikulum.');
    }

    /** Hapus mata kuliah dari kurikulum */
    public function removeDetail(Kurikulum $kurikulum, KurikulumDetail $detail)
    {
        $this->authorizeProdi($kurikulum);
        $detail->delete();

        return redirect()->route('admin.kurikulum.show', $kurikulum)
                         ->with('success', 'Mata kuliah berhasil dihapus dari kurikulum.');
    }

    /** Pastikan admin_prodi / kaprodi hanya bisa akses prodi sendiri */
    private function authorizeProdi(Kurikulum $kurikulum): void
    {
        if (auth()->user()->isAdminProdi() && $kurikulum->prodi_id !== auth()->user()->prodi_id) {
            abort(403, 'Anda tidak berhak mengakses kurikulum prodi lain.');
        }
    }
}
