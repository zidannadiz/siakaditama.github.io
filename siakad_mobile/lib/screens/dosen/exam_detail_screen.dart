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
  List<dynamic> questions = [];
  bool isLoading = true;
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
      final result = await ApiService.get('/dosen/exam/${widget.examId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          exam = data['exam'];
          questions = data['questions'] ?? [];
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

  Future<void> _deleteQuestion(int questionId) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Soal'),
        content: const Text('Apakah Anda yakin ingin menghapus soal ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final result = await ApiService.delete(
          '/dosen/exam/${widget.examId}/question/$questionId');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Soal berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadExam();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus soal'),
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
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Detail Ujian'),
          actions: [
            IconButton(
              icon: const Icon(Icons.edit),
              onPressed: () async {
                final result = await context.push(
                    '/dosen/exam/${widget.examId}/edit');
                if (result == true) {
                  _loadExam();
                }
              },
              tooltip: 'Edit',
            ),
            IconButton(
              icon: const Icon(Icons.refresh),
              onPressed: _loadExam,
              tooltip: 'Refresh',
            ),
          ],
          bottom: const TabBar(
            tabs: [
              Tab(text: 'Info', icon: Icon(Icons.info)),
              Tab(text: 'Soal', icon: Icon(Icons.quiz)),
            ],
          ),
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
                        child: TabBarView(
                          children: [
                            // Info Tab
                            SingleChildScrollView(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Card(
                                    color: Colors.blue[50],
                                    child: Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            children: [
                                              Expanded(
                                                child: Text(
                                                  exam!['judul'] ?? '-',
                                                  style: const TextStyle(
                                                    fontSize: 18,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ),
                                              Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                  horizontal: 8,
                                                  vertical: 4,
                                                ),
                                                decoration: BoxDecoration(
                                                  color: exam!['status'] ==
                                                          'published'
                                                      ? Colors.green[100]
                                                      : Colors.grey[300],
                                                  borderRadius:
                                                      BorderRadius.circular(12),
                                                ),
                                                child: Text(
                                                  exam!['status'] == 'published'
                                                      ? 'Published'
                                                      : 'Draft',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: exam!['status'] ==
                                                            'published'
                                                        ? Colors.green[700]
                                                        : Colors.grey[700],
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 12),
                                          Text(
                                            exam!['deskripsi'] ?? '-',
                                            style: const TextStyle(
                                              fontSize: 14,
                                              height: 1.6,
                                            ),
                                          ),
                                          const SizedBox(height: 12),
                                          Text(
                                            '${exam!['mata_kuliah'] ?? '-'} (${exam!['kode_mk'] ?? '-'})',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey[700],
                                            ),
                                          ),
                                          const SizedBox(height: 12),
                                          _buildInfoRow(
                                            Icons.quiz,
                                            'Tipe',
                                            exam!['tipe'] ?? '-',
                                          ),
                                          const SizedBox(height: 8),
                                          _buildInfoRow(
                                            Icons.access_time,
                                            'Durasi',
                                            '${exam!['durasi'] ?? 0} menit',
                                          ),
                                          const SizedBox(height: 8),
                                          _buildInfoRow(
                                            Icons.numbers,
                                            'Total Soal',
                                            '${exam!['total_soal'] ?? 0} soal',
                                          ),
                                          const SizedBox(height: 8),
                                          _buildInfoRow(
                                            Icons.scale,
                                            'Bobot',
                                            '${exam!['bobot'] ?? 0}',
                                          ),
                                          if (exam!['mulai'] != null) ...[
                                            const SizedBox(height: 8),
                                            _buildInfoRow(
                                              Icons.calendar_today,
                                              'Mulai',
                                              _formatDate(exam!['mulai']),
                                            ),
                                          ],
                                          const SizedBox(height: 8),
                                          _buildInfoRow(
                                            Icons.event_busy,
                                            'Selesai',
                                            _formatDate(exam!['selesai']),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 16),
                                  // Settings
                                  Card(
                                    child: Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          const Text(
                                            'Pengaturan',
                                            style: TextStyle(
                                              fontSize: 16,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                          const SizedBox(height: 8),
                                          _buildSettingRow(
                                            'Acak Soal',
                                            exam!['random_soal'] == true,
                                          ),
                                          _buildSettingRow(
                                            'Acak Pilihan',
                                            exam!['random_pilihan'] == true,
                                          ),
                                          _buildSettingRow(
                                            'Tampilkan Nilai',
                                            exam!['tampilkan_nilai'] == true,
                                          ),
                                          _buildSettingRow(
                                            'Prevent Copy Paste',
                                            exam!['prevent_copy_paste'] == true,
                                          ),
                                          _buildSettingRow(
                                            'Prevent New Tab',
                                            exam!['prevent_new_tab'] == true,
                                          ),
                                          _buildSettingRow(
                                            'Fullscreen Mode',
                                            exam!['fullscreen_mode'] == true,
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 16),
                                  // Actions
                                  SizedBox(
                                    width: double.infinity,
                                    child: ElevatedButton.icon(
                                      onPressed: () {
                                        context.push(
                                            '/dosen/exam/${widget.examId}/results');
                                      },
                                      icon: const Icon(Icons.assessment),
                                      label: const Text('Lihat Hasil'),
                                      style: ElevatedButton.styleFrom(
                                        padding: const EdgeInsets.symmetric(
                                          vertical: 16,
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            // Questions Tab
                            questions.isEmpty
                                ? Center(
                                    child: Column(
                                      mainAxisAlignment:
                                          MainAxisAlignment.center,
                                      children: [
                                        Icon(Icons.quiz_outlined,
                                            size: 64, color: Colors.grey[400]),
                                        const SizedBox(height: 16),
                                        Text(
                                          'Belum ada soal',
                                          style: TextStyle(
                                            fontSize: 16,
                                            color: Colors.grey[600],
                                          ),
                                        ),
                                        const SizedBox(height: 16),
                                        ElevatedButton.icon(
                                          onPressed: () async {
                                            final result = await context.push(
                                                '/dosen/exam/${widget.examId}/question/add');
                                            if (result == true) {
                                              _loadExam();
                                            }
                                          },
                                          icon: const Icon(Icons.add),
                                          label: const Text('Tambah Soal'),
                                        ),
                                      ],
                                    ),
                                  )
                                : Column(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(16),
                                        color: Colors.blue[50],
                                        child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.spaceBetween,
                                          children: [
                                            Text(
                                              'Total: ${questions.length} soal',
                                              style: const TextStyle(
                                                fontWeight: FontWeight.bold,
                                              ),
                                            ),
                                            ElevatedButton.icon(
                                              onPressed: () async {
                                                final result = await context
                                                    .push(
                                                        '/dosen/exam/${widget.examId}/question/add');
                                                if (result == true) {
                                                  _loadExam();
                                                }
                                              },
                                              icon: const Icon(Icons.add),
                                              label: const Text('Tambah Soal'),
                                            ),
                                          ],
                                        ),
                                      ),
                                      Expanded(
                                        child: ListView.builder(
                                          padding: const EdgeInsets.all(8),
                                          itemCount: questions.length,
                                          itemBuilder: (context, index) {
                                            final question = questions[index];
                                            return Card(
                                              margin: const EdgeInsets.symmetric(
                                                horizontal: 8,
                                                vertical: 4,
                                              ),
                                              child: ListTile(
                                                leading: CircleAvatar(
                                                  backgroundColor:
                                                      question['tipe'] ==
                                                              'pilgan'
                                                          ? Colors.blue[100]
                                                          : Colors.green[100],
                                                  child: Text(
                                                    '${index + 1}',
                                                    style: TextStyle(
                                                      fontWeight: FontWeight.bold,
                                                      color: question['tipe'] ==
                                                              'pilgan'
                                                          ? Colors.blue[700]
                                                          : Colors.green[700],
                                                    ),
                                                  ),
                                                ),
                                                title: Text(
                                                  question['pertanyaan'] ?? '-',
                                                  maxLines: 2,
                                                  overflow: TextOverflow.ellipsis,
                                                  style: const TextStyle(
                                                    fontWeight: FontWeight.w600,
                                                  ),
                                                ),
                                                subtitle: Column(
                                                  crossAxisAlignment:
                                                      CrossAxisAlignment.start,
                                                  children: [
                                                    const SizedBox(height: 4),
                                                    Text(
                                                      'Tipe: ${question['tipe'] == 'pilgan' ? 'Pilihan Ganda' : 'Essay'} | Bobot: ${question['bobot'] ?? 0}',
                                                      style: TextStyle(
                                                        fontSize: 12,
                                                        color: Colors.grey[600],
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                                trailing: Row(
                                                  mainAxisSize: MainAxisSize.min,
                                                  children: [
                                                    IconButton(
                                                      icon: const Icon(Icons.edit),
                                                      onPressed: () async {
                                                        final result =
                                                            await context.push(
                                                                '/dosen/exam/${widget.examId}/question/${question['id']}');
                                                        if (result == true) {
                                                          _loadExam();
                                                        }
                                                      },
                                                      tooltip: 'Edit',
                                                    ),
                                                    IconButton(
                                                      icon: const Icon(
                                                          Icons.delete),
                                                      color: Colors.red,
                                                      onPressed: () =>
                                                          _deleteQuestion(
                                                              question['id']),
                                                      tooltip: 'Hapus',
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            );
                                          },
                                        ),
                                      ),
                                    ],
                                  ),
                          ],
                        ),
                      ),
        floatingActionButton: questions.isNotEmpty
            ? FloatingActionButton(
                onPressed: () async {
                  final result = await context.push(
                      '/dosen/exam/${widget.examId}/question/add');
                  if (result == true) {
                    _loadExam();
                  }
                },
                child: const Icon(Icons.add),
                tooltip: 'Tambah Soal',
              )
            : null,
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
          style: TextStyle(
            fontSize: 12,
            color: Colors.grey[700],
          ),
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

  Widget _buildSettingRow(String label, bool value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        children: [
          Icon(
            value ? Icons.check_circle : Icons.cancel,
            size: 16,
            color: value ? Colors.green : Colors.grey,
          ),
          const SizedBox(width: 8),
          Text(
            label,
            style: TextStyle(
              fontSize: 14,
              color: value ? Colors.green[700] : Colors.grey[700],
            ),
          ),
        ],
      ),
    );
  }
}

