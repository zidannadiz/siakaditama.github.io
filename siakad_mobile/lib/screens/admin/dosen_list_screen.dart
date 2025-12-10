import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class DosenListScreen extends StatefulWidget {
  const DosenListScreen({Key? key}) : super(key: key);

  @override
  State<DosenListScreen> createState() => _DosenListScreenState();
}

class _DosenListScreenState extends State<DosenListScreen> {
  List<dynamic> dosens = [];
  bool isLoading = true;
  String? errorMessage;
  int currentPage = 1;
  int lastPage = 1;
  int total = 0;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadDosens();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadDosens({int page = 1}) async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/dosen?page=$page');
      if (result['success'] == true) {
        setState(() {
          dosens = result['data']['dosens'] ?? [];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
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

  Future<void> _deleteDosen(int id, String nama) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Dosen'),
        content: Text('Apakah Anda yakin ingin menghapus dosen $nama?'),
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
      final result = await ApiService.delete('/admin/dosen/$id');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Dosen berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadDosens(page: currentPage);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus dosen'),
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
        title: const Text('Data Dosen'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadDosens(page: currentPage),
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
                    onPressed: () => _loadDosens(page: currentPage),
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
                      hintText: 'Cari dosen...',
                      prefixIcon: const Icon(Icons.search),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _loadDosens();
                              },
                            )
                          : null,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    onSubmitted: (value) {
                      // TODO: Implement search
                      _loadDosens();
                    },
                  ),
                ),

                // Summary
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.teal[50],
                  child: Row(
                    children: [
                      Icon(Icons.person_outline, color: Colors.teal[700]),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Total Dosen',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[700],
                              ),
                            ),
                            Text(
                              '$total Dosen',
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: Colors.teal[900],
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
                  child: dosens.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.person_off_outlined,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada data dosen',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () => _loadDosens(page: currentPage),
                          child: ListView.builder(
                            padding: const EdgeInsets.all(8),
                            itemCount: dosens.length,
                            itemBuilder: (context, index) {
                              final dosen = dosens[index];
                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                child: ListTile(
                                  leading: CircleAvatar(
                                    backgroundColor: Colors.teal[100],
                                    child: Text(
                                      (dosen['nama'] ?? 'D')[0].toUpperCase(),
                                      style: TextStyle(
                                        color: Colors.teal[900],
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                  title: Text(
                                    dosen['nama'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 4),
                                      Text(
                                        'NIDN: ${dosen['nidn'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      Text(
                                        'Email: ${dosen['email'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 8,
                                          vertical: 4,
                                        ),
                                        decoration: BoxDecoration(
                                          color: _getStatusColor(
                                            dosen['status'],
                                          ).withOpacity(0.2),
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Text(
                                          _getStatusLabel(dosen['status']),
                                          style: TextStyle(
                                            fontSize: 10,
                                            fontWeight: FontWeight.bold,
                                            color: _getStatusColor(
                                              dosen['status'],
                                            ),
                                          ),
                                        ),
                                      ),
                                      PopupMenuButton(
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
                                                Icon(
                                                  Icons.info_outline,
                                                  size: 20,
                                                ),
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
                                              '/admin/dosen/${dosen['id']}/edit',
                                            );
                                          } else if (value == 'detail') {
                                            context.push(
                                              '/admin/dosen/${dosen['id']}',
                                            );
                                          } else if (value == 'delete') {
                                            _deleteDosen(
                                              dosen['id'],
                                              dosen['nama'],
                                            );
                                          }
                                        },
                                      ),
                                    ],
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
                              ? () => _loadDosens(page: currentPage - 1)
                              : null,
                        ),
                        Text(
                          'Halaman $currentPage dari $lastPage',
                          style: const TextStyle(fontSize: 14),
                        ),
                        IconButton(
                          icon: const Icon(Icons.chevron_right),
                          onPressed: currentPage < lastPage
                              ? () => _loadDosens(page: currentPage + 1)
                              : null,
                        ),
                      ],
                    ),
                  ),
              ],
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/admin/dosen/create'),
        child: const Icon(Icons.add),
      ),
    );
  }
}
