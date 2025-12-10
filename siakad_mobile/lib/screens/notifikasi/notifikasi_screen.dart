import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class NotifikasiScreen extends StatefulWidget {
  const NotifikasiScreen({Key? key}) : super(key: key);

  @override
  State<NotifikasiScreen> createState() => _NotifikasiScreenState();
}

class _NotifikasiScreenState extends State<NotifikasiScreen> {
  List<dynamic> notifikasis = [];
  int unreadCount = 0;
  bool isLoading = true;
  bool isLoadingMore = false;
  int currentPage = 1;
  int lastPage = 1;
  String? errorMessage;
  String? filter; // null, 'read', 'unread'

  @override
  void initState() {
    super.initState();
    _loadNotifikasis();
    _loadUnreadCount();
  }

  Future<void> _loadNotifikasis({bool refresh = false}) async {
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
      String endpoint = '/notifikasi';
      if (filter != null) {
        endpoint += '?filter=$filter&page=$currentPage';
      } else {
        endpoint += '?page=$currentPage';
      }

      final result = await ApiService.get(endpoint);
      if (result['success'] == true) {
        final data = result['data'];
        final newNotifikasis = data['notifikasis'] ?? [];
        final pagination = data['pagination'] ?? {};

        setState(() {
          if (refresh || currentPage == 1) {
            notifikasis = newNotifikasis;
          } else {
            notifikasis.addAll(newNotifikasis);
          }
          currentPage = pagination['current_page'] ?? 1;
          lastPage = pagination['last_page'] ?? 1;
          isLoading = false;
          isLoadingMore = false;
        });

        // Reload unread count after loading
        _loadUnreadCount();
      } else {
        setState(() {
          isLoading = false;
          isLoadingMore = false;
          errorMessage = result['message'] ?? 'Gagal memuat notifikasi';
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

  Future<void> _loadUnreadCount() async {
    final result = await ApiService.get('/notifikasi/unread-count');
    if (result['success'] == true) {
      setState(() {
        unreadCount = result['data']['count'] ?? 0;
      });
    }
  }

  Future<void> _markAsRead(int notifikasiId) async {
    final result = await ApiService.post('/notifikasi/$notifikasiId/read', {});
    if (result['success'] == true) {
      // Update local state
      setState(() {
        final index = notifikasis.indexWhere((n) => n['id'] == notifikasiId);
        if (index != -1) {
          notifikasis[index]['is_read'] = true;
        }
        if (unreadCount > 0) {
          unreadCount--;
        }
      });

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Notifikasi ditandai sebagai sudah dibaca'),
            backgroundColor: Colors.green,
            duration: Duration(seconds: 2),
          ),
        );
      }
    }
  }

  Future<void> _markAllAsRead() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Tandai Semua Sudah Dibaca'),
        content: const Text(
          'Apakah Anda yakin ingin menandai semua notifikasi sebagai sudah dibaca?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Ya, Tandai Semua'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      final result = await ApiService.post('/notifikasi/read-all', {});
      if (result['success'] == true) {
        // Reload notifikasis
        _loadNotifikasis(refresh: true);
        _loadUnreadCount();

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Semua notifikasi ditandai sebagai sudah dibaca'),
              backgroundColor: Colors.green,
            ),
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal menandai semua notifikasi',
              ),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    }
  }

  IconData _getTipeIcon(String? tipe) {
    switch (tipe) {
      case 'info':
        return Icons.info;
      case 'success':
        return Icons.check_circle;
      case 'warning':
        return Icons.warning;
      case 'error':
        return Icons.error;
      default:
        return Icons.notifications;
    }
  }

  Color _getTipeColor(String? tipe) {
    switch (tipe) {
      case 'info':
        return Colors.blue;
      case 'success':
        return Colors.green;
      case 'warning':
        return Colors.orange;
      case 'error':
        return Colors.red;
      default:
        return Colors.grey;
    }
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
          return '${difference.inMinutes} menit yang lalu';
        }
        return '${difference.inHours} jam yang lalu';
      } else if (difference.inDays == 1) {
        return 'Kemarin';
      } else if (difference.inDays < 7) {
        return '${difference.inDays} hari yang lalu';
      } else {
        return DateFormat('dd MMM yyyy', 'id_ID').format(date);
      }
    } catch (e) {
      return dateString;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi'),
        actions: [
          if (unreadCount > 0 && filter != 'read')
            TextButton.icon(
              onPressed: _markAllAsRead,
              icon: const Icon(Icons.done_all, size: 20),
              label: const Text('Tandai Semua'),
              style: TextButton.styleFrom(foregroundColor: Colors.white),
            ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadNotifikasis(refresh: true),
            tooltip: 'Refresh',
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(50),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Row(
              children: [
                Expanded(
                  child: ChoiceChip(
                    label: Text('Semua (${notifikasis.length})'),
                    selected: filter == null,
                    onSelected: (selected) {
                      setState(() {
                        filter = null;
                      });
                      _loadNotifikasis(refresh: true);
                    },
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ChoiceChip(
                    label: Text('Belum Dibaca ($unreadCount)'),
                    selected: filter == 'unread',
                    onSelected: (selected) {
                      setState(() {
                        filter = 'unread';
                      });
                      _loadNotifikasis(refresh: true);
                    },
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ChoiceChip(
                    label: const Text('Sudah Dibaca'),
                    selected: filter == 'read',
                    onSelected: (selected) {
                      setState(() {
                        filter = 'read';
                      });
                      _loadNotifikasis(refresh: true);
                    },
                  ),
                ),
              ],
            ),
          ),
        ),
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
                    onPressed: () => _loadNotifikasis(refresh: true),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : notifikasis.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.notifications_none,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    filter == 'unread'
                        ? 'Tidak ada notifikasi belum dibaca'
                        : filter == 'read'
                        ? 'Tidak ada notifikasi sudah dibaca'
                        : 'Belum ada notifikasi',
                    style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                  ),
                ],
              ),
            )
          : RefreshIndicator(
              onRefresh: () => _loadNotifikasis(refresh: true),
              child: ListView.builder(
                padding: const EdgeInsets.all(8),
                itemCount: notifikasis.length + (isLoadingMore ? 1 : 0),
                itemBuilder: (context, index) {
                  if (index == notifikasis.length) {
                    return const Center(
                      child: Padding(
                        padding: EdgeInsets.all(16),
                        child: CircularProgressIndicator(),
                      ),
                    );
                  }

                  final notifikasi = notifikasis[index];
                  final isRead = notifikasi['is_read'] == true;
                  final tipe = notifikasi['tipe'] ?? 'info';
                  final tipeColor = _getTipeColor(tipe);
                  final tipeIcon = _getTipeIcon(tipe);

                  return Card(
                    margin: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 4,
                    ),
                    color: isRead ? null : Colors.blue[50],
                    child: ListTile(
                      leading: Container(
                        width: 40,
                        height: 40,
                        decoration: BoxDecoration(
                          color: tipeColor.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Icon(tipeIcon, color: tipeColor, size: 20),
                      ),
                      title: Text(
                        notifikasi['judul'] ?? '-',
                        style: TextStyle(
                          fontWeight: isRead
                              ? FontWeight.normal
                              : FontWeight.bold,
                        ),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 4),
                          Text(
                            notifikasi['isi'] ?? '-',
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 4),
                          Text(
                            _formatDate(notifikasi['created_at']),
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                        ],
                      ),
                      trailing: isRead
                          ? null
                          : IconButton(
                              icon: const Icon(
                                Icons.circle,
                                size: 12,
                                color: Colors.blue,
                              ),
                              onPressed: () => _markAsRead(notifikasi['id']),
                              tooltip: 'Tandai sudah dibaca',
                            ),
                      onTap: () {
                        if (!isRead) {
                          _markAsRead(notifikasi['id']);
                        }
                        // TODO: Navigate to link if available
                        if (notifikasi['link'] != null) {
                          // Handle navigation to link
                        }
                      },
                    ),
                  );
                },
              ),
            ),
    );
  }
}
