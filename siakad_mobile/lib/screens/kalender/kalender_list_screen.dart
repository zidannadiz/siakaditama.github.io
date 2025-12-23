import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';

class KalenderListScreen extends StatefulWidget {
  const KalenderListScreen({Key? key}) : super(key: key);

  @override
  State<KalenderListScreen> createState() => _KalenderListScreenState();
}

class _KalenderListScreenState extends State<KalenderListScreen> {
  List<dynamic> events = [];
  bool isLoading = true;
  String? errorMessage;
  String? selectedJenis;
  String? selectedSemester;
  List<dynamic> semesters = [];
  Map<String, dynamic>? userRole;

  final Map<String, String> jenisLabels = {
    'semester': 'Semester',
    'krs': 'KRS',
    'pembayaran': 'Pembayaran',
    'ujian': 'Ujian',
    'libur': 'Libur',
    'kegiatan': 'Kegiatan',
    'pengumuman': 'Pengumuman',
    'lainnya': 'Lainnya',
  };

  final Map<String, Color> jenisColors = {
    'semester': Colors.blue,
    'krs': Colors.green,
    'pembayaran': Colors.orange,
    'ujian': Colors.red,
    'libur': Colors.purple,
    'kegiatan': Colors.pink,
    'pengumuman': Colors.cyan,
    'lainnya': Colors.grey,
  };

  @override
  void initState() {
    super.initState();
    _loadUserRole();
    _loadEvents();
  }

  Future<void> _loadUserRole() async {
    final user = await StorageService.getUser();
    setState(() {
      userRole = user;
    });
  }

  Future<void> _loadEvents() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      String url = '/kalender-akademik';
      if (selectedJenis != null) {
        url += '?jenis=$selectedJenis';
      }
      if (selectedSemester != null) {
        url += url.contains('?')
            ? '&semester_id=$selectedSemester'
            : '?semester_id=$selectedSemester';
      }

      final result = await ApiService.get(url);
      if (result['success'] == true) {
        setState(() {
          events = result['data'] ?? [];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat kalender akademik';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '-';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMMM yyyy', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  Color _getJenisColor(String? jenis) {
    if (jenis == null) return Colors.grey;
    return jenisColors[jenis] ?? Colors.grey;
  }

  String _getJenisLabel(String? jenis) {
    if (jenis == null) return 'Lainnya';
    return jenisLabels[jenis] ?? jenis;
  }

  @override
  Widget build(BuildContext context) {
    final isAdmin = userRole?['role'] == 'admin';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Kalender Akademik'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadEvents,
            tooltip: 'Refresh',
          ),
          if (isAdmin)
            IconButton(
              icon: const Icon(Icons.add),
              onPressed: () => context.push('/admin/kalender-akademik/create'),
              tooltip: 'Tambah Event',
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
                    onPressed: _loadEvents,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Filters
                Container(
                  padding: const EdgeInsets.all(8),
                  color: Colors.blue[50],
                  child: Row(
                    children: [
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          value: selectedJenis,
                          decoration: const InputDecoration(
                            labelText: 'Jenis',
                            border: OutlineInputBorder(),
                            contentPadding: EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 8,
                            ),
                          ),
                          items: [
                            const DropdownMenuItem(
                              value: null,
                              child: Text('Semua Jenis'),
                            ),
                            ...jenisLabels.entries.map((entry) {
                              return DropdownMenuItem(
                                value: entry.key,
                                child: Text(entry.value),
                              );
                            }),
                          ],
                          onChanged: (value) {
                            setState(() {
                              selectedJenis = value;
                            });
                            _loadEvents();
                          },
                        ),
                      ),
                    ],
                  ),
                ),

                // List
                Expanded(
                  child: events.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.calendar_today,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada event',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: _loadEvents,
                          child: ListView.builder(
                            padding: const EdgeInsets.all(8),
                            itemCount: events.length,
                            itemBuilder: (context, index) {
                              final event = events[index];
                              final color = _getJenisColor(event['jenis']);
                              final isImportant = event['is_important'] == true;

                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                elevation: isImportant ? 4 : 1,
                                color: isImportant
                                    ? color.withOpacity(0.1)
                                    : null,
                                child: ListTile(
                                  leading: Container(
                                    width: 4,
                                    height: double.infinity,
                                    color: color,
                                  ),
                                  title: Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          event['judul'] ?? '-',
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                            fontSize: 16,
                                          ),
                                        ),
                                      ),
                                      if (isImportant)
                                        Icon(
                                          Icons.star,
                                          color: Colors.orange,
                                          size: 20,
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
                                          Icon(
                                            Icons.calendar_today,
                                            size: 14,
                                            color: Colors.grey[600],
                                          ),
                                          const SizedBox(width: 4),
                                          Expanded(
                                            child: Text(
                                              event['tanggal_selesai'] != null
                                                  ? '${_formatDate(event['tanggal_mulai'])} - ${_formatDate(event['tanggal_selesai'])}'
                                                  : _formatDate(
                                                      event['tanggal_mulai'],
                                                    ),
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Colors.grey[600],
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                      if (event['jam_mulai'] != null) ...[
                                        const SizedBox(height: 2),
                                        Row(
                                          children: [
                                            Icon(
                                              Icons.access_time,
                                              size: 14,
                                              color: Colors.grey[600],
                                            ),
                                            const SizedBox(width: 4),
                                            Text(
                                              '${event['jam_mulai']}${event['jam_selesai'] != null ? ' - ${event['jam_selesai']}' : ''}',
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Colors.grey[600],
                                              ),
                                            ),
                                          ],
                                        ),
                                      ],
                                      const SizedBox(height: 4),
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 8,
                                          vertical: 4,
                                        ),
                                        decoration: BoxDecoration(
                                          color: color.withOpacity(0.2),
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Text(
                                          _getJenisLabel(event['jenis']),
                                          style: TextStyle(
                                            fontSize: 10,
                                            fontWeight: FontWeight.bold,
                                            color: color,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: const Icon(
                                    Icons.chevron_right,
                                    size: 20,
                                  ),
                                  onTap: () {
                                    context.push(
                                      '/kalender-akademik/${event['id']}',
                                    );
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
