import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';

class KalenderDetailScreen extends StatefulWidget {
  final int eventId;

  const KalenderDetailScreen({Key? key, required this.eventId})
    : super(key: key);

  @override
  State<KalenderDetailScreen> createState() => _KalenderDetailScreenState();
}

class _KalenderDetailScreenState extends State<KalenderDetailScreen> {
  Map<String, dynamic>? event;
  bool isLoading = true;
  String? errorMessage;
  Map<String, dynamic>? userRole;

  @override
  void initState() {
    super.initState();
    _loadUserRole();
    _loadEvent();
  }

  Future<void> _loadUserRole() async {
    final user = await StorageService.getUser();
    setState(() {
      userRole = user;
    });
  }

  Future<void> _loadEvent() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/kalender-akademik/${widget.eventId}',
      );
      if (result['success'] == true) {
        setState(() {
          event = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat event';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _deleteEvent() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Event'),
        content: const Text('Apakah Anda yakin ingin menghapus event ini?'),
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
        '/admin/kalender-akademik/${widget.eventId}',
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Event berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          context.pop();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus event'),
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

  Color _getJenisColor(String? jenis) {
    final colors = {
      'semester': Colors.blue,
      'krs': Colors.green,
      'pembayaran': Colors.orange,
      'ujian': Colors.red,
      'libur': Colors.purple,
      'kegiatan': Colors.pink,
      'pengumuman': Colors.cyan,
      'lainnya': Colors.grey,
    };
    return colors[jenis] ?? Colors.grey;
  }

  String _getJenisLabel(String? jenis) {
    final labels = {
      'semester': 'Semester',
      'krs': 'KRS',
      'pembayaran': 'Pembayaran',
      'ujian': 'Ujian',
      'libur': 'Libur',
      'kegiatan': 'Kegiatan',
      'pengumuman': 'Pengumuman',
      'lainnya': 'Lainnya',
    };
    return labels[jenis] ?? jenis ?? 'Lainnya';
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '-';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMMM yyyy', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  @override
  Widget build(BuildContext context) {
    final isAdmin = userRole?['role'] == 'admin';
    final color = event != null ? _getJenisColor(event!['jenis']) : Colors.grey;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Event'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadEvent,
            tooltip: 'Refresh',
          ),
          if (isAdmin) ...[
            IconButton(
              icon: const Icon(Icons.edit),
              onPressed: () {
                context.push('/admin/kalender-akademik/${widget.eventId}/edit');
              },
              tooltip: 'Edit',
            ),
            IconButton(
              icon: const Icon(Icons.delete),
              onPressed: _deleteEvent,
              tooltip: 'Hapus',
            ),
          ],
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
                    onPressed: _loadEvent,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : event == null
          ? const Center(child: Text('Event tidak ditemukan'))
          : RefreshIndicator(
              onRefresh: _loadEvent,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Card
                    Card(
                      color: color.withOpacity(0.1),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Row(
                          children: [
                            Container(width: 4, height: 60, color: color),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          event!['judul'] ?? '-',
                                          style: const TextStyle(
                                            fontSize: 20,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ),
                                      if (event!['is_important'] == true)
                                        Icon(Icons.star, color: Colors.orange),
                                    ],
                                  ),
                                  const SizedBox(height: 8),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 8,
                                      vertical: 4,
                                    ),
                                    decoration: BoxDecoration(
                                      color: color.withOpacity(0.2),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: Text(
                                      _getJenisLabel(event!['jenis']),
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: color,
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
                              'Informasi Event',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.calendar_today,
                              'Tanggal Mulai',
                              _formatDate(event!['tanggal_mulai']),
                            ),
                            if (event!['tanggal_selesai'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.calendar_today,
                                'Tanggal Selesai',
                                _formatDate(event!['tanggal_selesai']),
                              ),
                            ],
                            if (event!['jam_mulai'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.access_time,
                                'Waktu',
                                '${event!['jam_mulai']}${event!['jam_selesai'] != null ? ' - ${event!['jam_selesai']}' : ''}',
                              ),
                            ],
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.people,
                              'Target',
                              event!['target_role'] == 'semua'
                                  ? 'Semua'
                                  : event!['target_role'] == 'admin'
                                  ? 'Admin'
                                  : event!['target_role'] == 'dosen'
                                  ? 'Dosen'
                                  : 'Mahasiswa',
                            ),
                            if (event!['semester'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.school,
                                'Semester',
                                '${event!['semester']['nama']} ${event!['semester']['tahun_ajaran']}',
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),

                    // Deskripsi
                    if (event!['deskripsi'] != null &&
                        event!['deskripsi'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Deskripsi',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                event!['deskripsi'],
                                style: const TextStyle(fontSize: 14),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],

                    // Link
                    if (event!['link'] != null &&
                        event!['link'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Link',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 8),
                              InkWell(
                                onTap: () {
                                  // TODO: Open URL
                                },
                                child: Text(
                                  event!['link'],
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: Colors.blue,
                                    decoration: TextDecoration.underline,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
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
