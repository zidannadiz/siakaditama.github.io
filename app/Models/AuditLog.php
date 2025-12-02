<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi polymorphic ke model yang diubah
     */
    public function model()
    {
        return $this->morphTo('model');
    }

    /**
     * Get human readable action name
     */
    public function getActionNameAttribute(): string
    {
        $actions = [
            'create' => 'Membuat',
            'update' => 'Mengubah',
            'delete' => 'Menghapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'approve' => 'Menyetujui',
            'reject' => 'Menolak',
            'restore' => 'Mengembalikan',
            'backup' => 'Backup',
            'export' => 'Export',
            'import' => 'Import',
            'chat_send' => 'Mengirim Chat',
            'chat_read' => 'Membaca Chat',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get model name in Indonesian
     */
    public function getModelNameAttribute(): string
    {
        if (!$this->model_type) {
            return '-';
        }

        $models = [
            'App\Models\User' => 'User',
            'App\Models\Mahasiswa' => 'Mahasiswa',
            'App\Models\Dosen' => 'Dosen',
            'App\Models\Nilai' => 'Nilai',
            'App\Models\KRS' => 'KRS',
            'App\Models\Exam' => 'Ujian',
            'App\Models\Assignment' => 'Tugas',
            'App\Models\MataKuliah' => 'Mata Kuliah',
            'App\Models\JadwalKuliah' => 'Jadwal Kuliah',
            'App\Models\Prodi' => 'Program Studi',
            'App\Models\Semester' => 'Semester',
            'App\Models\Conversation' => 'Percakapan',
            'App\Models\Message' => 'Pesan',
        ];

        $className = class_basename($this->model_type);
        
        return $models[$this->model_type] ?? $className;
    }

    /**
     * Scope untuk filter berdasarkan action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan model type
     */
    public function scopeModelType($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
