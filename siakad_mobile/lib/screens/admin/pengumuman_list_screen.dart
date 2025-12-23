import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class PengumumanListScreen extends StatefulWidget {
  const PengumumanListScreen({Key? key}) : super(key: key);

  @override
  State<PengumumanListScreen> createState() => _PengumumanListScreenState();
}

class _PengumumanListScreenState extends State<PengumumanListScreen> {
  List<dynamic> pengumumans = [];
  bool isLoading = true;
  String? errorMessage;
  int currentPage = 1;
  int lastPage = 1;
  int total = 0;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadPengumumans();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadPengumumans({int page = 1}) async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/pengumuman?page=$page');
      if (result['success'] == true) {
        setState(() {
          pengumumans = result['data']['pengumumans'] ?? [];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
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

  Future<void> _deletePengumuman(int id, String judul) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Pengumuman'),
        content: Text('Apakah Anda yakin ingin menghapus pengumuman "$judul"?'),
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
      final result = await ApiService.delete('/admin/pengumuman/$id');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Pengumuman berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadPengumumans(page: currentPage);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus pengumuman'),
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

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(date);
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
        title: const Text('Manajemen Pengumuman'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadPengumumans(page: currentPage),
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
                    onPressed: () => _loadPengumumans(page: currentPage),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Search Bar
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari pengumuman...',
                      prefixIcon: const Icon(Icons.search),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _loadPengumumans();
                              },
                            )
                          : null,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    onSubmitted: (value) {
                      // TODO: Implement search
                      _loadPengumumans();
                    },
                  ),
                ),

                // Summary
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.orange[50],
                  child: Row(
                    children: [
                      Icon(Icons.announcement, color: Colors.orange[700]),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Total Pengumuman',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[700],
                              ),
                            ),
                            Text(
                              '$total Pengumuman',
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: Colors.orange[900],
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),

                // List
                Expanded(
                  child: pengumumans.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.announcement_outlined,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada data pengumuman',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () => _loadPengumumans(page: currentPage),
                          child: ListView.builder(
                            padding: const EdgeInsets.all(8),
                            itemCount: pengumumans.length,
                            itemBuilder: (context, index) {
                              final pengumuman = pengumumans[index];
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
                                      color: pengumuman['is_pinned'] == true
                                          ? Colors.orange[100]
                                          : Colors.orange[50],
                                      borderRadius: BorderRadius.circular(25),
                                    ),
                                    child: Icon(
                                      pengumuman['is_pinned'] == true
                                          ? Icons.push_pin
                                          : Icons.announcement,
                                      color: Colors.orange[700],
                                      size: 24,
                                    ),
                                  ),
                                  title: Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          pengumuman['judul'] ?? '-',
                                          style: TextStyle(
                                            fontWeight: FontWeight.w600,
                                            color:
                                                pengumuman['is_pinned'] == true
                                                ? Colors.orange[900]
                                                : null,
                                          ),
                                        ),
                                      ),
                                      if (pengumuman['is_pinned'] == true)
                                        Container(
                                          padding: const EdgeInsets.symmetric(
                                            horizontal: 6,
                                            vertical: 2,
                                          ),
                                          decoration: BoxDecoration(
                                            color: Colors.orange[200],
                                            borderRadius: BorderRadius.circular(
                                              8,
                                            ),
                                          ),
                                          child: const Text(
                                            'PINNED',
                                            style: TextStyle(
                                              fontSize: 8,
                                              fontWeight: FontWeight.bold,
                                              color: Colors.orange,
                                            ),
                                          ),
                                        ),
                                    ],
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 4),
                                      Row(
                                        children: [
                                          Container(
                                            padding: const EdgeInsets.symmetric(
                                              horizontal: 6,
                                              vertical: 2,
                                            ),
                                            decoration: BoxDecoration(
                                              color: _getTargetColor(
                                                pengumuman['target'],
                                              ).withOpacity(0.2),
                                              borderRadius:
                                                  BorderRadius.circular(8),
                                            ),
                                            child: Text(
                                              _getTargetLabel(
                                                pengumuman['target'],
                                              ),
                                              style: TextStyle(
                                                fontSize: 10,
                                                fontWeight: FontWeight.bold,
                                                color: _getTargetColor(
                                                  pengumuman['target'],
                                                ),
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        'Dipublikasikan: ${_formatDate(pengumuman['published_at'])}',
                                        style: TextStyle(
                                          fontSize: 11,
                                          color: Colors.grey[500],
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: PopupMenuButton(
                                    itemBuilder: (context) => [
                                      const PopupMenuItem(
                                        value: 'edit',
                                        child: Row(
                                          children: [
                                            Icon(Icons.edit, size: 20),
                                            SizedBox(width: 8),
                                            Text('Edit'),
                                          ],
                                        ),
                                      ),
                                      const PopupMenuItem(
                                        value: 'detail',
                                        child: Row(
                                          children: [
                                            Icon(Icons.info_outline, size: 20),
                                            SizedBox(width: 8),
                                            Text('Detail'),
                                          ],
                                        ),
                                      ),
                                      const PopupMenuItem(
                                        value: 'delete',
                                        child: Row(
                                          children: [
                                            Icon(
                                              Icons.delete,
                                              size: 20,
                                              color: Colors.red,
                                            ),
                                            SizedBox(width: 8),
                                            Text(
                                              'Hapus',
                                              style: TextStyle(
                                                color: Colors.red,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                    onSelected: (value) {
                                      if (value == 'edit') {
                                        context.push(
                                          '/admin/pengumuman/${pengumuman['id']}/edit',
                                        );
                                      } else if (value == 'detail') {
                                        context.push(
                                          '/admin/pengumuman/${pengumuman['id']}',
                                        );
                                      } else if (value == 'delete') {
                                        _deletePengumuman(
                                          pengumuman['id'],
                                          pengumuman['judul'],
                                        );
                                      }
                                    },
                                  ),
                                ),
                              );
                            },
                          ),
                        ),
                ),

                // Pagination
                if (lastPage > 1)
                  Container(
                    padding: const EdgeInsets.all(8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.chevron_left),
                          onPressed: currentPage > 1
                              ? () => _loadPengumumans(page: currentPage - 1)
                              : null,
                        ),
                        Text(
                          'Halaman $currentPage dari $lastPage',
                          style: const TextStyle(fontSize: 14),
                        ),
                        IconButton(
                          icon: const Icon(Icons.chevron_right),
                          onPressed: currentPage < lastPage
                              ? () => _loadPengumumans(page: currentPage + 1)
                              : null,
                        ),
                      ],
                    ),
                  ),
              ],
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/admin/pengumuman/create'),
        child: const Icon(Icons.add),
      ),
    );
  }
}
