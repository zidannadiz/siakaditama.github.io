import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';
import 'krs_add_screen.dart';

class KRSListScreen extends StatefulWidget {
  const KRSListScreen({Key? key}) : super(key: key);

  @override
  State<KRSListScreen> createState() => _KRSListScreenState();
}

class _KRSListScreenState extends State<KRSListScreen> {
  List<dynamic> krsList = [];
  Map<String, dynamic>? semesterAktif;
  int totalSks = 0;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadKRS();
  }

  Future<void> _loadKRS() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    final result = await ApiService.get('/mahasiswa/krs');
    if (result['success'] == true) {
      setState(() {
        krsList = result['data']['krs_list'] ?? [];
        semesterAktif = result['data']['semester_aktif'];
        totalSks = result['data']['total_sks'] ?? 0;
        isLoading = false;
      });
    } else {
      setState(() {
        isLoading = false;
        errorMessage = result['message'] ?? 'Gagal memuat data KRS';
      });
    }
  }

  Future<void> _deleteKRS(int krsId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus KRS'),
        content: const Text('Apakah Anda yakin ingin menghapus KRS ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      final result = await ApiService.delete('/mahasiswa/krs/$krsId');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('KRS berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadKRS();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus KRS'),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'disetujui':
        return Colors.green;
      case 'ditolak':
        return Colors.red;
      case 'pending':
      default:
        return Colors.orange;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'disetujui':
        return 'Disetujui';
      case 'ditolak':
        return 'Ditolak';
      case 'pending':
      default:
        return 'Menunggu Persetujuan';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Kartu Rencana Studi (KRS)'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadKRS,
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
                        onPressed: _loadKRS,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadKRS,
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Semester Info Card
                        if (semesterAktif != null)
                          Card(
                            color: Colors.blue[50],
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Row(
                                children: [
                                  const Icon(Icons.calendar_today,
                                      color: Colors.blue),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        const Text(
                                          'Semester Aktif',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey,
                                          ),
                                        ),
                                        Text(
                                          semesterAktif!['nama'] ?? '-',
                                          style: const TextStyle(
                                            fontSize: 16,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 12,
                                      vertical: 8,
                                    ),
                                    decoration: BoxDecoration(
                                      color: Colors.orange,
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Text(
                                      '$totalSks SKS',
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        const SizedBox(height: 24),

                        // KRS List
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Daftar KRS (${krsList.length})',
                              style: Theme.of(context)
                                  .textTheme
                                  .titleLarge
                                  ?.copyWith(fontWeight: FontWeight.bold),
                            ),
                            ElevatedButton.icon(
                              onPressed: () async {
                                final result = await context.push(
                                  '/mahasiswa/krs/add',
                                );
                                if (result == true) {
                                  _loadKRS();
                                }
                              },
                              icon: const Icon(Icons.add),
                              label: const Text('Tambah KRS'),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),

                        if (krsList.isEmpty)
                          Card(
                            child: Padding(
                              padding: const EdgeInsets.all(32),
                              child: Column(
                                children: [
                                  Icon(Icons.book_outlined,
                                      size: 64, color: Colors.grey[400]),
                                  const SizedBox(height: 16),
                                  Text(
                                    'Belum ada KRS',
                                    style: TextStyle(
                                      fontSize: 16,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Text(
                                    'Tambahkan mata kuliah untuk semester ini',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey[500],
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                ],
                              ),
                            ),
                          )
                        else
                          ...krsList.map<Widget>((krs) => Card(
                                margin: const EdgeInsets.only(bottom: 12),
                                child: ListTile(
                                  contentPadding: const EdgeInsets.all(16),
                                  leading: Container(
                                    width: 50,
                                    height: 50,
                                    decoration: BoxDecoration(
                                      color: _getStatusColor(
                                              krs['status'] ?? 'pending')
                                          .withOpacity(0.1),
                                      borderRadius: BorderRadius.circular(8),
                                    ),
                                    child: Icon(
                                      Icons.book,
                                      color: _getStatusColor(
                                          krs['status'] ?? 'pending'),
                                    ),
                                  ),
                                  title: Text(
                                    krs['mata_kuliah'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 8),
                                      if (krs['dosen'] != null)
                                        Row(
                                          children: [
                                            const Icon(Icons.person,
                                                size: 16),
                                            const SizedBox(width: 4),
                                            Text('${krs['dosen'] ?? '-'}'),
                                          ],
                                        ),
                                      const SizedBox(height: 4),
                                      if (krs['hari'] != null &&
                                          krs['jam_mulai'] != null)
                                        Row(
                                          children: [
                                            const Icon(Icons.schedule,
                                                size: 16),
                                            const SizedBox(width: 4),
                                            Text(
                                                '${krs['hari'] ?? '-'}, ${krs['jam_mulai'] ?? '-'} - ${krs['jam_selesai'] ?? '-'}'),
                                          ],
                                        ),
                                      const SizedBox(height: 4),
                                      if (krs['ruangan'] != null)
                                        Row(
                                          children: [
                                            const Icon(Icons.location_on,
                                                size: 16),
                                            const SizedBox(width: 4),
                                            Text('${krs['ruangan'] ?? '-'}'),
                                          ],
                                        ),
                                      const SizedBox(height: 8),
                                      Row(
                                        children: [
                                          Container(
                                            padding:
                                                const EdgeInsets.symmetric(
                                                  horizontal: 8,
                                                  vertical: 4,
                                                ),
                                            decoration: BoxDecoration(
                                              color: _getStatusColor(
                                                      krs['status'] ??
                                                          'pending')
                                                  .withOpacity(0.1),
                                              borderRadius:
                                                  BorderRadius.circular(12),
                                              border: Border.all(
                                                color: _getStatusColor(
                                                    krs['status'] ?? 'pending'),
                                                width: 1,
                                              ),
                                            ),
                                            child: Text(
                                              _getStatusText(
                                                  krs['status'] ?? 'pending'),
                                              style: TextStyle(
                                                fontSize: 10,
                                                fontWeight: FontWeight.bold,
                                                color: _getStatusColor(
                                                    krs['status'] ?? 'pending'),
                                              ),
                                            ),
                                          ),
                                          const SizedBox(width: 8),
                                          if (krs['sks'] != null)
                                            Text(
                                              '${krs['sks'] ?? 0} SKS',
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Colors.grey[600],
                                              ),
                                            ),
                                        ],
                                      ),
                                    ],
                                  ),
                                  trailing: krs['status'] == 'pending'
                                      ? IconButton(
                                          icon: const Icon(Icons.delete,
                                              color: Colors.red),
                                          onPressed: () =>
                                              _deleteKRS(krs['id']),
                                          tooltip: 'Hapus',
                                        )
                                      : null,
                                ),
                              )),
                      ],
                    ),
                  ),
                ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () async {
          final result = await context.push('/mahasiswa/krs/add');
          if (result == true) {
            _loadKRS();
          }
        },
        icon: const Icon(Icons.add),
        label: const Text('Tambah KRS'),
      ),
    );
  }
}
