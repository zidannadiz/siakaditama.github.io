<?php

namespace App\Services;

use App\Mail\GeneralNotificationMail;
use App\Mail\KrsApprovedMail;
use App\Mail\KrsRejectedMail;
use App\Mail\NilaiInputtedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Send email notification for KRS approved
     */
    public static function sendKrsApproved($krs, $userId)
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return false;
            }

            Mail::to($user->email)->send(new KrsApprovedMail($krs, $user));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send KRS approved email: ' . $e->getMessage(), [
                'user_id' => $userId,
                'krs_id' => $krs->id ?? null,
            ]);
            return false;
        }
    }

    /**
     * Send email notification for KRS rejected
     */
    public static function sendKrsRejected($krs, $userId, ?string $reason = null)
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return false;
            }

            Mail::to($user->email)->send(new KrsRejectedMail($krs, $user, $reason));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send KRS rejected email: ' . $e->getMessage(), [
                'user_id' => $userId,
                'krs_id' => $krs->id ?? null,
            ]);
            return false;
        }
    }

    /**
     * Send email notification for nilai inputted
     */
    public static function sendNilaiInputted($nilai, $userId)
    {
        try {
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return false;
            }

            Mail::to($user->email)->send(new NilaiInputtedMail($nilai, $user));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send nilai inputted email: ' . $e->getMessage(), [
                'user_id' => $userId,
                'nilai_id' => $nilai->id ?? null,
            ]);
            return false;
        }
    }

    /**
     * Send general notification email
     */
    public static function sendGeneralNotification(
        $userId,
        string $subject,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionText = null,
        string $type = 'info'
    ) {
        try {
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return false;
            }

            Mail::to($user->email)->send(new GeneralNotificationMail(
                $user,
                $subject,
                $title,
                $message,
                $actionUrl,
                $actionText,
                $type
            ));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send general notification email: ' . $e->getMessage(), [
                'user_id' => $userId,
            ]);
            return false;
        }
    }

    /**
     * Send notification with both in-app and email
     */
    public static function sendWithNotification(
        $userId,
        string $judul,
        string $pesan,
        string $tipe = 'info',
        ?string $link = null,
        bool $sendEmail = true,
        ?string $emailSubject = null
    ) {
        // Create in-app notification
        NotifikasiService::create($userId, $judul, $pesan, $tipe, $link);

        // Send email if enabled
        if ($sendEmail) {
            $emailSubject = $emailSubject ?? $judul;
            self::sendGeneralNotification(
                $userId,
                $emailSubject,
                $judul,
                $pesan,
                $link ? url($link) : null,
                'Buka Sistem',
                $tipe
            );
        }

        return true;
    }
}

