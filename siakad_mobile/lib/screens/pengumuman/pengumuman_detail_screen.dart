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

    final result = await ApiService.get('/pengumuman/${widget.pengumumanId}');
    if (result['success'] == true) {
      setState(() {
        pengumuman = result['data'];
        isLoading = false;
      });
    } else {
      setState(() {
        isLoading = false;
        errorMessage = result['message'] ?? 'Gagal memuat pengumuman';
      });
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

  Color _getKategoriColor(String? kategori) {
    switch (kategori) {
      case 'umum':
        return Colors.blue;
      case 'akademik':
        return Colors.green;
      case 'beasiswa':
        return Colors.orange;
      case 'kegiatan':
        return Colors.purple;
      default:
        return Colors.grey;
    }
  }

  String _getKategoriLabel(String? kategori) {
    switch (kategori) {
      case 'umum':
        return 'Umum';
      case 'akademik':
        return 'Akademik';
      case 'beasiswa':
        return 'Beasiswa';
      case 'kegiatan':
        return 'Kegiatan';
      default:
        return kategori ?? 'Umum';
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
      case 'admin':
        return 'Admin';
      default:
        return target ?? 'Semua';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Pengumuman'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadPengumuman,
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
                    onPressed: _loadPengumuman,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : pengumuman == null
          ? const Center(child: Text('Pengumuman tidak ditemukan'))
          : SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Header Card
                  Card(
                    color: pengumuman!['is_pinned'] == true
                        ? Colors.amber[50]
                        : Colors.blue[50],
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              if (pengumuman!['is_pinned'] == true)
                                Icon(
                                  Icons.push_pin,
                                  color: Colors.amber[700],
                                  size: 20,
                                ),
                              if (pengumuman!['is_pinned'] == true)
                                const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  pengumuman!['judul'] ?? '-',
                                  style: const TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                decoration: BoxDecoration(
                                  color: _getKategoriColor(
                                    pengumuman!['kategori'],
                                  ).withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: Text(
                                  _getKategoriLabel(pengumuman!['kategori']),
                                  style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: _getKategoriColor(
                                      pengumuman!['kategori'],
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.grey[200],
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: Text(
                                  'Target: ${_getTargetLabel(pengumuman!['target'])}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[700],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Content
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            pengumuman!['isi'] ?? '-',
                            style: const TextStyle(fontSize: 14, height: 1.6),
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
                        children: [
                          if (pengumuman!['user'] != null)
                            Row(
                              children: [
                                const Icon(
                                  Icons.person,
                                  size: 16,
                                  color: Colors.grey,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Dibuat oleh: ${pengumuman!['user']['name'] ?? '-'}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                              ],
                            ),
                          if (pengumuman!['user'] != null)
                            const SizedBox(height: 8),
                          Row(
                            children: [
                              const Icon(
                                Icons.calendar_today,
                                size: 16,
                                color: Colors.grey,
                              ),
                              const SizedBox(width: 8),
                              Text(
                                'Dipublikasikan: ${_formatDate(pengumuman!['published_at'] ?? pengumuman!['created_at'])}',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }
}
