import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'dart:async';
import '../../services/api_service.dart';

class ExamTakeScreen extends StatefulWidget {
  final int examId;
  final int sessionId;

  const ExamTakeScreen({
    Key? key,
    required this.examId,
    required this.sessionId,
  }) : super(key: key);

  @override
  State<ExamTakeScreen> createState() => _ExamTakeScreenState();
}

class _ExamTakeScreenState extends State<ExamTakeScreen> {
  Map<String, dynamic>? exam;
  Map<String, dynamic>? session;
  List<dynamic> questions = [];
  int currentQuestionIndex = 0;
  int waktuTersisa = 0; // in seconds
  Timer? timer;
  bool isLoading = true;
  bool isSubmitting = false;
  String? errorMessage;
  Map<int, String> pilganAnswers = {}; // question_id -> answer
  Map<int, String> essayAnswers = {}; // question_id -> answer
  Timer? autoSaveTimer;

  @override
  void initState() {
    super.initState();
    _loadExam();
    // Auto-save every 30 seconds
    autoSaveTimer = Timer.periodic(const Duration(seconds: 30), (_) {
      _autoSave();
    });
  }

  @override
  void dispose() {
    timer?.cancel();
    autoSaveTimer?.cancel();
    super.dispose();
  }

  Future<void> _loadExam() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/mahasiswa/exam/${widget.examId}/take/${widget.sessionId}',
      );
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          exam = data['exam'];
          session = data['session'];
          questions = data['questions'] ?? [];
          waktuTersisa = session!['waktu_tersisa'] ?? 0;

          // Load existing answers
          for (var q in questions) {
            final answer = q['answer'];
            if (answer != null) {
              if (q['tipe'] == 'pilgan' && answer['jawaban_pilgan'] != null) {
                pilganAnswers[q['id']] = answer['jawaban_pilgan'];
              } else if (q['tipe'] == 'essay' &&
                  answer['jawaban_essay'] != null) {
                essayAnswers[q['id']] = answer['jawaban_essay'];
              }
            }
          }

          isLoading = false;
        });

        // Start timer
        _startTimer();
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat ujian';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  void _startTimer() {
    timer?.cancel();
    timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (waktuTersisa > 0) {
        setState(() {
          waktuTersisa--;
        });
      } else {
        timer.cancel();
        _autoSubmit();
      }
    });
  }

  String _formatTime(int seconds) {
    final hours = seconds ~/ 3600;
    final minutes = (seconds % 3600) ~/ 60;
    final secs = seconds % 60;
    if (hours > 0) {
      return '${hours.toString().padLeft(2, '0')}:${minutes.toString().padLeft(2, '0')}:${secs.toString().padLeft(2, '0')}';
    }
    return '${minutes.toString().padLeft(2, '0')}:${secs.toString().padLeft(2, '0')}';
  }

  Future<void> _saveAnswer(
    int questionId,
    String? pilgan,
    String? essay,
  ) async {
    try {
      await ApiService.post('/mahasiswa/exam/${widget.examId}/save-answer', {
        'session_id': widget.sessionId,
        'question_id': questionId,
        if (pilgan != null) 'jawaban_pilgan': pilgan,
        if (essay != null) 'jawaban_essay': essay,
      });
    } catch (e) {
      // Silent fail for auto-save
      debugPrint('Auto-save failed: $e');
    }
  }

  Future<void> _autoSave() async {
    if (questions.isEmpty) return;

    final currentQ = questions[currentQuestionIndex];
    final questionId = currentQ['id'];

    if (currentQ['tipe'] == 'pilgan' && pilganAnswers.containsKey(questionId)) {
      await _saveAnswer(questionId, pilganAnswers[questionId], null);
    } else if (currentQ['tipe'] == 'essay' &&
        essayAnswers.containsKey(questionId)) {
      await _saveAnswer(questionId, null, essayAnswers[questionId]);
    }
  }

  Future<void> _submitExam() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Submit Ujian'),
        content: const Text(
          'Apakah Anda yakin ingin mengsubmit ujian? Setelah submit, Anda tidak dapat mengubah jawaban.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Submit'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    setState(() {
      isSubmitting = true;
    });

    try {
      // Save current answer first
      await _autoSave();

      final result = await ApiService.post(
        '/mahasiswa/exam/${widget.examId}/submit',
        {'session_id': widget.sessionId},
      );

      if (result['success'] == true) {
        if (mounted) {
          timer?.cancel();
          context.push(
            '/mahasiswa/exam/${widget.examId}/result/${widget.sessionId}',
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal submit ujian'),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: ${e.toString()}'),
            backgroundColor: Colors.red,
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() {
          isSubmitting = false;
        });
      }
    }
  }

  Future<void> _autoSubmit() async {
    timer?.cancel();
    await _autoSave();

    try {
      await ApiService.post('/mahasiswa/exam/${widget.examId}/submit', {
        'session_id': widget.sessionId,
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Waktu habis. Ujian otomatis disubmit.'),
            backgroundColor: Colors.orange,
          ),
        );
        context.push(
          '/mahasiswa/exam/${widget.examId}/result/${widget.sessionId}',
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error auto-submit: ${e.toString()}'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) async {
        if (didPop) return;
        final confirmed = await showDialog<bool>(
          context: context,
          builder: (context) => AlertDialog(
            title: const Text('Keluar Ujian'),
            content: const Text(
              'Apakah Anda yakin ingin keluar? Jawaban yang belum disimpan mungkin hilang.',
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Batal'),
              ),
              ElevatedButton(
                onPressed: () => Navigator.pop(context, true),
                style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                child: const Text('Keluar'),
              ),
            ],
          ),
        );
        if (confirmed == true && context.mounted) {
          Navigator.of(context).pop();
        }
      },
      child: Scaffold(
        appBar: AppBar(
          title: Text(exam?['judul'] ?? 'Ujian'),
          automaticallyImplyLeading: false,
          actions: [
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              margin: const EdgeInsets.only(right: 8),
              decoration: BoxDecoration(
                color: waktuTersisa < 300
                    ? Colors.red[700]
                    : waktuTersisa < 600
                    ? Colors.orange[700]
                    : Colors.blue[700],
                borderRadius: BorderRadius.circular(20),
              ),
              child: Text(
                _formatTime(waktuTersisa),
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
            ),
          ],
        ),
        body: isLoading
            ? const Center(child: CircularProgressIndicator())
            : errorMessage != null
            ? Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                    const SizedBox(height: 16),
                    Text(
                      errorMessage!,
                      style: TextStyle(color: Colors.red[700]),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: _loadExam,
                      child: const Text('Coba Lagi'),
                    ),
                  ],
                ),
              )
            : questions.isEmpty
            ? const Center(child: Text('Tidak ada soal'))
            : Column(
                children: [
                  // Question Navigation
                  Container(
                    padding: const EdgeInsets.all(8),
                    color: Colors.grey[200],
                    child: Row(
                      children: [
                        Expanded(
                          child: SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            child: Row(
                              children: List.generate(
                                questions.length,
                                (index) => GestureDetector(
                                  onTap: () {
                                    setState(() {
                                      currentQuestionIndex = index;
                                    });
                                    _autoSave();
                                  },
                                  child: Container(
                                    width: 40,
                                    height: 40,
                                    margin: const EdgeInsets.symmetric(
                                      horizontal: 4,
                                    ),
                                    decoration: BoxDecoration(
                                      color: index == currentQuestionIndex
                                          ? Colors.blue
                                          : (questions[index]['answer'] !=
                                                    null &&
                                                questions[index]['answer']['is_answered'] ==
                                                    true)
                                          ? Colors.green
                                          : Colors.grey[300],
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: Center(
                                      child: Text(
                                        '${index + 1}',
                                        style: TextStyle(
                                          color: index == currentQuestionIndex
                                              ? Colors.white
                                              : Colors.black87,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                        Text(
                          '${currentQuestionIndex + 1}/${questions.length}',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ),

                  // Question Content
                  Expanded(
                    child: SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: _buildQuestion(questions[currentQuestionIndex]),
                    ),
                  ),

                  // Navigation Buttons
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      boxShadow: [
                        BoxShadow(
                          color: Colors.grey.withOpacity(0.3),
                          spreadRadius: 1,
                          blurRadius: 5,
                        ),
                      ],
                    ),
                    child: Row(
                      children: [
                        if (currentQuestionIndex > 0)
                          Expanded(
                            child: OutlinedButton(
                              onPressed: () {
                                setState(() {
                                  currentQuestionIndex--;
                                });
                                _autoSave();
                              },
                              child: const Text('Sebelumnya'),
                            ),
                          ),
                        if (currentQuestionIndex > 0) const SizedBox(width: 8),
                        if (currentQuestionIndex < questions.length - 1)
                          Expanded(
                            child: ElevatedButton(
                              onPressed: () {
                                setState(() {
                                  currentQuestionIndex++;
                                });
                                _autoSave();
                              },
                              child: const Text('Selanjutnya'),
                            ),
                          ),
                        if (currentQuestionIndex == questions.length - 1) ...[
                          const SizedBox(width: 8),
                          Expanded(
                            child: ElevatedButton(
                              onPressed: isSubmitting ? null : _submitExam,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.green,
                              ),
                              child: isSubmitting
                                  ? const SizedBox(
                                      height: 20,
                                      width: 20,
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                        valueColor:
                                            AlwaysStoppedAnimation<Color>(
                                              Colors.white,
                                            ),
                                      ),
                                    )
                                  : const Text('Submit'),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
      ),
    );
  }

  Widget _buildQuestion(Map<String, dynamic> question) {
    final questionId = question['id'];
    final tipe = question['tipe'];
    final pertanyaan = question['pertanyaan'] ?? '';
    final bobot = question['bobot'] ?? 0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.blue[50],
            borderRadius: BorderRadius.circular(8),
          ),
          child: Row(
            children: [
              Text(
                'Soal ${currentQuestionIndex + 1}',
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
              const Spacer(),
              Text(
                'Bobot: $bobot',
                style: TextStyle(color: Colors.grey[700], fontSize: 12),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),
        Text(pertanyaan, style: const TextStyle(fontSize: 16, height: 1.6)),
        const SizedBox(height: 16),
        if (tipe == 'pilgan')
          _buildPilganQuestion(questionId, question['pilihan'] ?? {})
        else if (tipe == 'essay')
          _buildEssayQuestion(questionId),
      ],
    );
  }

  Widget _buildPilganQuestion(int questionId, Map<String, dynamic> pilihan) {
    final currentAnswer = pilganAnswers[questionId] ?? '';

    return Column(
      children: pilihan.entries.map((entry) {
        final key = entry.key;
        final value = entry.value;
        final isSelected = currentAnswer == key;

        return Card(
          margin: const EdgeInsets.only(bottom: 8),
          color: isSelected ? Colors.blue[50] : Colors.white,
          child: RadioListTile<String>(
            title: Text(value ?? ''),
            value: key,
            groupValue: currentAnswer.isEmpty ? null : currentAnswer,
            onChanged: (value) {
              if (value != null) {
                setState(() {
                  pilganAnswers[questionId] = value;
                });
                _saveAnswer(questionId, value, null);
              }
            },
          ),
        );
      }).toList(),
    );
  }

  Widget _buildEssayQuestion(int questionId) {
    final currentAnswer = essayAnswers[questionId] ?? '';

    return TextFormField(
      initialValue: currentAnswer,
      decoration: InputDecoration(
        hintText: 'Tulis jawaban Anda di sini...',
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
        filled: true,
        fillColor: Colors.grey[100],
      ),
      maxLines: 10,
      onChanged: (value) {
        setState(() {
          essayAnswers[questionId] = value;
        });
        // Auto-save after 2 seconds of no typing
        Future.delayed(const Duration(seconds: 2), () {
          if (essayAnswers[questionId] == value) {
            _saveAnswer(questionId, null, value);
          }
        });
      },
    );
  }
}
