import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class ExamListScreen extends StatefulWidget {
  const ExamListScreen({Key? key}) : super(key: key);

  @override
  State<ExamListScreen> createState() => _ExamListScreenState();
}

class _ExamListScreenState extends State<ExamListScreen> {
  List<dynamic> exams = [];
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadExams();
  }

  Future<void> _loadExams() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/mahasiswa/exam');
      if (result['success'] == true) {
        setState(() {
          exams = result['data'] ?? [];
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

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  Color _getStatusColor(
    bool isOngoing,
    bool isFinished,
    Map<String, dynamic>? session,
  ) {
    if (session != null && session['status'] != null) {
      final status = session['status'];
      if (status == 'submitted' || status == 'auto_submitted') {
        return Colors.green;
      } else if (status == 'started') {
        return Colors.blue;
      }
    }
    if (isFinished) {
      return Colors.grey;
    } else if (isOngoing) {
      return Colors.orange;
    } else {
      return Colors.blue;
    }
  }

  String _getStatusLabel(
    bool isOngoing,
    bool isFinished,
    Map<String, dynamic>? session,
  ) {
    if (session != null && session['status'] != null) {
      final status = session['status'];
      if (status == 'submitted' || status == 'auto_submitted') {
        return 'Selesai';
      } else if (status == 'started') {
        return 'Sedang Dikerjakan';
      }
    }
    if (isFinished) {
      return 'Selesai';
    } else if (isOngoing) {
      return 'Berlangsung';
    } else {
      return 'Belum Dimulai';
    }
  }

  IconData _getStatusIcon(
    bool isOngoing,
    bool isFinished,
    Map<String, dynamic>? session,
  ) {
    if (session != null && session['status'] != null) {
      final status = session['status'];
      if (status == 'submitted' || status == 'auto_submitted') {
        return Icons.check_circle;
      } else if (status == 'started') {
        return Icons.quiz;
      }
    }
    if (isFinished) {
      return Icons.event_busy;
    } else if (isOngoing) {
      return Icons.access_time;
    } else {
      return Icons.schedule;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Ujian'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadExams,
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
                    onPressed: _loadExams,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadExams,
              child: exams.isEmpty
                  ? Center(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.quiz_outlined,
                            size: 64,
                            color: Colors.grey[400],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'Belum ada ujian',
                            style: TextStyle(
                              fontSize: 16,
                              color: Colors.grey[600],
                            ),
                          ),
                        ],
                      ),
                    )
                  : ListView.builder(
                      padding: const EdgeInsets.all(8),
                      itemCount: exams.length,
                      itemBuilder: (context, index) {
                        final exam = exams[index];
                        final isOngoing = exam['is_ongoing'] == true;
                        final isFinished = exam['is_finished'] == true;
                        final session = exam['session'];
                        final statusColor = _getStatusColor(
                          isOngoing,
                          isFinished,
                          session,
                        );

                        return Card(
                          margin: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          child: ListTile(
                            leading: Container(
                              width: 50,
                              height: 50,
                              decoration: BoxDecoration(
                                color: statusColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(25),
                              ),
                              child: Icon(
                                _getStatusIcon(isOngoing, isFinished, session),
                                color: statusColor,
                                size: 24,
                              ),
                            ),
                            title: Text(
                              exam['judul'] ?? '-',
                              style: const TextStyle(
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const SizedBox(height: 4),
                                Text(
                                  '${exam['mata_kuliah'] ?? '-'} | ${exam['dosen'] ?? '-'}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Mulai: ${_formatDate(exam['mulai'])}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                                Text(
                                  'Selesai: ${_formatDate(exam['selesai'])}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                                Text(
                                  'Durasi: ${exam['durasi'] ?? 0} menit | ${exam['total_soal'] ?? 0} soal',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                                if (session != null && session['nilai'] != null)
                                  Padding(
                                    padding: const EdgeInsets.only(top: 4),
                                    child: Text(
                                      'Nilai: ${session['nilai']}',
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.blue[700],
                                      ),
                                    ),
                                  ),
                              ],
                            ),
                            trailing: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 8,
                                    vertical: 4,
                                  ),
                                  decoration: BoxDecoration(
                                    color: statusColor.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  child: Text(
                                    _getStatusLabel(
                                      isOngoing,
                                      isFinished,
                                      session,
                                    ),
                                    style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: statusColor,
                                    ),
                                  ),
                                ),
                                const Icon(Icons.arrow_forward_ios, size: 16),
                              ],
                            ),
                            onTap: () {
                              context.push('/mahasiswa/exam/${exam['id']}');
                            },
                          ),
                        );
                      },
                    ),
            ),
    );
  }
}
