import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import 'qna_detail_screen.dart';
import 'qna_create_screen.dart';

class QnAListScreen extends StatefulWidget {
  const QnAListScreen({Key? key}) : super(key: key);

  @override
  State<QnAListScreen> createState() => _QnAListScreenState();
}

class _QnAListScreenState extends State<QnAListScreen> {
  List<dynamic> questions = [];
  bool isLoading = true;
  bool isLoadingMore = false;
  int currentPage = 1;
  int lastPage = 1;
  String? errorMessage;
  String? selectedCategory;
  String? selectedStatus;
  String? searchQuery;
  final TextEditingController _searchController = TextEditingController();

  final List<String> categoryList = [
    'semua',
    'akademik',
    'administrasi',
    'teknologi',
    'umum',
  ];

  final List<String> statusList = ['semua', 'open', 'answered', 'closed'];

  @override
  void initState() {
    super.initState();
    _loadQuestions();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadQuestions({bool refresh = false}) async {
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
      String endpoint = '/qna?page=$currentPage';
      if (selectedCategory != null && selectedCategory != 'semua') {
        endpoint += '&category=$selectedCategory';
      }
      if (selectedStatus != null && selectedStatus != 'semua') {
        endpoint += '&status=$selectedStatus';
      }
      if (searchQuery != null && searchQuery!.isNotEmpty) {
        endpoint += '&search=$searchQuery';
      }

      final result = await ApiService.get(endpoint);
      if (result['success'] == true) {
        final data = result['data'];
        final newQuestions = data['data'] ?? [];
        final pagination = data;

        setState(() {
          if (refresh || currentPage == 1) {
            questions = newQuestions;
          } else {
            questions.addAll(newQuestions);
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
          errorMessage = result['message'] ?? 'Gagal memuat pertanyaan';
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
    _loadQuestions(refresh: true);
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
      case 'akademik':
        return Colors.green;
      case 'administrasi':
        return Colors.blue;
      case 'teknologi':
        return Colors.purple;
      case 'umum':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }

  String _getCategoryLabel(String? category) {
    switch (category) {
      case 'akademik':
        return 'Akademik';
      case 'administrasi':
        return 'Administrasi';
      case 'teknologi':
        return 'Teknologi';
      case 'umum':
        return 'Umum';
      default:
        return category ?? 'Umum';
    }
  }

  Color _getStatusColor(String? status) {
    switch (status) {
      case 'open':
        return Colors.orange;
      case 'answered':
        return Colors.green;
      case 'closed':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _getStatusLabel(String? status) {
    switch (status) {
      case 'open':
        return 'Terbuka';
      case 'answered':
        return 'Terjawab';
      case 'closed':
        return 'Tertutup';
      default:
        return status ?? 'Unknown';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Q&A'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadQuestions(refresh: true),
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
                hintText: 'Cari pertanyaan...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          setState(() {
                            searchQuery = null;
                          });
                          _loadQuestions(refresh: true);
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

          // Filter Category & Status
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
                      _loadQuestions(refresh: true);
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

          // Questions List
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
                          onPressed: () => _loadQuestions(refresh: true),
                          child: const Text('Coba Lagi'),
                        ),
                      ],
                    ),
                  )
                : questions.isEmpty
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          Icons.help_outline,
                          size: 64,
                          color: Colors.grey[400],
                        ),
                        const SizedBox(height: 16),
                        Text(
                          searchQuery != null && searchQuery!.isNotEmpty
                              ? 'Tidak ada pertanyaan yang ditemukan'
                              : 'Belum ada pertanyaan',
                          style: TextStyle(
                            fontSize: 16,
                            color: Colors.grey[600],
                          ),
                        ),
                      ],
                    ),
                  )
                : RefreshIndicator(
                    onRefresh: () => _loadQuestions(refresh: true),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(8),
                      itemCount: questions.length + (isLoadingMore ? 1 : 0),
                      itemBuilder: (context, index) {
                        if (index == questions.length) {
                          return const Center(
                            child: Padding(
                              padding: EdgeInsets.all(16),
                              child: CircularProgressIndicator(),
                            ),
                          );
                        }

                        final question = questions[index];
                        final category = question['category'] ?? 'umum';
                        final categoryColor = _getCategoryColor(category);
                        final status = question['status'] ?? 'open';
                        final statusColor = _getStatusColor(status);
                        final user = question['user'] ?? {};
                        final hasBestAnswer = question['best_answer'] != null;

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
                                color: categoryColor.withOpacity(0.1),
                                borderRadius: BorderRadius.circular(25),
                              ),
                              child: Icon(
                                hasBestAnswer
                                    ? Icons.check_circle
                                    : Icons.help_outline,
                                color: hasBestAnswer
                                    ? Colors.green
                                    : categoryColor,
                                size: 24,
                              ),
                            ),
                            title: Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    question['title'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ),
                                if (hasBestAnswer)
                                  Icon(
                                    Icons.star,
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
                                  question['content'] ?? '',
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
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                        horizontal: 8,
                                        vertical: 4,
                                      ),
                                      decoration: BoxDecoration(
                                        color: statusColor.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: Text(
                                        _getStatusLabel(status),
                                        style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                          color: statusColor,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      '${question['answers_count'] ?? 0} jawaban',
                                      style: TextStyle(
                                        fontSize: 10,
                                        color: Colors.grey[500],
                                      ),
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      _formatDate(question['created_at']),
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
                              context.push('/qna/${question['id']}');
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
          context.push('/qna/create');
        },
        child: const Icon(Icons.add),
        tooltip: 'Ajukan Pertanyaan',
      ),
    );
  }
}
