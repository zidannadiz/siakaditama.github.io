import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class ProdiListScreen extends StatefulWidget {
  const ProdiListScreen({Key? key}) : super(key: key);

  @override
  State<ProdiListScreen> createState() => _ProdiListScreenState();
}

class _ProdiListScreenState extends State<ProdiListScreen> {
  List<dynamic> prodis = [];
  bool isLoading = true;
  String? errorMessage;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadProdis();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadProdis() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/prodi');
      if (result['success'] == true) {
        setState(() {
          prodis = result['data'] ?? [];
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

  Future<void> _deleteProdi(int id, String nama) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Prodi'),
        content: Text('Apakah Anda yakin ingin menghapus prodi $nama?'),
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
      final result = await ApiService.delete('/admin/prodi/$id');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Prodi berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadProdis();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus prodi'),
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
        title: const Text('Data Program Studi'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadProdis,
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
                    onPressed: _loadProdis,
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
                      hintText: 'Cari prodi...',
                      prefixIcon: const Icon(Icons.search),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _loadProdis();
                              },
                            )
                          : null,
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    onChanged: (value) {
                      // Simple client-side search
                      setState(() {});
                    },
                  ),
                ),

                // Summary
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.indigo[50],
                  child: Row(
                    children: [
                      Icon(Icons.school, color: Colors.indigo[700]),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Total Program Studi',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[700],
                              ),
                            ),
                            Text(
                              '${prodis.length} Prodi',
                              style: TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: Colors.indigo[900],
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
                  child: prodis.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.school_outlined,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada data prodi',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: _loadProdis,
                          child: ListView.builder(
                            padding: const EdgeInsets.all(8),
                            itemCount: prodis.length,
                            itemBuilder: (context, index) {
                              final prodi = prodis[index];
                              final searchQuery = _searchController.text
                                  .toLowerCase();
                              final prodiText =
                                  '${prodi['kode']} ${prodi['nama']}'
                                      .toLowerCase();

                              // Filter by search
                              if (searchQuery.isNotEmpty &&
                                  !prodiText.contains(searchQuery)) {
                                return const SizedBox.shrink();
                              }

                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                child: ListTile(
                                  leading: CircleAvatar(
                                    backgroundColor: Colors.indigo[100],
                                    child: Text(
                                      (prodi['kode'] ?? 'P')[0].toUpperCase(),
                                      style: TextStyle(
                                        color: Colors.indigo[900],
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                  title: Text(
                                    prodi['nama'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  subtitle: Text(
                                    'Kode: ${prodi['kode'] ?? '-'}',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: Colors.grey[600],
                                    ),
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
                                          '/admin/prodi/${prodi['id']}/edit',
                                        );
                                      } else if (value == 'detail') {
                                        context.push(
                                          '/admin/prodi/${prodi['id']}',
                                        );
                                      } else if (value == 'delete') {
                                        _deleteProdi(
                                          prodi['id'],
                                          prodi['nama'],
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
              ],
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/admin/prodi/create'),
        child: const Icon(Icons.add),
      ),
    );
  }
}
