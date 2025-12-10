import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';
import 'assignment_create_screen.dart';
import 'assignment_detail_screen.dart';

class AssignmentListScreen extends StatefulWidget {
  const AssignmentListScreen({Key? key}) : super(key: key);

  @override
  State<AssignmentListScreen> createState() => _AssignmentListScreenState();
}

class _AssignmentListScreenState extends State<AssignmentListScreen> {
  List<dynamic> jadwals = [];
  List<dynamic> assignments = [];
  int? selectedJadwalId;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData({int? jadwalId}) async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final query = jadwalId != null ? '?jadwal_id=$jadwalId' : '';
      final result = await ApiService.get('/dosen/assignment$query');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          jadwals = data['jadwals'] ?? [];
          assignments = data['assignments'] ?? [];
          selectedJadwalId = data['selected_jadwal_id'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data';
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
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tugas'),
        actions: [
          if (selectedJadwalId != null)
            IconButton(
              icon: const Icon(Icons.add),
              onPressed: () {
                context.push(
                  '/dosen/assignment/create?jadwal_id=$selectedJadwalId',
                );
              },
              tooltip: 'Tambah Tugas',
            ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(jadwalId: selectedJadwalId),
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
                    onPressed: () => _loadData(jadwalId: selectedJadwalId),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Jadwal Selection
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.blue[50],
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Pilih Jadwal Kuliah',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      if (jadwals.isEmpty)
                        const Text('Tidak ada jadwal kuliah')
                      else
                        Wrap(
                          spacing: 8,
                          runSpacing: 8,
                          children: jadwals.map((jadwal) {
                            final isSelected = selectedJadwalId == jadwal['id'];
                            return FilterChip(
                              label: Text(
                                '${jadwal['kode_mk'] ?? '-'} - ${jadwal['mata_kuliah'] ?? '-'}',
                              ),
                              selected: isSelected,
                              onSelected: (selected) {
                                if (selected) {
                                  _loadData(jadwalId: jadwal['id']);
                                }
                              },
                            );
                          }).toList(),
                        ),
                    ],
                  ),
                ),

                // Assignments List
                Expanded(
                  child: selectedJadwalId == null
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.assignment_outlined,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Pilih jadwal kuliah untuk melihat tugas',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () =>
                              _loadData(jadwalId: selectedJadwalId),
                          child: assignments.isEmpty
                              ? Center(
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(
                                        Icons.assignment_outlined,
                                        size: 64,
                                        color: Colors.grey[400],
                                      ),
                                      const SizedBox(height: 16),
                                      Text(
                                        'Belum ada tugas',
                                        style: TextStyle(
                                          fontSize: 16,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                    ],
                                  ),
                                )
                              : ListView.builder(
                                  padding: const EdgeInsets.all(8),
                                  itemCount: assignments.length,
                                  itemBuilder: (context, index) {
                                    final assignment = assignments[index];
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
                                            color:
                                                assignment['status'] ==
                                                    'published'
                                                ? Colors.green[100]
                                                : Colors.grey[300],
                                            borderRadius: BorderRadius.circular(
                                              25,
                                            ),
                                          ),
                                          child: Icon(
                                            Icons.assignment,
                                            color:
                                                assignment['status'] ==
                                                    'published'
                                                ? Colors.green[700]
                                                : Colors.grey[700],
                                            size: 24,
                                          ),
                                        ),
                                        title: Text(
                                          assignment['judul'] ?? '-',
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
                                              'Deadline: ${_formatDate(assignment['deadline'])}',
                                              style: TextStyle(
                                                fontSize: 12,
                                                color: Colors.grey[600],
                                              ),
                                            ),
                                            const SizedBox(height: 4),
                                            Text(
                                              'Submissions: ${assignment['submission_count'] ?? 0} | Graded: ${assignment['graded_count'] ?? 0}',
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
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                    horizontal: 8,
                                                    vertical: 4,
                                                  ),
                                              decoration: BoxDecoration(
                                                color:
                                                    assignment['status'] ==
                                                        'published'
                                                    ? Colors.green[100]
                                                    : Colors.grey[300],
                                                borderRadius:
                                                    BorderRadius.circular(12),
                                              ),
                                              child: Text(
                                                assignment['status'] ==
                                                        'published'
                                                    ? 'Published'
                                                    : 'Draft',
                                                style: TextStyle(
                                                  fontSize: 10,
                                                  fontWeight: FontWeight.bold,
                                                  color:
                                                      assignment['status'] ==
                                                          'published'
                                                      ? Colors.green[700]
                                                      : Colors.grey[700],
                                                ),
                                              ),
                                            ),
                                            const Icon(
                                              Icons.arrow_forward_ios,
                                              size: 16,
                                            ),
                                          ],
                                        ),
                                        onTap: () {
                                          context.push(
                                            '/dosen/assignment/${assignment['id']}',
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
      floatingActionButton: selectedJadwalId != null
          ? FloatingActionButton(
              onPressed: () {
                context.push(
                  '/dosen/assignment/create?jadwal_id=$selectedJadwalId',
                );
              },
              child: const Icon(Icons.add),
              tooltip: 'Tambah Tugas',
            )
          : null,
    );
  }
}
