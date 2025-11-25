<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BankController extends Controller
{
    /**
     * Display list of banks
     */
    public function index()
    {
        $banks = Bank::orderBy('name')->get();
        return view('admin.bank.index', compact('banks'));
    }

    /**
     * Show bank edit form
     */
    public function edit(Bank $bank)
    {
        return view('admin.bank.edit', compact('bank'));
    }

    /**
     * Update bank
     */
    public function update(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:banks,code,' . $bank->id,
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($bank->logo && Storage::disk('public')->exists($bank->logo)) {
                Storage::disk('public')->delete($bank->logo);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('banks', 'public');
            $validated['logo'] = $logoPath;
        } else {
            // Keep existing logo
            unset($validated['logo']);
        }

        $bank->update($validated);

        return redirect()->route('admin.bank.index')
            ->with('success', 'Bank berhasil diperbarui');
    }

    /**
     * Toggle bank active status
     */
    public function toggleStatus(Bank $bank)
    {
        $bank->update([
            'is_active' => !$bank->is_active
        ]);

        return back()->with('success', 'Status bank berhasil diubah');
    }
}

