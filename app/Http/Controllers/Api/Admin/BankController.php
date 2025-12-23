<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Get list of banks
     */
    public function index()
    {
        $banks = Bank::orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $banks->map(function($bank) {
                return [
                    'id' => $bank->id,
                    'name' => $bank->name,
                    'code' => $bank->code,
                    'is_active' => $bank->is_active,
                    'logo' => $bank->logo ? asset('storage/' . $bank->logo) : null,
                ];
            }),
        ]);
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
        ]);

        $bank->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bank berhasil diperbarui',
            'data' => [
                'id' => $bank->id,
                'name' => $bank->name,
                'code' => $bank->code,
                'is_active' => $bank->is_active,
                'logo' => $bank->logo ? asset('storage/' . $bank->logo) : null,
            ],
        ]);
    }

    /**
     * Toggle bank active status
     */
    public function toggleStatus(Bank $bank)
    {
        $bank->update([
            'is_active' => !$bank->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status bank berhasil diubah',
            'data' => [
                'id' => $bank->id,
                'is_active' => $bank->is_active,
            ],
        ]);
    }
}
