<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingsService;
use App\Services\AuditLogService;
use App\Models\Semester;
use App\Models\LetterGrade;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemSettingsController extends Controller
{
    /**
     * Display system settings index page
     */
    public function index()
    {
        // If edit parameter exists, ensure tab is set to letter-grades
        if (request('edit')) {
            $activeTab = 'letter-grades';
        } else {
            $activeTab = request('tab', 'semester');
        }
        
        // Get current settings
        $activeSemesterId = SystemSettingsService::getActiveSemesterId();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();
        $activeSemester = $semesters->firstWhere('id', $activeSemesterId);
        
        $gradingWeights = SystemSettingsService::getGradingWeights();
        $letterGrades = LetterGrade::getActiveOrdered();
        $appInfo = SystemSettingsService::getAppInfo();
        
        return view('admin.system-settings.index', compact(
            'activeTab',
            'semesters',
            'activeSemester',
            'gradingWeights',
            'letterGrades',
            'appInfo'
        ));
    }

    /**
     * Update active semester
     */
    public function updateSemester(Request $request)
    {
        $validated = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            $oldSemesterId = SystemSettingsService::getActiveSemesterId();
            $newSemesterId = $validated['semester_id'];
            
            // Get semester info
            $oldSemester = $oldSemesterId ? Semester::find($oldSemesterId) : null;
            $newSemester = Semester::find($newSemesterId);
            
            // Update active semester
            SystemSettingsService::setActiveSemesterId($newSemesterId);
            
            // Deactivate old semester
            if ($oldSemester) {
                $oldSemester->update(['status' => 'nonaktif']);
            }
            
            // Activate new semester
            $newSemester->update(['status' => 'aktif']);
            
            // Audit log
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah semester aktif: " . ($oldSemester ? $oldSemester->nama_semester : 'Tidak ada') . " → " . $newSemester->nama_semester
            );
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'semester'])
                ->with('success', 'Semester aktif berhasil diubah.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah semester aktif: ' . $e->getMessage()]);
        }
    }

    /**
     * Update grading weights
     */
    public function updateGrading(Request $request)
    {
        $validated = $request->validate([
            'weight_tugas' => 'required|numeric|min:0|max:100',
            'weight_uts' => 'required|numeric|min:0|max:100',
            'weight_uas' => 'required|numeric|min:0|max:100',
        ], [], [
            'weight_tugas' => 'bobot tugas',
            'weight_uts' => 'bobot UTS',
            'weight_uas' => 'bobot UAS',
        ]);

        try {
            $oldWeights = SystemSettingsService::getGradingWeights();
            
            SystemSettingsService::setGradingWeights(
                $validated['weight_tugas'],
                $validated['weight_uts'],
                $validated['weight_uas']
            );
            
            // Audit log
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah bobot penilaian: Tugas {$oldWeights['tugas']}% → {$validated['weight_tugas']}%, UTS {$oldWeights['uts']}% → {$validated['weight_uts']}%, UAS {$oldWeights['uas']}% → {$validated['weight_uas']}%"
            );
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'grading'])
                ->with('success', 'Bobot penilaian berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Store new letter grade
     */
    public function storeLetterGrade(Request $request)
    {
        $validated = $request->validate([
            'letter' => 'required|string|max:5|unique:letter_grades,letter',
            'bobot' => 'required|numeric|min:0|max:4',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'nullable|integer|min:0|max:100|gte:min_score',
        ], [], [
            'letter' => 'huruf mutu',
            'bobot' => 'bobot',
            'min_score' => 'nilai minimal',
            'max_score' => 'nilai maksimal',
        ]);

        try {
            // Check for overlap
            if (LetterGrade::hasOverlap($validated['min_score'], $validated['max_score'])) {
                return back()->withErrors(['error' => 'Range nilai bertabrakan dengan huruf mutu yang sudah ada.']);
            }

            // Get max order
            $maxOrder = LetterGrade::max('order') ?? 0;
            
            LetterGrade::create([
                'letter' => $validated['letter'],
                'bobot' => $validated['bobot'],
                'min_score' => $validated['min_score'],
                'max_score' => $validated['max_score'],
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Menambahkan huruf mutu: {$validated['letter']} (Range: {$validated['min_score']}-{$validated['max_score']}, Bobot: {$validated['bobot']})"
            );
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'letter-grades'])
                ->with('success', 'Huruf mutu berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan huruf mutu: ' . $e->getMessage()]);
        }
    }

    /**
     * Update letter grade
     */
    public function updateLetterGrade(Request $request, LetterGrade $letterGrade)
    {
        $validated = $request->validate([
            'letter' => 'required|string|max:5|unique:letter_grades,letter,' . $letterGrade->id,
            'bobot' => 'required|numeric|min:0|max:4',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'nullable|integer|min:0|max:100|gte:min_score',
        ], [], [
            'letter' => 'huruf mutu',
            'bobot' => 'bobot',
            'min_score' => 'nilai minimal',
            'max_score' => 'nilai maksimal',
        ]);

        try {
            // Check for overlap (exclude current letter grade)
            if (LetterGrade::hasOverlap($validated['min_score'], $validated['max_score'], $letterGrade->id)) {
                return back()->withErrors(['error' => 'Range nilai bertabrakan dengan huruf mutu yang sudah ada.']);
            }

            $oldValues = $letterGrade->toArray();
            
            $letterGrade->update([
                'letter' => $validated['letter'],
                'bobot' => $validated['bobot'],
                'min_score' => $validated['min_score'],
                'max_score' => $validated['max_score'],
            ]);
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah huruf mutu: {$validated['letter']} (Range: {$validated['min_score']}-{$validated['max_score']}, Bobot: {$validated['bobot']})"
            );
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'letter-grades'])
                ->with('success', 'Huruf mutu berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.system-settings.index', ['tab' => 'letter-grades', 'edit' => $letterGrade->id])
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Error updating letter grade: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'letter_grade_id' => $letterGrade->id,
                'request_data' => $request->all()
            ]);
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'letter-grades', 'edit' => $letterGrade->id])
                ->withInput()
                ->withErrors(['error' => 'Gagal memperbarui huruf mutu: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete letter grade
     */
    public function deleteLetterGrade(LetterGrade $letterGrade)
    {
        try {
            $letter = $letterGrade->letter;
            
            // Soft delete by setting is_active to false
            $letterGrade->update(['is_active' => false]);
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Menghapus huruf mutu: {$letter}"
            );
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'letter-grades'])
                ->with('success', 'Huruf mutu berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus huruf mutu: ' . $e->getMessage()]);
        }
    }

    /**
     * Update application info
     */
    public function updateAppInfo(Request $request)
    {
        try {
            \Log::info('Update App Info - Request received', [
                'has_logo' => $request->hasFile('logo'),
                'has_favicon' => $request->hasFile('favicon'),
                'name' => $request->input('name'),
            ]);
            
            // Custom validation rules for nullable fields
            $rules = [
                'name' => 'required|string|max:255',
                'institution' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:50',
                'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                'favicon' => 'nullable|image|mimes:png,ico|max:512',
            ];
            
            // Email validation - only validate if not empty
            if ($request->filled('email')) {
                $rules['email'] = 'email|max:255';
            } else {
                $rules['email'] = 'nullable';
            }
            
            // Website validation - only validate if not empty
            if ($request->filled('website')) {
                $rules['website'] = 'url|max:255';
            } else {
                $rules['website'] = 'nullable';
            }
            
            $validated = $request->validate($rules, [], [
                'name' => 'nama aplikasi',
                'institution' => 'nama institusi',
                'address' => 'alamat',
                'phone' => 'nomor telepon',
                'email' => 'email',
                'website' => 'website',
                'logo' => 'logo',
                'favicon' => 'favicon',
            ]);

            $oldInfo = SystemSettingsService::getAppInfo();
            $data = $validated;
            
            // Ensure storage directory exists
            Storage::makeDirectory('public/system');
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoPath = $logo->store('public/system');
                $logoFileName = str_replace('public/system/', '', $logoPath);
                $data['logo'] = 'system/' . $logoFileName;
                
                // Delete old logo if exists
                if (!empty($oldInfo['logo']) && Storage::exists('public/' . $oldInfo['logo'])) {
                    Storage::delete('public/' . $oldInfo['logo']);
                }
            } else {
                $data['logo'] = $oldInfo['logo'] ?? '';
            }
            
            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                $favicon = $request->file('favicon');
                $faviconPath = $favicon->store('public/system');
                $faviconFileName = str_replace('public/system/', '', $faviconPath);
                $data['favicon'] = 'system/' . $faviconFileName;
                
                // Delete old favicon if exists
                if (!empty($oldInfo['favicon']) && Storage::exists('public/' . $oldInfo['favicon'])) {
                    Storage::delete('public/' . $oldInfo['favicon']);
                }
            } else {
                $data['favicon'] = $oldInfo['favicon'] ?? '';
            }
            
            // Ensure all required fields are present
            $data['name'] = $data['name'] ?? '';
            $data['institution'] = $data['institution'] ?? '';
            $data['address'] = $data['address'] ?? '';
            $data['phone'] = $data['phone'] ?? '';
            $data['email'] = $data['email'] ?? '';
            $data['website'] = $data['website'] ?? '';
            
            \Log::info('Update App Info - Data prepared', [
                'data_keys' => array_keys($data),
                'has_logo' => !empty($data['logo']),
                'has_favicon' => !empty($data['favicon']),
            ]);
            
            SystemSettingsService::setAppInfo($data);
            
            \Log::info('Update App Info - Success');
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah informasi aplikasi: {$data['name']}"
            );
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'app-info'])
                ->with('success', 'Informasi aplikasi berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.system-settings.index', ['tab' => 'app-info'])
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Mohon perbaiki kesalahan pada form di bawah ini.');
        } catch (\Exception $e) {
            \Log::error('Error updating app info: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['logo', 'favicon'])
            ]);
            
            return redirect()->route('admin.system-settings.index', ['tab' => 'app-info'])
                ->withInput()
                ->withErrors(['error' => 'Gagal memperbarui informasi aplikasi: ' . $e->getMessage()]);
        }
    }
}
