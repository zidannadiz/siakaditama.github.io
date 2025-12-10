import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class ExamResultScreen extends StatefulWidget {
  final int examId;
  final int sessionId;

  const ExamResultScreen({
    Key? key,
    required this.examId,
    required this.sessionId,
  }) : super(key: key);

  @override
  State<ExamResultScreen> createState() => _ExamResultScreenState();
}

class _ExamResultScreenState extends State<ExamResultScreen> {
  Map<String, dynamic>? exam;
  Map<String, dynamic>? session;
  List<dynamic> questions = [];
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadResult();
  }

  Future<void> _loadResult() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/mahasiswa/exam/${widget.examId}/result/${widget.sessionId}',
      );
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          exam = data['exam'];
          session = data['session'];
          questions = data['questions'] ?? [];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat hasil';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Color _getScoreColor(double? nilai) {
    if (nilai == null) return Colors.grey;
    if (nilai >= 80) return Colors.green;
    if (nilai >= 60) return Colors.orange;
    return Colors.red;
  }

  String _getScoreLabel(double? nilai) {
    if (nilai == null) return 'Belum dinilai';
    if (nilai >= 80) return 'Sangat Baik';
    if (nilai >= 60) return 'Baik';
    if (nilai >= 40) return 'Cukup';
    return 'Kurang';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Hasil Ujian'),
        automaticallyImplyLeading: false,
        actions: [
          IconButton(
            icon: const Icon(Icons.home),
            onPressed: () {
              context.go('/dashboard');
            },
            tooltip: 'Kembali ke Dashboard',
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
                    onPressed: _loadResult,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : exam == null
          ? const Center(child: Text('Hasil tidak ditemukan'))
          : RefreshIndicator(
              onRefresh: _loadResult,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Exam Info
                    Card(
                      color: Colors.blue[50],
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              exam!['judul'] ?? '-',
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              exam!['mata_kuliah'] ?? '-',
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey[700],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Score Card
                    if (exam!['tampilkan_nilai'] == true &&
                        session!['nilai'] != null)
                      Card(
                        color: _getScoreColor(session!['nilai']),
                        child: Padding(
                          padding: const EdgeInsets.all(24),
                          child: Column(
                            children: [
                              const Text(
                                'Nilai Akhir',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.white,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                '${session!['nilai']}',
                                style: const TextStyle(
                                  fontSize: 48,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                _getScoreLabel(session!['nilai']),
                                style: const TextStyle(
                                  fontSize: 16,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      )
                    else if (exam!['tampilkan_nilai'] == false)
                      Card(
                        color: Colors.grey[300],
                        child: const Padding(
                          padding: EdgeInsets.all(16),
                          child: Center(
                            child: Text(
                              'Nilai akan ditampilkan setelah dinilai oleh dosen',
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.black87,
                              ),
                            ),
                          ),
                        ),
                      ),
                    const SizedBox(height: 16),

                    // Questions Review
                    const Text(
                      'Review Jawaban',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 8),
                    ...questions.asMap().entries.map((entry) {
                      final index = entry.key;
                      final question = entry.value;
                      return _buildQuestionReview(index + 1, question);
                    }),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildQuestionReview(int number, Map<String, dynamic> question) {
    final tipe = question['tipe'];
    final pertanyaan = question['pertanyaan'] ?? '';
    final pilihan = question['pilihan'] ?? {};
    final jawabanBenar = question['jawaban_benar'];
    final bobot = question['bobot'] ?? 0;
    final penjelasan = question['penjelasan'];
    final answer = question['answer'];
    final jawabanPilgan = answer?['jawaban_pilgan'];
    final jawabanEssay = answer?['jawaban_essay'];
    final nilai = answer?['nilai'];

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 6,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.blue[100],
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text(
                    'Soal $number',
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      color: Colors.blue[900],
                    ),
                  ),
                ),
                const Spacer(),
                if (nilai != null)
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: _getScoreColor(nilai).withOpacity(0.2),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      'Nilai: $nilai / $bobot',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        color: _getScoreColor(nilai),
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 12),
            Text(pertanyaan, style: const TextStyle(fontSize: 16, height: 1.6)),
            const SizedBox(height: 16),
            if (tipe == 'pilgan') ...[
              ...pilihan.entries.map((entry) {
                final key = entry.key;
                final value = entry.value;
                final isSelected = jawabanPilgan == key;
                final isCorrectAnswer = key == jawabanBenar;

                Color? bgColor;
                IconData? icon;
                Color? iconColor;

                if (isCorrectAnswer) {
                  bgColor = Colors.green[50];
                  icon = Icons.check_circle;
                  iconColor = Colors.green;
                } else if (isSelected && !isCorrectAnswer) {
                  bgColor = Colors.red[50];
                  icon = Icons.cancel;
                  iconColor = Colors.red;
                }

                return Container(
                  margin: const EdgeInsets.only(bottom: 8),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: bgColor,
                    border: Border.all(
                      color: isSelected
                          ? (isCorrectAnswer ? Colors.green : Colors.red)
                          : Colors.grey[300]!,
                      width: isSelected ? 2 : 1,
                    ),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      if (icon != null) ...[
                        Icon(icon, color: iconColor, size: 20),
                        const SizedBox(width: 8),
                      ],
                      Expanded(child: Text(value ?? '')),
                      if (isCorrectAnswer)
                        Text(
                          'Jawaban Benar',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: Colors.green[700],
                          ),
                        ),
                    ],
                  ),
                );
              }),
            ] else if (tipe == 'essay') ...[
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.grey[100],
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Jawaban Anda:',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      jawabanEssay ?? '(Tidak dijawab)',
                      style: const TextStyle(fontSize: 14),
                    ),
                  ],
                ),
              ),
            ],
            if (penjelasan != null && penjelasan.isNotEmpty) ...[
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.blue[50],
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Penjelasan:',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(penjelasan, style: const TextStyle(fontSize: 14)),
                  ],
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
