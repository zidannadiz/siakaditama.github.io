import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class MataKuliahListScreen extends StatefulWidget {
  const MataKuliahListScreen({Key? key}) : super(key: key);

  @override
  State<MataKuliahListScreen> createState() => _MataKuliahListScreenState();
}

class _MataKuliahListScreenState extends State<MataKuliahListScreen> {
  List<dynamic> mataKuliahs = [];
  bool isLoading = true;
  String? errorMessage;
  int currentPage = 1;
  int lastPage = 1;
  int total = 0;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadMataKuliahs();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadMataKuliahs({int page = 1}) async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/mata-kuliah?page=$page');
      if (result['success'] == true) {
        setState(() {
          mataKuliahs = result['data']['mata_kuliahs'] ?? [];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
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

  Future<void> _deleteMataKuliah(int id, String nama) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Mata Kuliah'),
        content: Text('Apakah Anda yakin ingin menghapus mata kuliah $nama?'),
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
      final result = await ApiService.delete('/admin/mata-kuliah/$id');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Mata kuliah berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadMataKuliahs(page: currentPage);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus mata kuliah'),
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Data Mata Kuliah'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadMataKuliahs(page: currentPage),
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
                        onPressed: () => _loadMataKuliahs(page: currentPage),
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
                          hintText: 'Cari mata kuliah...',
                          prefixIcon: const Icon(Icons.search),
                          suffixIcon: _searchController.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.clear),
                                  onPressed: () {
                                    _searchController.clear();
                                    _loadMataKuliahs();
                                  },
                                )
                              : null,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        onSubmitted: (value) {
                          // TODO: Implement search
                          _loadMataKuliahs();
                        },
                      ),
                    ),

                    // Summary
                    Container(
                      padding: const EdgeInsets.all(16),
                      color: Colors.deepPurple[50],
                      child: Row(
                        children: [
                          Icon(Icons.menu_book, color: Colors.deepPurple[700]),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Total Mata Kuliah',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[700],
                                  ),
                                ),
                                Text(
                                  '$total Mata Kuliah',
                                  style: TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.deepPurple[900],
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
                      child: mataKuliahs.isEmpty
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.book_outlined,
                                      size: 64, color: Colors.grey[400]),
                                  const SizedBox(height: 16),
                                  Text(
                                    'Tidak ada data mata kuliah',
                                    style: TextStyle(
                                      fontSize: 16,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ],
                              ),
                            )
                          : RefreshIndicator(
                              onRefresh: () => _loadMataKuliahs(page: currentPage),
                              child: ListView.builder(
                                padding: const EdgeInsets.all(8),
                                itemCount: mataKuliahs.length,
                                itemBuilder: (context, index) {
                                  final mk = mataKuliahs[index];
                                  return Card(
                                    margin: const EdgeInsets.symmetric(
                                      horizontal: 8,
                                      vertical: 4,
                                    ),
                                    child: ListTile(
                                      leading: CircleAvatar(
                                        backgroundColor: Colors.deepPurple[100],
                                        child: Text(
                                          (mk['kode_mk'] ?? 'M')[0].toUpperCase(),
                                          style: TextStyle(
                                            color: Colors.deepPurple[900],
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ),
                                      title: Text(
                                        mk['nama'] ?? '-',
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
                                            'Kode: ${mk['kode_mk'] ?? '-'}',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey[600],
                                            ),
                                          ),
                                          Text(
                                            'Prodi: ${mk['prodi'] ?? '-'} | SKS: ${mk['sks'] ?? 0}',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey[600],
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
                                                Icon(Icons.info_outline,
                                                    size: 20),
                                                SizedBox(width: 8),
                                                Text('Detail'),
                                              ],
                                            ),
                                          ),
                                          const PopupMenuItem(
                                            value: 'delete',
                                            child: Row(
                                              children: [
                                                Icon(Icons.delete,
                                                    size: 20,
                                                    color: Colors.red),
                                                SizedBox(width: 8),
                                                Text('Hapus',
                                                    style: TextStyle(
                                                        color: Colors.red)),
                                              ],
                                            ),
                                          ),
                                        ],
                                        onSelected: (value) {
                                          if (value == 'edit') {
                                            context.push(
                                                '/admin/mata-kuliah/${mk['id']}/edit');
                                          } else if (value == 'detail') {
                                            context.push(
                                                '/admin/mata-kuliah/${mk['id']}');
                                          } else if (value == 'delete') {
                                            _deleteMataKuliah(
                                                mk['id'], mk['nama']);
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
                                  ? () => _loadMataKuliahs(page: currentPage - 1)
                                  : null,
                            ),
                            Text(
                              'Halaman $currentPage dari $lastPage',
                              style: const TextStyle(fontSize: 14),
                            ),
                            IconButton(
                              icon: const Icon(Icons.chevron_right),
                              onPressed: currentPage < lastPage
                                  ? () => _loadMataKuliahs(page: currentPage + 1)
                                  : null,
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/admin/mata-kuliah/create'),
        child: const Icon(Icons.add),
      ),
    );
  }
}

