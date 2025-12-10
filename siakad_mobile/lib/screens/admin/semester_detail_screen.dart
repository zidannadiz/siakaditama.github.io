import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class SemesterDetailScreen extends StatefulWidget {
  final int semesterId;

  const SemesterDetailScreen({Key? key, required this.semesterId})
      : super(key: key);

  @override
  State<SemesterDetailScreen> createState() => _SemesterDetailScreenState();
}

class _SemesterDetailScreenState extends State<SemesterDetailScreen> {
  Map<String, dynamic>? semester;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadSemester();
  }

  Future<void> _loadSemester() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result =
          await ApiService.get('/admin/semester/${widget.semesterId}');
      if (result['success'] == true) {
        setState(() {
          semester = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data semester';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Color _getStatusColor(String? status) {
    switch (status) {
      case 'aktif':
        return Colors.green;
      case 'nonaktif':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _getStatusLabel(String? status) {
    switch (status) {
      case 'aktif':
        return 'Aktif';
      case 'nonaktif':
        return 'Nonaktif';
      default:
        return 'Unknown';
    }
  }

  String _getJenisLabel(String? jenis) {
    switch (jenis) {
      case 'ganjil':
        return 'Ganjil';
      case 'genap':
        return 'Genap';
      default:
        return 'Unknown';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Semester'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              context.push('/admin/semester/${widget.semesterId}/edit');
            },
            tooltip: 'Edit',
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
                        onPressed: _loadSemester,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : semester == null
                  ? const Center(child: Text('Semester tidak ditemukan'))
                  : RefreshIndicator(
                      onRefresh: _loadSemester,
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Status Card
                            Card(
                              color: _getStatusColor(semester!['status'])
                                  .withOpacity(0.1),
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.calendar_today,
                                      color: _getStatusColor(semester!['status']),
                                      size: 32,
                                    ),
                                    const SizedBox(width: 16),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            semester!['nama'] ?? '-',
                                            style: const TextStyle(
                                              fontSize: 20,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          Container(
                                            padding: const EdgeInsets.symmetric(
                                              horizontal: 8,
                                              vertical: 4,
                                            ),
                                            decoration: BoxDecoration(
                                              color: _getStatusColor(
                                                      semester!['status'])
                                                  .withOpacity(0.2),
                                              borderRadius:
                                                  BorderRadius.circular(12),
                                            ),
                                            child: Text(
                                              _getStatusLabel(semester!['status']),
                                              style: TextStyle(
                                                fontSize: 12,
                                                fontWeight: FontWeight.bold,
                                                color: _getStatusColor(
                                                    semester!['status']),
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),

                            // Info Card
                            Card(
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    const Text(
                                      'Informasi Semester',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    _buildInfoRow(
                                      Icons.calendar_today,
                                      'Nama Semester',
                                      semester!['nama'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.date_range,
                                      'Tahun Ajaran',
                                      semester!['tahun_ajaran'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.category,
                                      'Jenis',
                                      _getJenisLabel(semester!['jenis']),
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.info,
                                      'Status',
                                      _getStatusLabel(semester!['status']),
                                    ),
                                  ],
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
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey[700],
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}

