import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class ProdiDetailScreen extends StatefulWidget {
  final int prodiId;

  const ProdiDetailScreen({Key? key, required this.prodiId}) : super(key: key);

  @override
  State<ProdiDetailScreen> createState() => _ProdiDetailScreenState();
}

class _ProdiDetailScreenState extends State<ProdiDetailScreen> {
  Map<String, dynamic>? prodi;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadProdi();
  }

  Future<void> _loadProdi() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/prodi/${widget.prodiId}');
      if (result['success'] == true) {
        setState(() {
          prodi = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data prodi';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Program Studi'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              context.push('/admin/prodi/${widget.prodiId}/edit');
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
                    onPressed: _loadProdi,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : prodi == null
          ? const Center(child: Text('Prodi tidak ditemukan'))
          : RefreshIndicator(
              onRefresh: _loadProdi,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Header Card
                    Card(
                      color: Colors.indigo[50],
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Row(
                          children: [
                            Icon(
                              Icons.school,
                              color: Colors.indigo[700],
                              size: 32,
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    prodi!['nama'] ?? '-',
                                    style: const TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    'Kode: ${prodi!['kode'] ?? '-'}',
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: Colors.grey[700],
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
                              'Informasi Program Studi',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.code,
                              'Kode Prodi',
                              prodi!['kode'] ?? '-',
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.school,
                              'Nama Prodi',
                              prodi!['nama'] ?? '-',
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
