import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class JadwalKuliahDetailScreen extends StatefulWidget {
  final int jadwalKuliahId;

  const JadwalKuliahDetailScreen({Key? key, required this.jadwalKuliahId})
      : super(key: key);

  @override
  State<JadwalKuliahDetailScreen> createState() =>
      _JadwalKuliahDetailScreenState();
}

class _JadwalKuliahDetailScreenState extends State<JadwalKuliahDetailScreen> {
  Map<String, dynamic>? jadwalKuliah;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadJadwalKuliah();
  }

  Future<void> _loadJadwalKuliah() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result =
          await ApiService.get('/admin/jadwal-kuliah/${widget.jadwalKuliahId}');
      if (result['success'] == true) {
        setState(() {
          jadwalKuliah = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage =
              result['message'] ?? 'Gagal memuat data jadwal kuliah';
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
        return Colors.red;
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Jadwal Kuliah'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              context.push(
                  '/admin/jadwal-kuliah/${widget.jadwalKuliahId}/edit');
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
                        onPressed: _loadJadwalKuliah,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : jadwalKuliah == null
                  ? const Center(child: Text('Jadwal kuliah tidak ditemukan'))
                  : RefreshIndicator(
                      onRefresh: _loadJadwalKuliah,
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Status Card
                            Card(
                              color: _getStatusColor(jadwalKuliah!['status'])
                                  .withOpacity(0.1),
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.schedule,
                                      color: _getStatusColor(
                                          jadwalKuliah!['status']),
                                      size: 32,
                                    ),
                                    const SizedBox(width: 16),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            jadwalKuliah!['mata_kuliah'] ?? '-',
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
                                                      jadwalKuliah!['status'])
                                                  .withOpacity(0.2),
                                              borderRadius:
                                                  BorderRadius.circular(12),
                                            ),
                                            child: Text(
                                              _getStatusLabel(
                                                  jadwalKuliah!['status']),
                                              style: TextStyle(
                                                fontSize: 12,
                                                fontWeight: FontWeight.bold,
                                                color: _getStatusColor(
                                                    jadwalKuliah!['status']),
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
                                      'Informasi Jadwal Kuliah',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    _buildInfoRow(
                                      Icons.menu_book,
                                      'Mata Kuliah',
                                      jadwalKuliah!['mata_kuliah'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.person_outline,
                                      'Dosen',
                                      jadwalKuliah!['dosen'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.calendar_today,
                                      'Semester',
                                      jadwalKuliah!['semester'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.calendar_view_week,
                                      'Hari',
                                      jadwalKuliah!['hari'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.access_time,
                                      'Waktu',
                                      '${jadwalKuliah!['jam_mulai'] ?? '-'} - ${jadwalKuliah!['jam_selesai'] ?? '-'}',
                                    ),
                                    if (jadwalKuliah!['ruangan'] != null &&
                                        jadwalKuliah!['ruangan']
                                            .toString()
                                            .isNotEmpty) ...[
                                      const SizedBox(height: 8),
                                      _buildInfoRow(
                                        Icons.room,
                                        'Ruangan',
                                        jadwalKuliah!['ruangan'] ?? '-',
                                      ),
                                    ],
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.people,
                                      'Kuota',
                                      '${jadwalKuliah!['terisi'] ?? 0}/${jadwalKuliah!['kuota'] ?? 0}',
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
