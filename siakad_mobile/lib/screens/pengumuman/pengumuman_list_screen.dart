import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import 'pengumuman_detail_screen.dart';

class PengumumanListScreen extends StatefulWidget {
  const PengumumanListScreen({Key? key}) : super(key: key);

  @override
  State<PengumumanListScreen> createState() => _PengumumanListScreenState();
}

class _PengumumanListScreenState extends State<PengumumanListScreen> {
  List<dynamic> pengumumans = [];
  bool isLoading = true;
  bool isLoadingMore = false;
  int currentPage = 1;
  int lastPage = 1;
  String? errorMessage;
  String? selectedKategori;
  String? searchQuery;
  final TextEditingController _searchController = TextEditingController();

  final List<String> kategoriList = [
    'semua',
    'umum',
    'akademik',
    'beasiswa',
    'kegiatan',
  ];

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

  Future<void> _loadPengumumans({bool refresh = false}) async {
    if (refresh) {
      setState(() {
        currentPage = 1;
        isLoading = true;
        errorMessage = null;
      });
    } else if (currentPage > 1) {
      setState(() {
        isLoadingMore = true;
      });
    }

    try {
      String endpoint = '/pengumuman?page=$currentPage';
      if (selectedKategori != null && selectedKategori != 'semua') {
        endpoint += '&kategori=$selectedKategori';
      }
      if (searchQuery != null && searchQuery!.isNotEmpty) {
        endpoint += '&search=$searchQuery';
      }

      final result = await ApiService.get(endpoint);
      if (result['success'] == true) {
        final data = result['data'];
        final newPengumumans = data['pengumumans'] ?? [];
        final pagination = data['pagination'] ?? {};

        setState(() {
          if (refresh || currentPage == 1) {
            pengumumans = newPengumumans;
          } else {
            pengumumans.addAll(newPengumumans);
          }
          currentPage = pagination['current_page'] ?? 1;
          lastPage = pagination['last_page'] ?? 1;
          isLoading = false;
          isLoadingMore = false;
        });
      } else {
        setState(() {
          isLoading = false;
          isLoadingMore = false;
          errorMessage = result['message'] ?? 'Gagal memuat pengumuman';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        isLoadingMore = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  void _performSearch() {
    setState(() {
      searchQuery = _searchController.text.trim();
    });
    _loadPengumumans(refresh: true);
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  Color _getKategoriColor(String? kategori) {
    switch (kategori) {
      case 'umum':
        return Colors.blue;
      case 'akademik':
        return Colors.green;
      case 'beasiswa':
        return Colors.orange;
      case 'kegiatan':
        return Colors.purple;
      default:
        return Colors.grey;
    }
  }

  String _getKategoriLabel(String? kategori) {
    switch (kategori) {
      case 'umum':
        return 'Umum';
      case 'akademik':
        return 'Akademik';
      case 'beasiswa':
        return 'Beasiswa';
      case 'kegiatan':
        return 'Kegiatan';
      default:
        return kategori ?? 'Umum';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pengumuman'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadPengumumans(refresh: true),
            tooltip: 'Refresh',
          ),
        ],
      ),
      body: Column(
        children: [
          // Search Bar
          Padding(
            padding: const EdgeInsets.all(16),
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
                          setState(() {
                            searchQuery = null;
                          });
                          _loadPengumumans(refresh: true);
                        },
                      )
                    : null,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                filled: true,
                fillColor: Colors.grey[100],
              ),
              onSubmitted: (_) => _performSearch(),
            ),
          ),

          // Filter Kategori
          Container(
            height: 50,
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: kategoriList.length,
              itemBuilder: (context, index) {
                final kategori = kategoriList[index];
                final isSelected =
                    selectedKategori == kategori ||
                    (selectedKategori == null && kategori == 'semua');

                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                    label: Text(
                      kategori == 'semua'
                          ? 'Semua'
                          : _getKategoriLabel(kategori),
                    ),
                    selected: isSelected,
                    onSelected: (selected) {
                      setState(() {
                        selectedKategori = kategori == 'semua'
                            ? null
                            : kategori;
                      });
                      _loadPengumumans(refresh: true);
                    },
                    selectedColor: Colors.blue[100],
                    labelStyle: TextStyle(
                      color: isSelected ? Colors.blue[700] : Colors.grey[700],
                      fontWeight: isSelected
                          ? FontWeight.bold
                          : FontWeight.normal,
                    ),
                  ),
                );
              },
            ),
          ),

          // Pengumuman List
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : errorMessage != null
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.error_outline,
                          size: 64,
                          color: Colors.red[300],
                        ),
                        const SizedBox(height: 16),
                        Text(
                          errorMessage!,
                          style: TextStyle(color: Colors.red[700]),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: () => _loadPengumumans(refresh: true),
                          child: const Text('Coba Lagi'),
                        ),
                      ],
                    ),
                  )
                : pengumumans.isEmpty
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
                          searchQuery != null && searchQuery!.isNotEmpty
                              ? 'Tidak ada pengumuman yang ditemukan'
                              : 'Belum ada pengumuman',
                          style: TextStyle(
                            fontSize: 16,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  )
                : RefreshIndicator(
                    onRefresh: () => _loadPengumumans(refresh: true),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(8),
                      itemCount: pengumumans.length + (isLoadingMore ? 1 : 0),
                      itemBuilder: (context, index) {
                        if (index == pengumumans.length) {
                          return const Center(
                            child: Padding(
                              padding: EdgeInsets.all(16),
                              child: CircularProgressIndicator(),
                            ),
                          );
                        }

                        final pengumuman = pengumumans[index];
                        final isPinned = pengumuman['is_pinned'] == true;
                        final kategori = pengumuman['kategori'] ?? 'umum';
                        final kategoriColor = _getKategoriColor(kategori);

                        return Card(
                          margin: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          color: isPinned ? Colors.amber[50] : null,
                          child: ListTile(
                            leading: Container(
                              width: 50,
                              height: 50,
                              decoration: BoxDecoration(
                                color: kategoriColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(25),
                              ),
                              child: Icon(
                                isPinned ? Icons.push_pin : Icons.announcement,
                                color: kategoriColor,
                                size: 24,
                              ),
                            ),
                            title: Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    pengumuman['judul'] ?? '-',
                                    style: TextStyle(
                                      fontWeight: isPinned
                                          ? FontWeight.bold
                                          : FontWeight.w600,
                                    ),
                                  ),
                                ),
                                if (isPinned)
                                  Icon(
                                    Icons.push_pin,
                                    size: 16,
                                    color: Colors.amber[700],
                                  ),
                              ],
                            ),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const SizedBox(height: 4),
                                Text(
                                  pengumuman['isi'] ?? '-',
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey[600],
                                  ),
                                ),
                                const SizedBox(height: 8),
                                Row(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 8,
                                        vertical: 4,
                                      ),
                                      decoration: BoxDecoration(
                                        color: kategoriColor.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: Text(
                                        _getKategoriLabel(kategori),
                                        style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                          color: kategoriColor,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      _formatDate(
                                        pengumuman['published_at'] ??
                                            pengumuman['created_at'],
                                      ),
                                      style: TextStyle(
                                        fontSize: 10,
                                        color: Colors.grey[500],
                                      ),
                                    ),
                                  ],
                                ),
                              ],
                            ),
                            trailing: const Icon(
                              Icons.arrow_forward_ios,
                              size: 16,
                            ),
                            onTap: () {
                              context.push('/pengumuman/${pengumuman['id']}');
                            },
                          ),
                        );
                      },
                    ),
                  ),
          ),
        ],
      ),
    );
  }
}
