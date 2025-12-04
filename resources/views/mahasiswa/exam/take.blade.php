<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian: {{ $exam->judul }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f3f4f6;
        }
        .exam-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .exam-header {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .timer {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
        }
        .timer.warning {
            color: #f59e0b;
        }
        .question-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .question-number {
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 12px;
        }
        .question-text {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #111827;
        }
        .answer-option {
            padding: 12px;
            margin-bottom: 8px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .answer-option:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }
        .answer-option.selected {
            border-color: #2563eb;
            background: #dbeafe;
        }
        .answer-option input[type="radio"] {
            margin-right: 10px;
        }
        textarea {
            width: 100%;
            min-height: 200px;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
        }
        textarea:focus {
            outline: none;
            border-color: #2563eb;
        }
        .navigation {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 2px solid #e5e7eb;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        .btn-danger:hover {
            background: #b91c1c;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .warning-banner {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #92400e;
            font-weight: 600;
        }
        .violation-count {
            background: #fee2e2;
            border: 2px solid #dc2626;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #991b1b;
            font-weight: 600;
        }
        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .progress-fill {
            height: 100%;
            background: #2563eb;
            transition: width 0.3s;
        }
        /* Prevent text selection */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body>
    <div class="exam-container">
        <!-- Header dengan Timer -->
        <div class="exam-header">
            <div>
                <h1 class="text-2xl font-bold">{{ $exam->judul }}</h1>
                <p class="text-gray-600">{{ $exam->jadwalKuliah->mataKuliah->nama_mk }}</p>
            </div>
            <div class="timer" id="timer">00:00:00</div>
        </div>

        <!-- Warning Banner - Completely hidden, all warnings shown via modal only -->
        @if($exam->prevent_copy_paste || $exam->prevent_new_tab || $exam->fullscreen_mode)
        <div class="warning-banner" style="display: none !important; visibility: hidden;">
            ⚠️ PERINGATAN: 
            @if($exam->prevent_copy_paste) Copy/Paste dilarang. 
            @endif
            @if($exam->prevent_new_tab) Membuka tab lain dilarang. 
            @endif
            @if($exam->fullscreen_mode) Mode fullscreen wajib diaktifkan.
            @endif
            Pelanggaran akan dicatat dan dapat menyebabkan ujian dihentikan!
        </div>
        @endif

        <!-- Violation Count - Completely hidden, violations shown via modal only -->
        <div class="violation-count" id="violation-banner" style="display: none !important; visibility: hidden;">
            ⚠️ PELANGGARAN DICATAT: <span id="violation-count">0</span> kali pelanggaran terdeteksi!
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-fill" id="progress-bar" style="width: 0%"></div>
        </div>

        <!-- Questions -->
        <form id="exam-form">
            @foreach($questions as $index => $question)
            <div class="question-card" data-question-id="{{ $question->id }}">
                <div class="question-number">Soal {{ $index + 1 }} / {{ $questions->count() }}</div>
                <div class="question-text no-select">{{ $question->pertanyaan }}</div>
                
                @if($question->isPilgan())
                    @php
                        $pilihan = $question->pilihan ?? [];
                        $currentAnswer = $question->answers->first();
                    @endphp
                    @foreach($pilihan as $key => $value)
                        <label class="answer-option no-select {{ $currentAnswer && $currentAnswer->jawaban_pilgan === $key ? 'selected' : '' }}">
                            <input type="radio" 
                                   name="question_{{ $question->id }}" 
                                   value="{{ $key }}"
                                   data-question-id="{{ $question->id }}"
                                   {{ $currentAnswer && $currentAnswer->jawaban_pilgan === $key ? 'checked' : '' }}>
                            <span>{{ $key }}. {{ $value }}</span>
                        </label>
                    @endforeach
                @else
                    @php
                        $currentAnswerEssay = $question->answers->first();
                    @endphp
                    <textarea 
                        name="question_{{ $question->id }}"
                        data-question-id="{{ $question->id }}"
                        placeholder="Tulis jawaban Anda di sini..."
                        class="no-select"
                    >{{ $currentAnswerEssay ? $currentAnswerEssay->jawaban_essay : '' }}</textarea>
                @endif
            </div>
            @endforeach
        </form>
    </div>

    <!-- Navigation Bar -->
    <div class="navigation">
        <button type="button" class="btn btn-primary" onclick="submitExam()">Submit Ujian</button>
        <div>
            <span id="answered-count">0</span> / {{ $questions->count() }} soal terjawab
        </div>
        <div>
            <button type="button" class="btn btn-primary" onclick="saveAll()">Simpan Semua</button>
        </div>
    </div>

    <script>
        // Constants
        const examId = {{ $exam->id }};
        const sessionId = {{ $session->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let timeRemaining = {{ $remaining }}; // in seconds
        let violationCount = 0;
        let isFullscreen = false;
        let autoSaveInterval;
        let timerInterval;
        
        // Violation Rules (from server)
        const violationRules = @json($exam->violationRule ?? \App\Models\ExamViolationRule::getDefaults());
        const warningMessage = violationRules.warning_message || 'Anda telah melakukan pelanggaran. Mohon untuk tidak melakukan hal yang sama lagi.';
        const terminationMessage = violationRules.termination_message || 'Ujian dihentikan karena Anda telah melakukan pelanggaran berulang kali.';
        const maxViolations = violationRules.max_violations_before_termination || 3;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeFullscreen();
            initializeTimer();
            initializeEventListeners();
            initializeAnswerTracking();
            startAutoSave();
            updateAnsweredCount();
        });

        // Fullscreen Mode
        function initializeFullscreen() {
            @if($exam->fullscreen_mode)
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error('Error attempting to enable fullscreen:', err);
                    showConfirm(
                        'Mode Fullscreen Diperlukan',
                        'Mohon aktifkan mode fullscreen untuk melanjutkan ujian! Mode fullscreen wajib diaktifkan saat mengerjakan ujian.',
                        function() {
                            closeUniversalModal();
                        }
                    );
                });
            }
            
            isFullscreen = !!document.fullscreenElement;

            // Detect fullscreen exit
            document.addEventListener('fullscreenchange', function() {
                const isCurrentlyFullscreen = !!document.fullscreenElement;
                if (!isCurrentlyFullscreen && isFullscreen) {
                    // Only log if detection is enabled
                    if (violationRules.enable_fullscreen_exit_detection !== false) {
                        logViolation('fullscreen_exit');
                    }
                    showConfirm(
                        'Peringatan Pelanggaran',
                        'Jangan keluar dari mode fullscreen!\n\nMode fullscreen wajib diaktifkan selama ujian. Pelanggaran telah dicatat.',
                        function() {
                            document.documentElement.requestFullscreen();
                            closeUniversalModal();
                        }
                    );
                }
                isFullscreen = isCurrentlyFullscreen;
            });
            @endif
        }

        // Timer
        function initializeTimer() {
            updateTimer();
            timerInterval = setInterval(function() {
                timeRemaining--;
                updateTimer();

                if (timeRemaining <= 300) { // 5 minutes warning
                    document.getElementById('timer').classList.add('warning');
                }

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    clearInterval(autoSaveInterval);
                    showConfirm(
                        'Waktu Ujian Habis',
                        'Waktu ujian telah habis! Jawaban Anda akan otomatis disubmit. Pastikan semua jawaban sudah tersimpan.',
                        function() {
                            submitExam(true); // auto-submit
                            closeUniversalModal();
                        }
                    );
                }
            }, 1000);
        }

        function updateTimer() {
            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = timeRemaining % 60;
            
            document.getElementById('timer').textContent = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
            
            // Update progress bar
            const totalTime = {{ $exam->durasi * 60 }};
            const elapsed = totalTime - timeRemaining;
            const progress = (elapsed / totalTime) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
        }

        // Prevent Copy/Paste
        @if($exam->prevent_copy_paste)
        function initializeEventListeners() {
            // Only enable if copy-paste detection is enabled in rules
            if (violationRules.enable_copy_paste_detection !== false) {
                // Disable copy
                document.addEventListener('copy', function(e) {
                    e.preventDefault();
                    logViolation('copy_paste');
                    // Modal will be shown by logViolation function
                });

                // Disable cut
                document.addEventListener('cut', function(e) {
                    e.preventDefault();
                    logViolation('copy_paste');
                });

                // Disable paste
                document.addEventListener('paste', function(e) {
                    e.preventDefault();
                    logViolation('copy_paste');
                });

                // Disable keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+A
                    if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'a')) {
                        e.preventDefault();
                        logViolation('copy_paste');
                    }

                    // F12 (Developer Tools)
                    if (e.key === 'F12') {
                        e.preventDefault();
                        logViolation('copy_paste');
                    }
                });
            }
            
            // Always disable right-click context menu and text selection
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });

            // Disable text selection on question text
            document.querySelectorAll('.no-select').forEach(el => {
                el.addEventListener('selectstart', e => e.preventDefault());
            });
        }
        @endif

        // Tab Switch Detection
        @if($exam->prevent_new_tab)
        let hasShownTabWarning = false;
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && timeRemaining > 0) {
                // Only log if tab switch detection is enabled
                if (violationRules.enable_tab_switch_detection !== false) {
                    logViolation('tab_switch');
                }
                
                if (!hasShownTabWarning) {
                    hasShownTabWarning = true;
                    // Modal will be shown by logViolation function
                    setTimeout(() => {
                        hasShownTabWarning = false;
                    }, 5000);
                }
            }
        });

        let hasShownBlurWarning = false;
        window.addEventListener('blur', function() {
            if (timeRemaining > 0) {
                // Only log if window blur detection is enabled
                if (violationRules.enable_window_blur_detection !== false) {
                    logViolation('window_blur');
                }
                
                if (!hasShownBlurWarning) {
                    hasShownBlurWarning = true;
                    // Modal will be shown by logViolation function
                    setTimeout(() => {
                        hasShownBlurWarning = false;
                    }, 5000);
                }
            }
        });
        @endif

        // Answer Tracking
        function initializeAnswerTracking() {
            // Radio buttons
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    saveAnswer(this.dataset.questionId, this.value, null);
                    updateAnsweredCount();
                });
            });

            // Textareas
            document.querySelectorAll('textarea').forEach(textarea => {
                let timeout;
                textarea.addEventListener('input', function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        saveAnswer(this.dataset.questionId, null, this.value);
                        updateAnsweredCount();
                    }, 1000); // Auto-save after 1 second of no typing
                });
            });
        }

        function updateAnsweredCount() {
            let count = 0;
            document.querySelectorAll('input[type="radio"]:checked, textarea').forEach(el => {
                if (el.tagName === 'INPUT' || (el.tagName === 'TEXTAREA' && el.value.trim() !== '')) {
                    count++;
                }
            });
            document.getElementById('answered-count').textContent = count;
        }

        // Save Answer
        function saveAnswer(questionId, jawabanPilgan, jawabanEssay) {
            fetch(`/mahasiswa/exam/${examId}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    question_id: questionId,
                    jawaban_pilgan: jawabanPilgan,
                    jawaban_essay: jawabanEssay
                })
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Answer saved:', questionId);
                }
            }).catch(error => {
                console.error('Error saving answer:', error);
            });
        }

        // Auto Save All
        function startAutoSave() {
            autoSaveInterval = setInterval(function() {
                saveAll();
            }, 30000); // Auto-save every 30 seconds
        }

        function saveAll() {
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                saveAnswer(radio.dataset.questionId, radio.value, null);
            });

            document.querySelectorAll('textarea').forEach(textarea => {
                if (textarea.value.trim() !== '') {
                    saveAnswer(textarea.dataset.questionId, null, textarea.value);
                }
            });
        }

        // Log Violation
        let lastViolationModalTime = 0;
        function logViolation(type) {
            // Check if this violation type is enabled
            let isEnabled = false;
            switch(type) {
                case 'tab_switch':
                    isEnabled = violationRules.enable_tab_switch_detection !== false;
                    break;
                case 'copy_paste':
                    isEnabled = violationRules.enable_copy_paste_detection !== false;
                    break;
                case 'window_blur':
                    isEnabled = violationRules.enable_window_blur_detection !== false;
                    break;
                case 'fullscreen_exit':
                    isEnabled = violationRules.enable_fullscreen_exit_detection !== false;
                    break;
            }
            
            if (!isEnabled) {
                return; // Don't log if detection is disabled
            }
            
            violationCount++;
            
            fetch(`/mahasiswa/exam/${examId}/log-violation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    violation_type: type
                })
            }).then(response => response.json())
            .then(data => {
                if (data.error && data.terminated) {
                    clearInterval(timerInterval);
                    clearInterval(autoSaveInterval);
                    
                    // Jika redirect_to_dashboard true, langsung redirect ke dashboard tanpa modal
                    if (data.redirect_to_dashboard) {
                        // Stop semua interval
                        if (timerInterval) clearInterval(timerInterval);
                        if (autoSaveInterval) clearInterval(autoSaveInterval);
                        
                        // Redirect langsung ke dashboard tanpa konfirmasi (gunakan replace agar tidak bisa kembali)
                        window.location.replace(data.redirect_url || '/mahasiswa/dashboard');
                        return;
                    }
                    
                    if (data.must_restart) {
                        // Session deleted, must restart from beginning
                        showConfirm(
                            'Ujian Dihentikan',
                            `${terminationMessage}\n\nUjian Anda telah dihentikan dan session telah dihapus. Anda harus mengulang ujian dari awal.\n\nAnda akan diarahkan kembali ke halaman daftar ujian.`,
                            function() {
                                window.location.href = `/mahasiswa/exam`;
                            }
                        );
                    } else {
                        showConfirm(
                            'Ujian Dihentikan',
                            `${terminationMessage}\n\nAnda akan diarahkan ke halaman hasil ujian.`,
                            function() {
                                window.location.href = `/mahasiswa/exam/${examId}/result/${sessionId}`;
                            }
                        );
                    }
                } else if (data.success !== false) {
                    // Update violation count from server response
                    const totalViolations = data.total_violations || violationCount;
                    const remaining = data.remaining !== undefined ? data.remaining : (maxViolations - totalViolations);
                    
                    // Update violation count in hidden element (for tracking only)
                    const violationCountEl = document.getElementById('violation-count');
                    if (violationCountEl) {
                        violationCountEl.textContent = totalViolations;
                    }
                    
                    // Show modal notification for violation count (throttle to avoid too many modals)
                    const now = Date.now();
                    if (now - lastViolationModalTime > 2000) { // Show modal max once every 2 seconds
                        lastViolationModalTime = now;
                        
                        let message = `${warningMessage}\n\nTotal pelanggaran: ${totalViolations} dari ${maxViolations} maksimal.`;
                        
                        // Add type-specific information
                        if (data.tab_switch_count !== undefined) {
                            message += `\n\nTab Switch: ${data.tab_switch_count} kali`;
                        }
                        if (data.copy_paste_count !== undefined) {
                            message += `\nCopy-Paste: ${data.copy_paste_count} kali`;
                        }
                        
                        if (remaining > 0) {
                            message += `\n\nSisa kesempatan: ${remaining} kali.`;
                            if (remaining === 1) {
                                message += `\n\nPERINGATAN: Jika Anda melakukan pelanggaran sekali lagi, ujian akan dihentikan!`;
                            }
                        } else {
                            message += `\n\nPERINGATAN: Anda telah mencapai batas maksimal pelanggaran!`;
                        }
                        
                        showConfirm(
                            'Pelanggaran Terdeteksi',
                            message,
                            function() {
                                closeUniversalModal();
                            }
                        );
                    }
                }
            }).catch(error => {
                console.error('Error logging violation:', error);
            });
        }

        // Submit Exam
        function submitExam(isAuto = false) {
            if (!isAuto) {
                showConfirm('Konfirmasi Submit', 'Apakah Anda yakin ingin submit ujian? Pastikan semua jawaban sudah diisi.', function() {
                    submitExamNow(isAuto);
                });
                return;
            }
            submitExamNow(isAuto);
        }

        function submitExamNow(isAuto = false) {
            // Save all answers first
            saveAll();

            // Disable all inputs
            document.querySelectorAll('input, textarea, button').forEach(el => {
                el.disabled = true;
            });

            // Submit
            fetch(`/mahasiswa/exam/${examId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    session_id: sessionId
                })
            }).then(response => response.json())
            .then(data => {
                clearInterval(timerInterval);
                clearInterval(autoSaveInterval);
                window.location.href = `/mahasiswa/exam/${examId}/result/${sessionId}`;
            }).catch(error => {
                console.error('Error submitting exam:', error);
                showConfirm(
                    'Error Submit Ujian',
                    'Terjadi kesalahan saat mengirim jawaban. Silakan coba lagi atau hubungi pengawas ujian jika masalah berlanjut.',
                    function() {
                        closeUniversalModal();
                    }
                );
            });
        }

        // Custom warning system (replaces browser default dialog)
        let isLeaving = false;
        let hasShownWarning = false;
        let pendingNavigation = null;
        let lastSaveTime = Date.now();

        // Auto-save periodically and before leaving
        function autoSaveBeforeLeave() {
            if (timeRemaining > 0) {
                // Only save if last save was more than 2 seconds ago
                const now = Date.now();
                if (now - lastSaveTime > 2000) {
                    saveAll();
                    lastSaveTime = now;
                }
            }
        }

        // Early warning: Detect mouse leaving window area (moving to browser controls)
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY <= 0 && timeRemaining > 0 && !hasShownWarning && !isLeaving) {
                hasShownWarning = true;
                autoSaveBeforeLeave(); // Save before warning
                showConfirm(
                    'Peringatan Penting',
                    'Jangan tutup atau refresh halaman ujian! Jika Anda meninggalkan halaman ini, ujian Anda akan terhenti dan jawaban yang belum tersimpan mungkin akan hilang. Pastikan semua jawaban sudah tersimpan sebelum meninggalkan halaman.',
                    function() {
                        hasShownWarning = false;
                        closeUniversalModal();
                    }
                );
                setTimeout(() => { hasShownWarning = false; }, 5000);
            }
        });

        // Detect navigation attempts via links
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a[href]');
            if (target && target.href && timeRemaining > 0 && !isLeaving) {
                e.preventDefault();
                pendingNavigation = target.href;
                autoSaveBeforeLeave(); // Save before asking
                showConfirm(
                    'Peringatan!',
                    'Jika Anda meninggalkan halaman ini, ujian Anda akan terhenti. Pastikan semua jawaban sudah tersimpan. Apakah Anda yakin ingin keluar?',
                    function() {
                        isLeaving = true;
                        if (pendingNavigation) {
                            window.location.href = pendingNavigation;
                        }
                    },
                    function() {
                        pendingNavigation = null;
                    }
                );
            }
        }, true);

        // Note: Browser default dialog cannot be completely replaced for security reasons
        // We use custom modal warnings (mouseleave detection) instead
        // Auto-save is handled by periodic auto-save interval
        
        // Optional: Try to save using pagehide event (non-blocking, no dialog)
        window.addEventListener('pagehide', function(e) {
            if (timeRemaining > 0 && !isLeaving) {
                // Try to save one last time (may not always work if page is unloading)
                try {
                    navigator.sendBeacon('/mahasiswa/exam/' + examId + '/save-all', JSON.stringify({
                        session_id: sessionId,
                        _token: csrfToken
                    }));
                } catch(err) {
                    console.log('Could not send beacon save');
                }
            }
        });
    </script>

    <!-- Universal Modal for Alerts and Confirmations -->
    <div id="universalModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 99999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); max-width: 500px; width: 90%; margin: 0 auto; position: relative; z-index: 100000;" onclick="event.stopPropagation()">
            <div style="padding: 24px;">
                <div id="modalIcon" style="display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; margin: 0 auto 16px; border-radius: 50%;">
                    <!-- Icon akan diisi oleh JavaScript -->
                </div>
                <h3 style="font-size: 18px; font-weight: 600; color: #111827; text-align: center; margin-bottom: 8px;" id="modalTitle">Title</h3>
                <p style="font-size: 14px; color: #4b5563; text-align: center; margin-bottom: 24px; white-space: pre-line;" id="modalMessage">Message</p>
                <div id="modalButtons" style="display: flex; gap: 12px;">
                    <!-- Buttons akan diisi oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            let confirmCallback = null;
            let cancelCallback = null;

            // Icon configurations
            const iconConfigs = {
                confirm: {
                    bg: 'bg-yellow-100',
                    icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    color: 'text-yellow-600'
                },
                alert: {
                    bg: 'bg-blue-100',
                    icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-blue-600'
                },
                error: {
                    bg: 'bg-red-100',
                    icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-red-600'
                },
                warning: {
                    bg: 'bg-orange-100',
                    icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                    color: 'text-orange-600'
                },
                info: {
                    bg: 'bg-blue-100',
                    icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-blue-600'
                },
                success: {
                    bg: 'bg-green-100',
                    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    color: 'text-green-600'
                }
            };

            function showUniversalModal(type, title, message, options = {}) {
                const modal = document.getElementById('universalModal');
                const iconEl = document.getElementById('modalIcon');
                const titleEl = document.getElementById('modalTitle');
                const messageEl = document.getElementById('modalMessage');
                const buttonsEl = document.getElementById('modalButtons');
                
                if (!modal) {
                    console.error('Modal not found!');
                    return false;
                }

                // Set icon (smaller size - reduced from w-16 h-16 to w-12 h-12, and icon from w-8 h-8 to w-6 h-6)
                const config = iconConfigs[type] || iconConfigs.alert;
                iconEl.className = `flex items-center justify-center w-12 h-12 mx-auto mb-4 ${config.bg} rounded-full`;
                iconEl.innerHTML = `<svg class="w-6 h-6 ${config.color}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${config.icon}"></path>
                </svg>`;

                // Set title and message
                if (titleEl) titleEl.textContent = title || 'Notifikasi';
                if (messageEl) messageEl.textContent = message || '';

                // Store callbacks before clearing buttons
                const callback = options.callback || null;
                const cancelCb = options.cancelCallback || null;

                // Set buttons based on type
                buttonsEl.innerHTML = '';
                
                // Check if this is a warning/alert type that should show single button
                const isWarningAlert = type === 'warning' || type === 'error' || type === 'info' || type === 'success';
                
                if (type === 'confirm' && !isWarningAlert) {
                    // Confirm modal: Batal and Ya button
                    const cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'flex-1 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-medium';
                    cancelBtn.style.cssText = 'background-color: #e5e7eb !important; color: #374151 !important; border: none !important; cursor: pointer;';
                    cancelBtn.textContent = options.cancelText || 'Batal';
                    cancelBtn.onclick = function() {
                        if (cancelCb) cancelCb();
                        closeUniversalModal();
                    };
                    
                    const confirmBtn = document.createElement('button');
                    confirmBtn.type = 'button';
                    confirmBtn.className = 'flex-1 px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium';
                    confirmBtn.style.cssText = 'background-color: #2563eb !important; color: #ffffff !important; border: none !important; cursor: pointer;';
                    confirmBtn.textContent = options.confirmText || 'Ya, Lanjutkan';
                    confirmBtn.onclick = function() {
                        if (callback) callback();
                        closeUniversalModal();
                    };
                    
                    buttonsEl.appendChild(cancelBtn);
                    buttonsEl.appendChild(confirmBtn);
                    confirmCallback = callback;
                    cancelCallback = cancelCb;
                } else {
                    // Alert/Warning modal: single button (Mengerti/OK)
                    const okBtn = document.createElement('button');
                    okBtn.type = 'button';
                    okBtn.className = 'w-full px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium';
                    okBtn.style.cssText = 'background-color: #2563eb !important; color: #ffffff !important; border: none !important; cursor: pointer;';
                    okBtn.textContent = options.okText || (type === 'warning' ? 'Mengerti' : 'OK');
                    okBtn.onclick = function() {
                        if (callback) callback();
                        closeUniversalModal();
                    };
                    buttonsEl.appendChild(okBtn);
                    confirmCallback = null;
                    cancelCallback = null;
                }
                
                // Show modal as overlay in center of screen
                modal.style.display = 'flex';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.right = '0';
                modal.style.bottom = '0';
                modal.style.zIndex = '99999';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                document.body.style.overflow = 'hidden';
                
                return true;
            }

            function closeUniversalModal() {
                const modal = document.getElementById('universalModal');
                if (modal) {
                    modal.style.display = 'none';
                }
                document.body.style.overflow = '';
                confirmCallback = null;
                cancelCallback = null;
            }

            // Custom alert function (non-blocking)
            window.showAlert = function(message, type = 'alert') {
                showUniversalModal(type, 'Notifikasi', message);
            };

            // Custom confirm function (callback-based)
            window.showConfirm = function(title, message, onConfirm, onCancel) {
                // Use 'warning' type for violation confirmations to show yellow warning icon
                // For violations, show single "Mengerti" button instead of confirm/cancel
                const isViolation = title && (title.includes('Peringatan') || title.includes('Pelanggaran') || title.includes('Dihentikan'));
                const modalType = isViolation ? 'warning' : 'confirm';
                
                if (isViolation) {
                    // Show as warning alert with single button
                    showUniversalModal(modalType, title || 'Peringatan', message, {
                        callback: onConfirm || function() {},
                        okText: 'Mengerti'
                    });
                } else {
                    // Show as confirm with cancel/confirm buttons
                    showUniversalModal(modalType, title || 'Konfirmasi', message, {
                        callback: onConfirm || function() {},
                        cancelCallback: onCancel || function() {}
                    });
                }
            };

            window.closeUniversalModal = closeUniversalModal;

            // Close modal when clicking outside
            setTimeout(function() {
                const modal = document.getElementById('universalModal');
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeUniversalModal();
                        }
                    });
                }
            }, 100);

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeUniversalModal();
                }
            });
        })();
    </script>
</body>
</html>

