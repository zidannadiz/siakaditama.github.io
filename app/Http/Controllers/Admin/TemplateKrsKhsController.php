<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateKrsKhs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateKrsKhsController extends Controller
{
    /**
     * Display list of templates
     */
    public function index(Request $request)
    {
        $query = TemplateKrsKhs::orderBy('jenis')->orderBy('created_at', 'desc');

        // Filter by jenis
        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $templates = $query->paginate(20);

        return view('admin.template-krs-khs.index', compact('templates'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.template-krs-khs.create');
    }

    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:krs,khs',
            'nama_template' => 'required|string|max:255',
            'template_file' => 'required|file|mimes:doc,docx|max:5120',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Store template file
        $file = $request->file('template_file');
        $filePath = $file->store('templates/krs-khs', 'local');
        
        $validated['file_path'] = $filePath;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Jika template baru diaktifkan, nonaktifkan template aktif lainnya dengan jenis yang sama
        if ($validated['is_active']) {
            TemplateKrsKhs::where('jenis', $validated['jenis'])
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        unset($validated['template_file']);

        TemplateKrsKhs::create($validated);

        return redirect()->route('admin.template-krs-khs.index')
            ->with('success', 'Template berhasil diupload');
    }

    /**
     * Show edit form
     */
    public function edit(TemplateKrsKhs $templateKrsKh)
    {
        return view('admin.template-krs-khs.edit', compact('templateKrsKh'));
    }

    /**
     * Update template
     */
    public function update(Request $request, TemplateKrsKhs $templateKrsKh)
    {
        $validated = $request->validate([
            'nama_template' => 'required|string|max:255',
            'template_file' => 'nullable|file|mimes:doc,docx|max:5120',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle file upload if new file provided
        if ($request->hasFile('template_file')) {
            // Delete old file
            if ($templateKrsKh->file_path && Storage::disk('local')->exists($templateKrsKh->file_path)) {
                Storage::disk('local')->delete($templateKrsKh->file_path);
            }

            // Store new file
            $file = $request->file('template_file');
            $filePath = $file->store('templates/krs-khs', 'local');
            $validated['file_path'] = $filePath;
        } else {
            unset($validated['template_file']);
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Jika template diaktifkan, nonaktifkan template aktif lainnya dengan jenis yang sama
        if ($validated['is_active']) {
            TemplateKrsKhs::where('jenis', $templateKrsKh->jenis)
                ->where('id', '!=', $templateKrsKh->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $templateKrsKh->update($validated);

        return redirect()->route('admin.template-krs-khs.index')
            ->with('success', 'Template berhasil diperbarui');
    }

    /**
     * Delete template
     */
    public function destroy(TemplateKrsKhs $templateKrsKh)
    {
        // Delete file
        if ($templateKrsKh->file_path && Storage::disk('local')->exists($templateKrsKh->file_path)) {
            Storage::disk('local')->delete($templateKrsKh->file_path);
        }

        $templateKrsKh->delete();

        return redirect()->route('admin.template-krs-khs.index')
            ->with('success', 'Template berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(TemplateKrsKhs $templateKrsKh)
    {
        if ($templateKrsKh->is_active) {
            $templateKrsKh->update(['is_active' => false]);
            $message = 'Template berhasil dinonaktifkan';
        } else {
            // Nonaktifkan template aktif lainnya dengan jenis yang sama
            TemplateKrsKhs::where('jenis', $templateKrsKh->jenis)
                ->where('id', '!=', $templateKrsKh->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
            
            $templateKrsKh->update(['is_active' => true]);
            $message = 'Template berhasil diaktifkan';
        }

        return back()->with('success', $message);
    }

    /**
     * Download template file
     */
    public function download(TemplateKrsKhs $templateKrsKh)
    {
        $filePath = storage_path('app/' . $templateKrsKh->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File template tidak ditemukan');
        }

        return response()->download($filePath, $templateKrsKh->nama_template . '.docx');
    }
}
