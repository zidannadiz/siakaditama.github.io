import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class DosenDetailScreen extends StatefulWidget {
  final int dosenId;

  const DosenDetailScreen({Key? key, required this.dosenId}) : super(key: key);

  @override
  State<DosenDetailScreen> createState() => _DosenDetailScreenState();
}

class _DosenDetailScreenState extends State<DosenDetailScreen> {
  Map<String, dynamic>? dosen;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadDosen();
  }

  Future<void> _loadDosen() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/dosen/${widget.dosenId}');
      if (result['success'] == true) {
        setState(() {
          dosen = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data dosen';
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
        title: const Text('Detail Dosen'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              context.push('/admin/dosen/${widget.dosenId}/edit');
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
                        onPressed: _loadDosen,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : dosen == null
                  ? const Center(child: Text('Dosen tidak ditemukan'))
                  : RefreshIndicator(
                      onRefresh: _loadDosen,
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Status Card
                            Card(
                              color: _getStatusColor(dosen!['status'])
                                  .withOpacity(0.1),
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.person,
                                      color: _getStatusColor(dosen!['status']),
                                      size: 32,
                                    ),
                                    const SizedBox(width: 16),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            dosen!['nama'] ?? '-',
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
                                                      dosen!['status'])
                                                  .withOpacity(0.2),
                                              borderRadius:
                                                  BorderRadius.circular(12),
                                            ),
                                            child: Text(
                                              _getStatusLabel(dosen!['status']),
                                              style: TextStyle(
                                                fontSize: 12,
                                                fontWeight: FontWeight.bold,
                                                color: _getStatusColor(
                                                    dosen!['status']),
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
                                      'Informasi Dosen',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    _buildInfoRow(
                                      Icons.badge,
                                      'NIDN',
                                      dosen!['nidn'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.email,
                                      'Email',
                                      dosen!['email'] ?? '-',
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
