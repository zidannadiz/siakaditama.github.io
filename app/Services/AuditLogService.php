<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Log aktivitas user
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?int $userId = null
    ): AuditLog {
        $userId = $userId ?? Auth::id();
        $request = request();

        return AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ]);
    }

    /**
     * Log create action
     */
    public static function logCreate(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            action: 'create',
            model: $model,
            newValues: $model->getAttributes(),
            description: $description ?? "Membuat {$model->getTable()} baru"
        );
    }

    /**
     * Log update action
     */
    public static function logUpdate(
        Model $model,
        array $oldValues,
        array $newValues,
        ?string $description = null
    ): AuditLog {
        return self::log(
            action: 'update',
            model: $model,
            oldValues: $oldValues,
            newValues: $newValues,
            description: $description ?? "Mengubah {$model->getTable()}"
        );
    }

    /**
     * Log delete action
     */
    public static function logDelete(Model $model, ?string $description = null): AuditLog
    {
        return self::log(
            action: 'delete',
            model: $model,
            oldValues: $model->getAttributes(),
            description: $description ?? "Menghapus {$model->getTable()}"
        );
    }

    /**
     * Log login action
     */
    public static function logLogin(?int $userId = null, ?string $description = null): AuditLog
    {
        return self::log(
            action: 'login',
            description: $description ?? 'User login ke sistem',
            userId: $userId
        );
    }

    /**
     * Log logout action
     */
    public static function logLogout(?int $userId = null, ?string $description = null): AuditLog
    {
        return self::log(
            action: 'logout',
            description: $description ?? 'User logout dari sistem',
            userId: $userId
        );
    }

    /**
     * Log approve action
     */
    public static function logApprove(
        Model $model,
        ?string $description = null
    ): AuditLog {
        return self::log(
            action: 'approve',
            model: $model,
            description: $description ?? "Menyetujui {$model->getTable()}"
        );
    }

    /**
     * Log reject action
     */
    public static function logReject(
        Model $model,
        ?string $reason = null
    ): AuditLog {
        return self::log(
            action: 'reject',
            model: $model,
            description: $reason ? "Menolak: {$reason}" : "Menolak {$model->getTable()}"
        );
    }

    /**
     * Log custom action
     */
    public static function logCustom(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        ?array $data = null
    ): AuditLog {
        return self::log(
            action: $action,
            model: $model,
            newValues: $data,
            description: $description
        );
    }
}

