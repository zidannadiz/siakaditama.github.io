<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\LetterGrade;

class SystemSettingsService
{
    /**
     * Get grading weight for Tugas, UTS, UAS
     */
    public static function getGradingWeights(): array
    {
        return [
            'tugas' => SystemSetting::getValue('grading_weight_tugas', 30),
            'uts' => SystemSetting::getValue('grading_weight_uts', 30),
            'uas' => SystemSetting::getValue('grading_weight_uas', 40),
        ];
    }

    /**
     * Set grading weights
     */
    public static function setGradingWeights(float $tugas, float $uts, float $uas): void
    {
        $total = $tugas + $uts + $uas;
        
        if (abs($total - 100) > 0.01) {
            throw new \InvalidArgumentException("Total bobot harus 100%, saat ini: {$total}%");
        }

        SystemSetting::setValue('grading_weight_tugas', $tugas, 'decimal', 'grading', 'Bobot nilai tugas');
        SystemSetting::setValue('grading_weight_uts', $uts, 'decimal', 'grading', 'Bobot nilai UTS');
        SystemSetting::setValue('grading_weight_uas', $uas, 'decimal', 'grading', 'Bobot nilai UAS');
    }

    /**
     * Calculate final score based on configured weights
     */
    public static function calculateFinalScore(?float $tugas, ?float $uts, ?float $uas): ?float
    {
        if ($tugas === null || $uts === null || $uas === null) {
            return null;
        }

        $weights = self::getGradingWeights();
        
        return ($tugas * $weights['tugas'] / 100) 
             + ($uts * $weights['uts'] / 100) 
             + ($uas * $weights['uas'] / 100);
    }

    /**
     * Get letter grade by numeric score
     */
    public static function getLetterGrade(float $score): ?LetterGrade
    {
        return LetterGrade::getByScore($score);
    }

    /**
     * Get active semester ID
     */
    public static function getActiveSemesterId(): ?int
    {
        return SystemSetting::getValue('active_semester_id', null);
    }

    /**
     * Set active semester ID
     */
    public static function setActiveSemesterId(?int $semesterId): void
    {
        SystemSetting::setValue(
            'active_semester_id', 
            $semesterId, 
            'integer', 
            'semester', 
            'ID semester yang aktif'
        );
    }

    /**
     * Get application info
     */
    public static function getAppInfo(): array
    {
        return [
            'name' => SystemSetting::getValue('app_name', env('APP_NAME', 'SIAKAD')),
            'institution' => SystemSetting::getValue('app_institution', ''),
            'address' => SystemSetting::getValue('app_address', ''),
            'phone' => SystemSetting::getValue('app_phone', ''),
            'email' => SystemSetting::getValue('app_email', ''),
            'website' => SystemSetting::getValue('app_website', ''),
            'logo' => SystemSetting::getValue('app_logo', ''),
            'favicon' => SystemSetting::getValue('app_favicon', ''),
        ];
    }

    /**
     * Set application info
     */
    public static function setAppInfo(array $data): void
    {
        SystemSetting::setValue('app_name', $data['name'] ?? '', 'string', 'app_info', 'Nama aplikasi');
        SystemSetting::setValue('app_institution', $data['institution'] ?? '', 'string', 'app_info', 'Nama institusi');
        SystemSetting::setValue('app_address', $data['address'] ?? '', 'string', 'app_info', 'Alamat institusi');
        SystemSetting::setValue('app_phone', $data['phone'] ?? '', 'string', 'app_info', 'Nomor telepon');
        SystemSetting::setValue('app_email', $data['email'] ?? '', 'string', 'app_info', 'Email kontak');
        SystemSetting::setValue('app_website', $data['website'] ?? '', 'string', 'app_info', 'Website');
        SystemSetting::setValue('app_logo', $data['logo'] ?? '', 'string', 'app_info', 'Logo aplikasi');
        SystemSetting::setValue('app_favicon', $data['favicon'] ?? '', 'string', 'app_info', 'Favicon');
    }

    /**
     * Get logo URL
     */
    public static function getLogoUrl(): ?string
    {
        $logo = SystemSetting::getValue('app_logo', '');
        
        if (!$logo) {
            return null;
        }

        // Cek apakah file ada di storage
        $storagePath = storage_path('app/public/' . $logo);
        if (file_exists($storagePath)) {
            return asset('storage/' . $logo);
        }

        // Cek apakah file ada di public
        $publicPath = public_path('storage/' . $logo);
        if (file_exists($publicPath)) {
            return asset('storage/' . $logo);
        }

        return null;
    }

    /**
     * Get favicon URL
     */
    public static function getFaviconUrl(): ?string
    {
        $favicon = SystemSetting::getValue('app_favicon', '');
        
        if (!$favicon) {
            return null;
        }

        $storagePath = storage_path('app/public/' . $favicon);
        if (file_exists($storagePath)) {
            return asset('storage/' . $favicon);
        }

        $publicPath = public_path('storage/' . $favicon);
        if (file_exists($publicPath)) {
            return asset('storage/' . $favicon);
        }

        return null;
    }
}

