<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamViolationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'enable_tab_switch_detection',
        'max_tab_switch_count',
        'terminate_on_tab_switch_limit',
        'enable_copy_paste_detection',
        'max_copy_paste_count',
        'terminate_on_copy_paste_limit',
        'enable_window_blur_detection',
        'max_window_blur_count',
        'terminate_on_window_blur_limit',
        'enable_fullscreen_exit_detection',
        'max_fullscreen_exit_count',
        'terminate_on_fullscreen_exit_limit',
        'enable_multiple_device_detection',
        'terminate_on_multiple_device',
        'enable_time_based_termination',
        'max_violations_before_termination',
        'warning_message',
        'termination_message',
    ];

    protected $casts = [
        'enable_tab_switch_detection' => 'boolean',
        'max_tab_switch_count' => 'integer',
        'terminate_on_tab_switch_limit' => 'boolean',
        'enable_copy_paste_detection' => 'boolean',
        'max_copy_paste_count' => 'integer',
        'terminate_on_copy_paste_limit' => 'boolean',
        'enable_window_blur_detection' => 'boolean',
        'max_window_blur_count' => 'integer',
        'terminate_on_window_blur_limit' => 'boolean',
        'enable_fullscreen_exit_detection' => 'boolean',
        'max_fullscreen_exit_count' => 'integer',
        'terminate_on_fullscreen_exit_limit' => 'boolean',
        'enable_multiple_device_detection' => 'boolean',
        'terminate_on_multiple_device' => 'boolean',
        'enable_time_based_termination' => 'boolean',
        'max_violations_before_termination' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get default violation rules
     */
    public static function getDefaults(): array
    {
        return [
            'enable_tab_switch_detection' => true,
            'max_tab_switch_count' => 3,
            'terminate_on_tab_switch_limit' => true,
            'enable_copy_paste_detection' => true,
            'max_copy_paste_count' => 3,
            'terminate_on_copy_paste_limit' => true,
            'enable_window_blur_detection' => true,
            'max_window_blur_count' => 5,
            'terminate_on_window_blur_limit' => false,
            'enable_fullscreen_exit_detection' => true,
            'max_fullscreen_exit_count' => 3,
            'terminate_on_fullscreen_exit_limit' => true,
            'enable_multiple_device_detection' => false,
            'terminate_on_multiple_device' => true,
            'enable_time_based_termination' => true,
            'max_violations_before_termination' => 3,
            'warning_message' => 'Anda telah melakukan pelanggaran. Mohon untuk tidak melakukan hal yang sama lagi.',
            'termination_message' => 'Ujian dihentikan karena Anda telah melakukan pelanggaran berulang kali.',
        ];
    }
}
