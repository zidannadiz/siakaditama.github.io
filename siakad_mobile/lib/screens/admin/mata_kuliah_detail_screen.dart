import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class MataKuliahDetailScreen extends StatefulWidget {
  final int mataKuliahId;

  const MataKuliahDetailScreen({Key? key, required this.mataKuliahId})
      : super(key: key);

  @override
  State<MataKuliahDetailScreen> createState() =>
      _MataKuliahDetailScreenState();
}

class _MataKuliahDetailScreenState extends State<MataKuliahDetailScreen> {
  Map<String, dynamic>? mataKuliah;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadMataKuliah();
  }

  Future<void> _loadMataKuliah() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result =
          await ApiService.get('/admin/mata-kuliah/${widget.mataKuliahId}');
      if (result['success'] == true) {
        setState(() {
          mataKuliah = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data mata kuliah';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  String _getJenisLabel(String? jenis) {
    switch (jenis) {
      case 'wajib':
        return 'Wajib';
      case 'pilihan':
        return 'Pilihan';
      default:
        return 'Unknown';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Mata Kuliah'),
        actions: [
          IconButton(
            icon: const Icon(Icons.edit),
            onPressed: () {
              context.push('/admin/mata-kuliah/${widget.mataKuliahId}/edit');
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
                        onPressed: _loadMataKuliah,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : mataKuliah == null
                  ? const Center(child: Text('Mata kuliah tidak ditemukan'))
                  : RefreshIndicator(
                      onRefresh: _loadMataKuliah,
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Header Card
                            Card(
                              color: Colors.deepPurple[50],
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.menu_book,
                                      color: Colors.deepPurple[700],
                                      size: 32,
                                    ),
                                    const SizedBox(width: 16),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            mataKuliah!['nama'] ?? '-',
                                            style: const TextStyle(
                                              fontSize: 20,
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            'Kode: ${mataKuliah!['kode_mk'] ?? '-'}',
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
                                      'Informasi Mata Kuliah',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    _buildInfoRow(
                                      Icons.code,
                                      'Kode Mata Kuliah',
                                      mataKuliah!['kode_mk'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.menu_book,
                                      'Nama Mata Kuliah',
                                      mataKuliah!['nama'] ?? '-',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.credit_card,
                                      'SKS',
                                      '${mataKuliah!['sks'] ?? 0} SKS',
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.school,
                                      'Program Studi',
                                      mataKuliah!['prodi'] ?? '-',
                                    ),
                                    if (mataKuliah!['semester'] != null) ...[
                                      const SizedBox(height: 8),
                                      _buildInfoRow(
                                        Icons.numbers,
                                        'Semester',
                                        '${mataKuliah!['semester']}',
                                      ),
                                    ],
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.category,
                                      'Jenis',
                                      _getJenisLabel(mataKuliah!['jenis']),
                                    ),
                                    if (mataKuliah!['deskripsi'] != null &&
                                        mataKuliah!['deskripsi'].toString().isNotEmpty) ...[
                                      const SizedBox(height: 16),
                                      const Text(
                                        'Deskripsi',
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        mataKuliah!['deskripsi'],
                                        style: const TextStyle(fontSize: 14),
                                      ),
                                    ],
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

