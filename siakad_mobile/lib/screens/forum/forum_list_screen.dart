import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import 'forum_detail_screen.dart';
import 'forum_create_screen.dart';

class ForumListScreen extends StatefulWidget {
  const ForumListScreen({Key? key}) : super(key: key);

  @override
  State<ForumListScreen> createState() => _ForumListScreenState();
}

class _ForumListScreenState extends State<ForumListScreen> {
  List<dynamic> topics = [];
  bool isLoading = true;
  bool isLoadingMore = false;
  int currentPage = 1;
  int lastPage = 1;
  String? errorMessage;
  String? selectedCategory;
  String? searchQuery;
  final TextEditingController _searchController = TextEditingController();

  final List<String> categoryList = [
    'semua',
    'umum',
    'akademik',
    'organisasi',
    'hobi',
    'lainnya',
  ];

  @override
  void initState() {
    super.initState();
    _loadTopics();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadTopics({bool refresh = false}) async {
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
      String endpoint = '/forum?page=$currentPage';
      if (selectedCategory != null && selectedCategory != 'semua') {
        endpoint += '&category=$selectedCategory';
      }
      if (searchQuery != null && searchQuery!.isNotEmpty) {
        endpoint += '&search=$searchQuery';
      }

      final result = await ApiService.get(endpoint);
      if (result['success'] == true) {
        final data = result['data'];
        final newTopics = data['data'] ?? [];
        final pagination = data;

        setState(() {
          if (refresh || currentPage == 1) {
            topics = newTopics;
          } else {
            topics.addAll(newTopics);
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
          errorMessage = result['message'] ?? 'Gagal memuat forum';
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
    _loadTopics(refresh: true);
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      final now = DateTime.now();
      final difference = now.difference(date);

      if (difference.inDays == 0) {
        if (difference.inHours == 0) {
          if (difference.inMinutes == 0) {
            return 'Baru saja';
          }
          return '${difference.inMinutes} menit lalu';
        }
        return '${difference.inHours} jam lalu';
      } else if (difference.inDays == 1) {
        return 'Kemarin';
      } else if (difference.inDays < 7) {
        return '${difference.inDays} hari lalu';
      } else {
        return DateFormat('dd MMM yyyy', 'id_ID').format(date);
      }
    } catch (e) {
      return dateString;
    }
  }

  Color _getCategoryColor(String? category) {
    switch (category) {
      case 'umum':
        return Colors.blue;
      case 'akademik':
        return Colors.green;
      case 'organisasi':
        return Colors.orange;
      case 'hobi':
        return Colors.purple;
      case 'lainnya':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _getCategoryLabel(String? category) {
    switch (category) {
      case 'umum':
        return 'Umum';
      case 'akademik':
        return 'Akademik';
      case 'organisasi':
        return 'Organisasi';
      case 'hobi':
        return 'Hobi';
      case 'lainnya':
        return 'Lainnya';
      default:
        return category ?? 'Umum';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Forum'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadTopics(refresh: true),
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
                hintText: 'Cari topik...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          setState(() {
                            searchQuery = null;
                          });
                          _loadTopics(refresh: true);
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

          // Filter Category
          Container(
            height: 50,
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              itemCount: categoryList.length,
              itemBuilder: (context, index) {
                final category = categoryList[index];
                final isSelected =
                    selectedCategory == category ||
                    (selectedCategory == null && category == 'semua');

                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                    label: Text(
                      category == 'semua'
                          ? 'Semua'
                          : _getCategoryLabel(category),
                    ),
                    selected: isSelected,
                    onSelected: (selected) {
                      setState(() {
                        selectedCategory = category == 'semua'
                            ? null
                            : category;
                      });
                      _loadTopics(refresh: true);
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

          // Topics List
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
                          onPressed: () => _loadTopics(refresh: true),
                          child: const Text('Coba Lagi'),
                        ),
                      ],
                    ),
                  )
                : topics.isEmpty
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.forum_outlined,
                          size: 64,
                          color: Colors.grey[400],
                        ),
                        const SizedBox(height: 16),
                        Text(
                          searchQuery != null && searchQuery!.isNotEmpty
                              ? 'Tidak ada topik yang ditemukan'
                              : 'Belum ada topik forum',
                          style: TextStyle(
                            fontSize: 16,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  )
                : RefreshIndicator(
                    onRefresh: () => _loadTopics(refresh: true),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(8),
                      itemCount: topics.length + (isLoadingMore ? 1 : 0),
                      itemBuilder: (context, index) {
                        if (index == topics.length) {
                          return const Center(
                            child: Padding(
                              padding: EdgeInsets.all(16),
                              child: CircularProgressIndicator(),
                            ),
                          );
                        }

                        final topic = topics[index];
                        final isPinned = topic['is_pinned'] == true;
                        final category = topic['category'] ?? 'umum';
                        final categoryColor = _getCategoryColor(category);
                        final latestPost = topic['latest_post'];
                        final creator = topic['creator'] ?? {};

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
                                color: categoryColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(25),
                              ),
                              child: Icon(
                                isPinned ? Icons.push_pin : Icons.forum,
                                color: categoryColor,
                                size: 24,
                              ),
                            ),
                            title: Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    topic['title'] ?? '-',
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
                                if (topic['description'] != null)
                                  Text(
                                    topic['description'] ?? '',
                                    maxLines: 1,
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
                                        color: categoryColor.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: Text(
                                        _getCategoryLabel(category),
                                        style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                          color: categoryColor,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      '${topic['replies_count'] ?? 0} balasan',
                                      style: TextStyle(
                                        fontSize: 10,
                                        color: Colors.grey[500],
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    if (latestPost != null)
                                      Text(
                                        _formatDate(latestPost['created_at']),
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
                              context.push('/forum/${topic['id']}');
                            },
                          ),
                        );
                      },
                    ),
                  ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          context.push('/forum/create');
        },
        child: const Icon(Icons.add),
        tooltip: 'Buat Topik Baru',
      ),
    );
  }
}
