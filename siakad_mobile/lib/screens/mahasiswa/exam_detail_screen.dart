import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class ExamDetailScreen extends StatefulWidget {
  final int examId;

  const ExamDetailScreen({Key? key, required this.examId}) : super(key: key);

  @override
  State<ExamDetailScreen> createState() => _ExamDetailScreenState();
}

class _ExamDetailScreenState extends State<ExamDetailScreen> {
  Map<String, dynamic>? exam;
  Map<String, dynamic>? session;
  bool isLoading = true;
  bool isStarting = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadExam();
  }

  Future<void> _loadExam() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/mahasiswa/exam/${widget.examId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          exam = data['exam'];
          session = data['session'];
          isLoading = false;
        });
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

  Future<void> _startExam() async {
    setState(() {
      isStarting = true;
    });

    try {
      final result = await ApiService.post(
        '/mahasiswa/exam/${widget.examId}/start',
        {},
      );
      if (result['success'] == true) {
        final sessionId = result['data']['session_id'];
        if (mounted) {
          context.push('/mahasiswa/exam/${widget.examId}/take/$sessionId');
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal memulai ujian'),
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
          isStarting = false;
        });
      }
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Ujian'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadExam,
            tooltip: 'Refresh',
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
          : exam == null
          ? const Center(child: Text('Ujian tidak ditemukan'))
          : RefreshIndicator(
              onRefresh: _loadExam,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Exam Info Card
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
                            const SizedBox(height: 12),
                            Text(
                              exam!['deskripsi'] ?? '-',
                              style: const TextStyle(fontSize: 14, height: 1.6),
                            ),
                            const SizedBox(height: 12),
                            Row(
                              children: [
                                Icon(
                                  Icons.school,
                                  size: 16,
                                  color: Colors.grey[600],
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  '${exam!['mata_kuliah'] ?? '-'} (${exam!['kode_mk'] ?? '-'})',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[700],
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 4),
                            Row(
                              children: [
                                Icon(
                                  Icons.person,
                                  size: 16,
                                  color: Colors.grey[600],
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Dosen: ${exam!['dosen'] ?? '-'}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[700],
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.quiz,
                              'Total Soal',
                              '${exam!['total_soal'] ?? 0} soal',
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.access_time,
                              'Durasi',
                              '${exam!['durasi'] ?? 0} menit',
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.calendar_today,
                              'Mulai',
                              _formatDate(exam!['mulai']),
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.event_busy,
                              'Selesai',
                              _formatDate(exam!['selesai']),
                            ),
                            if (exam!['random_soal'] == true) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(Icons.shuffle, 'Soal', 'Acak'),
                            ],
                            if (exam!['random_pilihan'] == true) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(Icons.shuffle, 'Pilihan', 'Acak'),
                            ],
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Session Status
                    if (session != null)
                      Card(
                        color:
                            session!['status'] == 'submitted' ||
                                session!['status'] == 'auto_submitted'
                            ? Colors.green[50]
                            : Colors.blue[50],
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  Icon(
                                    session!['status'] == 'submitted' ||
                                            session!['status'] ==
                                                'auto_submitted'
                                        ? Icons.check_circle
                                        : Icons.quiz,
                                    color:
                                        session!['status'] == 'submitted' ||
                                            session!['status'] ==
                                                'auto_submitted'
                                        ? Colors.green[700]
                                        : Colors.blue[700],
                                  ),
                                  const SizedBox(width: 8),
                                  Text(
                                    session!['status'] == 'submitted' ||
                                            session!['status'] ==
                                                'auto_submitted'
                                        ? 'Ujian Selesai'
                                        : 'Sedang Dikerjakan',
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      color:
                                          session!['status'] == 'submitted' ||
                                              session!['status'] ==
                                                  'auto_submitted'
                                          ? Colors.green[700]
                                          : Colors.blue[700],
                                    ),
                                  ),
                                ],
                              ),
                              if (session!['nilai'] != null) ...[
                                const SizedBox(height: 12),
                                Container(
                                  padding: const EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: Colors.blue[100],
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Row(
                                    children: [
                                      const Icon(
                                        Icons.grade,
                                        color: Colors.blue,
                                      ),
                                      const SizedBox(width: 8),
                                      Text(
                                        'Nilai: ${session!['nilai']}',
                                        style: const TextStyle(
                                          fontSize: 18,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.blue,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                      ),
                    const SizedBox(height: 16),

                    // Action Button
                    if (session == null)
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: isStarting ? null : _startExam,
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            backgroundColor: Colors.green,
                          ),
                          child: isStarting
                              ? const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white,
                                    ),
                                  ),
                                )
                              : const Text(
                                  'Mulai Ujian',
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                        ),
                      )
                    else if (session!['status'] == 'started')
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: () {
                            context.push(
                              '/mahasiswa/exam/${widget.examId}/take/${session!['id']}',
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            backgroundColor: Colors.blue,
                          ),
                          child: const Text(
                            'Lanjutkan Ujian',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      )
                    else if (session!['status'] == 'submitted' ||
                        session!['status'] == 'auto_submitted')
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: () {
                            context.push(
                              '/mahasiswa/exam/${widget.examId}/result/${session!['id']}',
                            );
                          },
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            backgroundColor: Colors.green,
                          ),
                          child: const Text(
                            'Lihat Hasil',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                      ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: TextStyle(fontSize: 12, color: Colors.grey[700]),
        ),
        Text(
          value,
          style: TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.bold,
            color: Colors.grey[900],
          ),
        ),
      ],
    );
  }
}
