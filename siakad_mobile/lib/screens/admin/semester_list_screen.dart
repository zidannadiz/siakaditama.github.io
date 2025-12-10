import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class SemesterListScreen extends StatefulWidget {
  const SemesterListScreen({Key? key}) : super(key: key);

  @override
  State<SemesterListScreen> createState() => _SemesterListScreenState();
}

class _SemesterListScreenState extends State<SemesterListScreen> {
  List<dynamic> semesters = [];
  bool isLoading = true;
  String? errorMessage;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadSemesters();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadSemesters() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/semester');
      if (result['success'] == true) {
        setState(() {
          semesters = result['data'] ?? [];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data semester';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _deleteSemester(int id, String nama) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Semester'),
        content: Text('Apakah Anda yakin ingin menghapus semester $nama?'),
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
      final result = await ApiService.delete('/admin/semester/$id');
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Semester berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          _loadSemesters();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus semester'),
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
        return Colors.grey;
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

  String _getJenisLabel(String? jenis) {
    switch (jenis) {
      case 'ganjil':
        return 'Ganjil';
      case 'genap':
        return 'Genap';
      default:
        return 'Unknown';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Data Semester'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadSemesters,
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
                        onPressed: _loadSemesters,
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
                          hintText: 'Cari semester...',
                          prefixIcon: const Icon(Icons.search),
                          suffixIcon: _searchController.text.isNotEmpty
                              ? IconButton(
                                  icon: const Icon(Icons.clear),
                                  onPressed: () {
                                    _searchController.clear();
                                    _loadSemesters();
                                  },
                                )
                              : null,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        onChanged: (value) {
                          setState(() {});
                        },
                      ),
                    ),

                    // Summary
                    Container(
                      padding: const EdgeInsets.all(16),
                      color: Colors.amber[50],
                      child: Row(
                        children: [
                          Icon(Icons.calendar_today, color: Colors.amber[700]),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Total Semester',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[700],
                                  ),
                                ),
                                Text(
                                  '${semesters.length} Semester',
                                  style: TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.amber[900],
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
                      child: semesters.isEmpty
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.calendar_today_outlined,
                                      size: 64, color: Colors.grey[400]),
                                  const SizedBox(height: 16),
                                  Text(
                                    'Tidak ada data semester',
                                    style: TextStyle(
                                      fontSize: 16,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ],
                              ),
                            )
                          : RefreshIndicator(
                              onRefresh: _loadSemesters,
                              child: ListView.builder(
                                padding: const EdgeInsets.all(8),
                                itemCount: semesters.length,
                                itemBuilder: (context, index) {
                                  final semester = semesters[index];
                                  final searchQuery =
                                      _searchController.text.toLowerCase();
                                  final semesterText =
                                      '${semester['nama']} ${semester['tahun_ajaran']}'
                                          .toLowerCase();

                                  // Filter by search
                                  if (searchQuery.isNotEmpty &&
                                      !semesterText.contains(searchQuery)) {
                                    return const SizedBox.shrink();
                                  }

                                  return Card(
                                    margin: const EdgeInsets.symmetric(
                                      horizontal: 8,
                                      vertical: 4,
                                    ),
                                    child: ListTile(
                                      leading: CircleAvatar(
                                        backgroundColor: _getStatusColor(
                                                semester['status'])
                                            .withOpacity(0.2),
                                        child: Icon(
                                          Icons.calendar_today,
                                          color: _getStatusColor(
                                              semester['status']),
                                        ),
                                      ),
                                      title: Text(
                                        semester['nama'] ?? '-',
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
                                            'Tahun Ajaran: ${semester['tahun_ajaran'] ?? '-'}',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey[600],
                                            ),
                                          ),
                                          Text(
                                            'Jenis: ${_getJenisLabel(semester['jenis'])}',
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
                                                      semester['status'])
                                                  .withOpacity(0.2),
                                              borderRadius:
                                                  BorderRadius.circular(12),
                                            ),
                                            child: Text(
                                              _getStatusLabel(semester['status']),
                                              style: TextStyle(
                                                fontSize: 10,
                                                fontWeight: FontWeight.bold,
                                                color: _getStatusColor(
                                                    semester['status']),
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
                                                    '/admin/semester/${semester['id']}/edit');
                                              } else if (value == 'detail') {
                                                context.push(
                                                    '/admin/semester/${semester['id']}');
                                              } else if (value == 'delete') {
                                                _deleteSemester(
                                                    semester['id'],
                                                    semester['nama']);
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
                  ],
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => context.push('/admin/semester/create'),
        child: const Icon(Icons.add),
      ),
    );
  }
}

