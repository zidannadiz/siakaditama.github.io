import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class PengumumanDetailScreen extends StatefulWidget {
  final int pengumumanId;

  const PengumumanDetailScreen({Key? key, required this.pengumumanId})
    : super(key: key);

  @override
  State<PengumumanDetailScreen> createState() => _PengumumanDetailScreenState();
}

class _PengumumanDetailScreenState extends State<PengumumanDetailScreen> {
  Map<String, dynamic>? pengumuman;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadPengumuman();
  }

  Future<void> _loadPengumuman() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/admin/pengumuman/${widget.pengumumanId}',
      );
      if (result['success'] == true) {
        setState(() {
          pengumuman = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data pengumuman';
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
    if (dateString == null) return 'Belum dipublikasikan';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  String _getTargetLabel(String? target) {
    switch (target) {
      case 'semua':
        return 'Semua';
      case 'mahasiswa':
        return 'Mahasiswa';
      case 'dosen':
        return 'Dosen';
      default:
        return 'Unknown';
    }
  }

  Color _getTargetColor(String? target) {
    switch (target) {
      case 'semua':
        return Colors.blue;
      case 'mahasiswa':
        return Colors.green;
      case 'dosen':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Pengumuman'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              context.push('/admin/pengumuman/${widget.pengumumanId}/edit');
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
                  Icon(Icons.error_outline, size: 64, color: Colors.red[300]),
                  const SizedBox(height: 16),
                  Text(
                    errorMessage!,
                    style: TextStyle(color: Colors.red[700]),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: _loadPengumuman,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : pengumuman == null
          ? const Center(child: Text('Pengumuman tidak ditemukan'))
          : RefreshIndicator(
              onRefresh: _loadPengumuman,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Card
                    Card(
                      color: pengumuman!['is_pinned'] == true
                          ? Colors.orange[50]
                          : Colors.orange[100],
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Row(
                          children: [
                            Icon(
                              pengumuman!['is_pinned'] == true
                                  ? Icons.push_pin
                                  : Icons.announcement,
                              color: Colors.orange[700],
                              size: 32,
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    pengumuman!['judul'] ?? '-',
                                    style: const TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 8,
                                          vertical: 4,
                                        ),
                                        decoration: BoxDecoration(
                                          color: _getTargetColor(
                                            pengumuman!['target'],
                                          ).withOpacity(0.2),
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Text(
                                          _getTargetLabel(
                                            pengumuman!['target'],
                                          ),
                                          style: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: _getTargetColor(
                                              pengumuman!['target'],
                                            ),
                                          ),
                                        ),
                                      ),
                                      if (pengumuman!['is_pinned'] == true) ...[
                                        const SizedBox(width: 8),
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                            horizontal: 8,
                                            vertical: 4,
                                          ),
                                          decoration: BoxDecoration(
                                            color: Colors.orange[200],
                                            borderRadius: BorderRadius.circular(
                                              12,
                                            ),
                                          ),
                                          child: const Text(
                                            'PINNED',
                                            style: TextStyle(
                                              fontSize: 12,
                                              fontWeight: FontWeight.bold,
                                              color: Colors.orange,
                                            ),
                                          ),
                                        ),
                                      ],
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Content Card
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Isi Pengumuman',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            Text(
                              pengumuman!['isi'] ?? '-',
                              style: const TextStyle(fontSize: 14),
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
                              'Informasi',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.people,
                              'Target',
                              _getTargetLabel(pengumuman!['target']),
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.calendar_today,
                              'Tanggal Publikasi',
                              _formatDate(pengumuman!['published_at']),
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.push_pin,
                              'Status Pin',
                              pengumuman!['is_pinned'] == true
                                  ? 'Dipin'
                                  : 'Tidak dipin',
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
          style: TextStyle(fontSize: 14, color: Colors.grey[700]),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
          ),
        ),
      ],
    );
  }
}
