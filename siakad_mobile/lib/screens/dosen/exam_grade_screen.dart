import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class ExamGradeScreen extends StatefulWidget {
  final int examId;
  final int sessionId;

  const ExamGradeScreen({
    Key? key,
    required this.examId,
    required this.sessionId,
  }) : super(key: key);

  @override
  State<ExamGradeScreen> createState() => _ExamGradeScreenState();
}

class _ExamGradeScreenState extends State<ExamGradeScreen> {
  Map<String, dynamic>? exam;
  Map<String, dynamic>? session;
  List<dynamic> essayAnswers = [];
  Map<int, TextEditingController> _nilaiControllers = {};
  Map<int, TextEditingController> _feedbackControllers = {};
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _nilaiControllers.values.forEach((controller) => controller.dispose());
    _feedbackControllers.values.forEach((controller) => controller.dispose());
    super.dispose();
  }

  Future<void> _loadData() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
          '/dosen/exam/${widget.examId}/grade/${widget.sessionId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          exam = data['exam'];
          session = data['session'];
          essayAnswers = data['essay_answers'] ?? [];

          // Initialize controllers
          for (var answer in essayAnswers) {
            final answerId = answer['id'];
            _nilaiControllers[answerId] = TextEditingController(
              text: answer['nilai']?.toString() ?? '',
            );
            _feedbackControllers[answerId] = TextEditingController(
              text: answer['feedback'] ?? '',
            );
          }

          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _saveGrades() async {
    // Validate all nilai fields
    bool hasError = false;
    for (var answer in essayAnswers) {
      final answerId = answer['id'];
      final nilaiText = _nilaiControllers[answerId]?.text ?? '';
      if (nilaiText.trim().isEmpty) {
        hasError = true;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Semua nilai wajib diisi'),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
      final nilai = double.tryParse(nilaiText);
      if (nilai == null || nilai < 0) {
        hasError = true;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Nilai harus angka positif'),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    }

    if (hasError) return;

    setState(() {
      isSaving = true;
    });

    try {
      final answers = essayAnswers.map((answer) {
        final answerId = answer['id'];
        return {
          'id': answerId,
          'nilai': double.parse(_nilaiControllers[answerId]!.text),
          'feedback': _feedbackControllers[answerId]?.text.trim() ?? '',
        };
      }).toList();

      final result = await ApiService.post(
          '/dosen/exam/${widget.examId}/grade/${widget.sessionId}',
          {'answers': answers});

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Nilai berhasil disimpan'),
              backgroundColor: Colors.green,
            ),
          );
          context.pop(true);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menyimpan nilai'),
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
          isSaving = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Nilai Ujian'),
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : errorMessage != null
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.error_outline,
                          size: 64, color: Colors.red[300]),
                      const SizedBox(height: 16),
                      Text(
                        errorMessage!,
                        style: TextStyle(color: Colors.red[700]),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 16),
                      ElevatedButton(
                        onPressed: _loadData,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : exam == null || session == null
                  ? const Center(child: Text('Data tidak ditemukan'))
                  : essayAnswers.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.quiz_outlined,
                                  size: 64, color: Colors.grey[400]),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada soal essay yang perlu dinilai',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : Column(
                          children: [
                            // Info Card
                            Container(
                              padding: const EdgeInsets.all(16),
                              color: Colors.blue[50],
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    exam!['judul'] ?? '-',
                                    style: const TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Mahasiswa: ${session!['mahasiswa']['nama'] ?? '-'} (${session!['mahasiswa']['nim'] ?? '-'})',
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: Colors.grey[700],
                                    ),
                                  ),
                                  if (session!['nilai'] != null)
                                    Padding(
                                      padding: const EdgeInsets.only(top: 8),
                                      child: Text(
                                        'Nilai Sementara: ${session!['nilai']}',
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.blue,
                                        ),
                                      ),
                                    ),
                                ],
                              ),
                            ),

                            // Essay Answers
                            Expanded(
                              child: SingleChildScrollView(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const Text(
                                      'Nilai Soal Essay',
                                      style: TextStyle(
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 16),
                                    ...essayAnswers.asMap().entries.map((entry) {
                                      final index = entry.key;
                                      final answer = entry.value;
                                      final answerId = answer['id'];
                                      final question = answer['question'];
                                      final maxNilai = question['bobot'] ?? 0;

                                      return Card(
                                        margin: const EdgeInsets.only(bottom: 16),
                                        child: Padding(
                                          padding: const EdgeInsets.all(16),
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Row(
                                                children: [
                                                  Container(
                                                    padding:
                                                        const EdgeInsets.symmetric(
                                                      horizontal: 12,
                                                      vertical: 6,
                                                    ),
                                                    decoration: BoxDecoration(
                                                      color: Colors.blue[100],
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                              12),
                                                    ),
                                                    child: Text(
                                                      'Soal ${index + 1}',
                                                      style: TextStyle(
                                                        fontWeight:
                                                            FontWeight.bold,
                                                        color: Colors.blue[900],
                                                      ),
                                                    ),
                                                  ),
                                                  const Spacer(),
                                                  Text(
                                                    'Bobot: $maxNilai',
                                                    style: TextStyle(
                                                      fontSize: 12,
                                                      color: Colors.grey[600],
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              const SizedBox(height: 12),
                                              Text(
                                                question['pertanyaan'] ?? '-',
                                                style: const TextStyle(
                                                  fontSize: 14,
                                                  fontWeight: FontWeight.w600,
                                                ),
                                              ),
                                              const SizedBox(height: 12),
                                              Container(
                                                padding: const EdgeInsets.all(12),
                                                decoration: BoxDecoration(
                                                  color: Colors.grey[100],
                                                  borderRadius:
                                                      BorderRadius.circular(8),
                                                ),
                                                child: Column(
                                                  crossAxisAlignment:
                                                      CrossAxisAlignment.start,
                                                  children: [
                                                    const Text(
                                                      'Jawaban Mahasiswa:',
                                                      style: TextStyle(
                                                        fontSize: 12,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                      ),
                                                    ),
                                                    const SizedBox(height: 8),
                                                    Text(
                                                      answer['jawaban_essay'] ??
                                                          '(Tidak dijawab)',
                                                      style: const TextStyle(
                                                        fontSize: 14,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              ),
                                              const SizedBox(height: 16),
                                              TextFormField(
                                                controller:
                                                    _nilaiControllers[answerId],
                                                decoration: InputDecoration(
                                                  labelText:
                                                      'Nilai (0-$maxNilai) *',
                                                  hintText: 'Masukkan nilai',
                                                  border: OutlineInputBorder(
                                                    borderRadius:
                                                        BorderRadius.circular(12),
                                                  ),
                                                  filled: true,
                                                  fillColor: Colors.grey[100],
                                                ),
                                                keyboardType:
                                                    TextInputType.number,
                                              ),
                                              const SizedBox(height: 16),
                                              TextFormField(
                                                controller: _feedbackControllers[
                                                    answerId],
                                                decoration: InputDecoration(
                                                  labelText: 'Feedback',
                                                  hintText:
                                                      'Masukkan feedback untuk mahasiswa',
                                                  border: OutlineInputBorder(
                                                    borderRadius:
                                                        BorderRadius.circular(12),
                                                  ),
                                                  filled: true,
                                                  fillColor: Colors.grey[100],
                                                ),
                                                maxLines: 3,
                                              ),
                                            ],
                                          ),
                                        ),
                                      );
                                    }).toList(),
                                  ],
                                ),
                              ),
                            ),

                            // Save Button
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
                              child: SizedBox(
                                width: double.infinity,
                                child: ElevatedButton(
                                  onPressed: isSaving ? null : _saveGrades,
                                  style: ElevatedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                  ),
                                  child: isSaving
                                      ? const SizedBox(
                                          height: 20,
                                          width: 20,
                                          child: CircularProgressIndicator(
                                            strokeWidth: 2,
                                            valueColor:
                                                AlwaysStoppedAnimation<Color>(
                                                    Colors.white),
                                          ),
                                        )
                                      : const Text('Simpan Nilai'),
                                ),
                              ),
                            ),
                          ],
                        ),
    );
  }
}

