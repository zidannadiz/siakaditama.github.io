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
  List<dynamic> jadwals = [];
  List<dynamic> exams = [];
  int? selectedJadwalId;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData({int? jadwalId}) async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final query = jadwalId != null ? '?jadwal_id=$jadwalId' : '';
      final result = await ApiService.get('/dosen/exam$query');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          jadwals = data['jadwals'] ?? [];
          exams = data['exams'] ?? [];
          selectedJadwalId = data['selected_jadwal_id'];
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

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  Color _getStatusColor(bool isOngoing, bool isFinished, String status) {
    if (isFinished) {
      return Colors.grey;
    } else if (isOngoing) {
      return Colors.orange;
    } else if (status == 'published') {
      return Colors.green;
    } else {
      return Colors.blue;
    }
  }

  String _getStatusLabel(bool isOngoing, bool isFinished, String status) {
    if (isFinished) {
      return 'Selesai';
    } else if (isOngoing) {
      return 'Berlangsung';
    } else if (status == 'published') {
      return 'Published';
    } else {
      return 'Draft';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Ujian'),
        actions: [
          if (selectedJadwalId != null)
            IconButton(
              icon: const Icon(Icons.add),
              onPressed: () {
                context.push('/dosen/exam/create?jadwal_id=$selectedJadwalId');
              },
              tooltip: 'Tambah Ujian',
            ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(jadwalId: selectedJadwalId),
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
                        onPressed: () => _loadData(jadwalId: selectedJadwalId),
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : Column(
                  children: [
                    // Jadwal Selection
                    Container(
                      padding: const EdgeInsets.all(16),
                      color: Colors.blue[50],
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Pilih Jadwal Kuliah',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 8),
                          if (jadwals.isEmpty)
                            const Text('Tidak ada jadwal kuliah')
                          else
                            Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: jadwals.map((jadwal) {
                                final isSelected =
                                    selectedJadwalId == jadwal['id'];
                                return FilterChip(
                                  label: Text(
                                      '${jadwal['kode_mk'] ?? '-'} - ${jadwal['mata_kuliah'] ?? '-'}'),
                                  selected: isSelected,
                                  onSelected: (selected) {
                                    if (selected) {
                                      _loadData(jadwalId: jadwal['id']);
                                    }
                                  },
                                );
                              }).toList(),
                            ),
                        ],
                      ),
                    ),

                    // Exams List
                    Expanded(
                      child: selectedJadwalId == null
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.quiz_outlined,
                                      size: 64, color: Colors.grey[400]),
                                  const SizedBox(height: 16),
                                  Text(
                                    'Pilih jadwal kuliah untuk melihat ujian',
                                    style: TextStyle(
                                      fontSize: 16,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ],
                              ),
                            )
                          : RefreshIndicator(
                              onRefresh: () =>
                                  _loadData(jadwalId: selectedJadwalId),
                              child: exams.isEmpty
                                  ? Center(
                                      child: Column(
                                        mainAxisAlignment:
                                            MainAxisAlignment.center,
                                        children: [
                                          Icon(Icons.quiz_outlined,
                                              size: 64,
                                              color: Colors.grey[400]),
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
                                        final status = exam['status'] ?? 'draft';
                                        final statusColor = _getStatusColor(
                                            isOngoing, isFinished, status);

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
                                                borderRadius:
                                                    BorderRadius.circular(25),
                                              ),
                                              child: Icon(
                                                Icons.quiz,
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
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
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
                                              ],
                                            ),
                                            trailing: Row(
                                              mainAxisSize: MainAxisSize.min,
                                              children: [
                                                Container(
                                                  padding:
                                                      const EdgeInsets.symmetric(
                                                    horizontal: 8,
                                                    vertical: 4,
                                                  ),
                                                  decoration: BoxDecoration(
                                                    color: statusColor.withOpacity(0.1),
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            12),
                                                  ),
                                                  child: Text(
                                                    _getStatusLabel(
                                                        isOngoing,
                                                        isFinished,
                                                        status),
                                                    style: TextStyle(
                                                      fontSize: 10,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                      color: statusColor,
                                                    ),
                                                  ),
                                                ),
                                                const Icon(
                                                  Icons.arrow_forward_ios,
                                                  size: 16,
                                                ),
                                              ],
                                            ),
                                            onTap: () {
                                              context.push(
                                                  '/dosen/exam/${exam['id']}');
                                            },
                                          ),
                                        );
                                      },
                                    ),
                            ),
                    ),
                  ],
                ),
      floatingActionButton: selectedJadwalId != null
          ? FloatingActionButton(
              onPressed: () {
                context.push(
                    '/dosen/exam/create?jadwal_id=$selectedJadwalId');
              },
              child: const Icon(Icons.add),
              tooltip: 'Tambah Ujian',
            )
          : null,
    );
  }
}

