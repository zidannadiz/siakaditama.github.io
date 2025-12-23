<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingsService;
use App\Services\AuditLogService;
use App\Models\Semester;
use App\Models\LetterGrade;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    /**
     * Get all system settings
     */
    public function index()
    {
        $activeSemesterId = SystemSettingsService::getActiveSemesterId();
        $semesters = Semester::orderBy('tahun_ajaran', 'desc')
            ->orderBy('jenis', 'asc')
            ->get();
        $activeSemester = $semesters->firstWhere('id', $activeSemesterId);
        
        $gradingWeights = SystemSettingsService::getGradingWeights();
        $letterGrades = LetterGrade::getActiveOrdered();
        $appInfo = SystemSettingsService::getAppInfo();

        return response()->json([
            'success' => true,
            'data' => [
                'semester' => [
                    'active_semester' => $activeSemester ? [
                        'id' => $activeSemester->id,
                        'nama' => $activeSemester->nama_semester,
                        'tahun_ajaran' => $activeSemester->tahun_ajaran,
                        'jenis' => $activeSemester->jenis,
                    ] : null,
                    'semesters' => $semesters->map(function($semester) {
                        return [
                            'id' => $semester->id,
                            'nama' => $semester->nama_semester,
                            'tahun_ajaran' => $semester->tahun_ajaran,
                            'jenis' => $semester->jenis,
                            'status' => $semester->status,
                        ];
                    }),
                ],
                'grading' => [
                    'weight_tugas' => $gradingWeights['tugas'] ?? 30,
                    'weight_uts' => $gradingWeights['uts'] ?? 30,
                    'weight_uas' => $gradingWeights['uas'] ?? 40,
                ],
                'letter_grades' => $letterGrades->map(function($grade) {
                    return [
                        'id' => $grade->id,
                        'letter' => $grade->letter,
                        'bobot' => $grade->bobot,
                        'min_score' => $grade->min_score,
                        'max_score' => $grade->max_score,
                        'order' => $grade->order,
                    ];
                }),
                'app_info' => [
                    'name' => $appInfo['name'] ?? '',
                    'institution' => $appInfo['institution'] ?? '',
                    'address' => $appInfo['address'] ?? '',
                    'phone' => $appInfo['phone'] ?? '',
                    'email' => $appInfo['email'] ?? '',
                    'website' => $appInfo['website'] ?? '',
                    'logo' => $appInfo['logo'] ?? null,
                    'favicon' => $appInfo['favicon'] ?? null,
                ],
            ],
        ]);
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
            
            $oldSemester = $oldSemesterId ? Semester::find($oldSemesterId) : null;
            $newSemester = Semester::find($newSemesterId);
            
            SystemSettingsService::setActiveSemesterId($newSemesterId);
            
            if ($oldSemester) {
                $oldSemester->update(['status' => 'nonaktif']);
            }
            
            $newSemester->update(['status' => 'aktif']);
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah semester aktif: " . ($oldSemester ? $oldSemester->nama_semester : 'Tidak ada') . " → " . $newSemester->nama_semester
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Semester aktif berhasil diubah',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah semester aktif: ' . $e->getMessage(),
            ], 400);
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
        ]);

        try {
            $total = $validated['weight_tugas'] + $validated['weight_uts'] + $validated['weight_uas'];
            if (abs($total - 100) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total bobot harus sama dengan 100%',
                ], 400);
            }

            $oldWeights = SystemSettingsService::getGradingWeights();
            
            SystemSettingsService::setGradingWeights(
                $validated['weight_tugas'],
                $validated['weight_uts'],
                $validated['weight_uas']
            );
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah bobot penilaian: Tugas {$oldWeights['tugas']}% → {$validated['weight_tugas']}%, UTS {$oldWeights['uts']}% → {$validated['weight_uts']}%, UAS {$oldWeights['uas']}% → {$validated['weight_uas']}%"
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Bobot penilaian berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui bobot penilaian: ' . $e->getMessage(),
            ], 400);
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
        ]);

        try {
            if (LetterGrade::hasOverlap($validated['min_score'], $validated['max_score'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Range nilai bertabrakan dengan huruf mutu yang sudah ada',
                ], 400);
            }

            $maxOrder = LetterGrade::max('order') ?? 0;
            
            $letterGrade = LetterGrade::create([
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
            
            return response()->json([
                'success' => true,
                'message' => 'Huruf mutu berhasil ditambahkan',
                'data' => [
                    'id' => $letterGrade->id,
                    'letter' => $letterGrade->letter,
                    'bobot' => $letterGrade->bobot,
                    'min_score' => $letterGrade->min_score,
                    'max_score' => $letterGrade->max_score,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan huruf mutu: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update letter grade
     */
    public function updateLetterGrade(Request $request, $id)
    {
        $letterGrade = LetterGrade::find($id);
        
        if (!$letterGrade) {
            return response()->json([
                'success' => false,
                'message' => 'Huruf mutu tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'letter' => 'required|string|max:5|unique:letter_grades,letter,' . $id,
            'bobot' => 'required|numeric|min:0|max:4',
            'min_score' => 'required|integer|min:0|max:100',
            'max_score' => 'nullable|integer|min:0|max:100|gte:min_score',
        ]);

        try {
            if (LetterGrade::hasOverlap($validated['min_score'], $validated['max_score'], $id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Range nilai bertabrakan dengan huruf mutu yang sudah ada',
                ], 400);
            }
            
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
            
            return response()->json([
                'success' => true,
                'message' => 'Huruf mutu berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui huruf mutu: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Delete letter grade
     */
    public function deleteLetterGrade($id)
    {
        $letterGrade = LetterGrade::find($id);
        
        if (!$letterGrade) {
            return response()->json([
                'success' => false,
                'message' => 'Huruf mutu tidak ditemukan',
            ], 404);
        }

        try {
            $letter = $letterGrade->letter;
            $letterGrade->update(['is_active' => false]);
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Menghapus huruf mutu: {$letter}"
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Huruf mutu berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus huruf mutu: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update application info
     */
    public function updateAppInfo(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'institution' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:50',
            ];
            
            if ($request->filled('email')) {
                $rules['email'] = 'email|max:255';
            } else {
                $rules['email'] = 'nullable';
            }
            
            if ($request->filled('website')) {
                $rules['website'] = 'url|max:255';
            } else {
                $rules['website'] = 'nullable';
            }
            
            $validated = $request->validate($rules);

            $oldInfo = SystemSettingsService::getAppInfo();
            $data = $validated;
            
            // Keep existing logo and favicon (file upload not supported in mobile API)
            $data['logo'] = $oldInfo['logo'] ?? '';
            $data['favicon'] = $oldInfo['favicon'] ?? '';
            
            SystemSettingsService::setAppInfo($data);
            
            AuditLogService::logCustom(
                'system_settings',
                null,
                "Mengubah informasi aplikasi: {$data['name']}"
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Informasi aplikasi berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui informasi aplikasi: ' . $e->getMessage(),
            ], 400);
        }
    }
}
