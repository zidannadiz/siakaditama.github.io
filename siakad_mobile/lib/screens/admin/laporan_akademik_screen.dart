import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class LaporanAkademikScreen extends StatefulWidget {
  const LaporanAkademikScreen({Key? key}) : super(key: key);

  @override
  State<LaporanAkademikScreen> createState() => _LaporanAkademikScreenState();
}

class _LaporanAkademikScreenState extends State<LaporanAkademikScreen> {
  List<dynamic> mahasiswas = [];
  Map<String, dynamic>? stats;
  List<dynamic> prodis = [];
  List<dynamic> semesters = [];
  Map<String, dynamic>? selectedSemester;
  bool isLoading = true;
  String? errorMessage;

  // Filters
  int? selectedProdiId;
  int? selectedSemesterId;
  final TextEditingController _searchController = TextEditingController();

  int currentPage = 1;
  int lastPage = 1;
  int total = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData({bool resetPage = false}) async {
    if (resetPage) {
      currentPage = 1;
    }

    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final queryParams = <String, String>{'page': currentPage.toString()};

      if (selectedProdiId != null) {
        queryParams['prodi_id'] = selectedProdiId!.toString();
      }
      if (selectedSemesterId != null) {
        queryParams['semester_id'] = selectedSemesterId!.toString();
      }
      if (_searchController.text.isNotEmpty) {
        queryParams['search'] = _searchController.text;
      }

      final queryString = queryParams.entries
          .map(
            (e) =>
                '${Uri.encodeComponent(e.key)}=${Uri.encodeComponent(e.value)}',
          )
          .join('&');

      final result = await ApiService.get(
        '/admin/laporan/akademik?$queryString',
      );

      if (result['success'] == true) {
        setState(() {
          mahasiswas = result['data']['mahasiswas'] ?? [];
          stats = result['data']['stats'];
          prodis = result['data']['prodis'] ?? [];
          semesters = result['data']['semesters'] ?? [];
          selectedSemester = result['data']['semester'];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
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

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text('Filter Laporan'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                DropdownButtonFormField<int>(
                  value: selectedProdiId,
                  decoration: const InputDecoration(
                    labelText: 'Program Studi',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(value: null, child: Text('Semua')),
                    ...prodis.map(
                      (prodi) => DropdownMenuItem(
                        value: prodi['id'],
                        child: Text(prodi['nama'] ?? ''),
                      ),
                    ),
                  ],
                  onChanged: (value) {
                    setDialogState(() {
                      selectedProdiId = value;
                    });
                  },
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<int>(
                  value: selectedSemesterId,
                  decoration: const InputDecoration(
                    labelText: 'Semester',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(
                      value: null,
                      child: Text('Semester Aktif'),
                    ),
                    ...semesters.map(
                      (semester) => DropdownMenuItem(
                        value: semester['id'],
                        child: Text(
                          '${semester['nama']} ${semester['tahun_ajaran']}',
                        ),
                      ),
                    ),
                  ],
                  onChanged: (value) {
                    setDialogState(() {
                      selectedSemesterId = value;
                    });
                  },
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () {
                setState(() {
                  selectedProdiId = null;
                  selectedSemesterId = null;
                });
                Navigator.pop(context);
                _loadData(resetPage: true);
              },
              child: const Text('Reset'),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                _loadData(resetPage: true);
              },
              child: const Text('Terapkan'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Laporan Akademik'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
            tooltip: 'Filter',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(resetPage: true),
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
                    onPressed: () => _loadData(resetPage: true),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Statistics Cards
                if (stats != null)
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: Colors.grey[100],
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Statistik',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            Expanded(
                              child: _StatCard(
                                title: 'Total Mahasiswa',
                                value:
                                    stats!['total_mahasiswa']?.toString() ??
                                    '0',
                                color: Colors.blue,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _StatCard(
                                title: 'Rata-rata IPK',
                                value: (stats!['avg_ipk'] ?? 0).toStringAsFixed(
                                  2,
                                ),
                                color: Colors.green,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: _StatCard(
                                title: 'Lulus',
                                value: stats!['lulus']?.toString() ?? '0',
                                color: Colors.green,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _StatCard(
                                title: 'Tidak Lulus',
                                value: stats!['tidak_lulus']?.toString() ?? '0',
                                color: Colors.red,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                // Search Bar
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari NIM, nama, email...',
                      prefixIcon: const Icon(Icons.search),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _loadData(resetPage: true);
                              },
                            )
                          : null,
                    ),
                    onSubmitted: (_) => _loadData(resetPage: true),
                  ),
                ),

                // Semester Info
                if (selectedSemester != null)
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 8,
                    ),
                    color: Colors.blue[50],
                    child: Row(
                      children: [
                        Icon(
                          Icons.calendar_today,
                          size: 16,
                          color: Colors.blue[700],
                        ),
                        const SizedBox(width: 8),
                        Text(
                          'Semester: ${selectedSemester!['nama']} ${selectedSemester!['tahun_ajaran']}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.blue[900],
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),

                // Mahasiswa List
                Expanded(
                  child: mahasiswas.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.school,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada data mahasiswa',
                                style: TextStyle(color: Colors.grey[600]),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () => _loadData(resetPage: true),
                          child: ListView.builder(
                            itemCount: mahasiswas.length,
                            itemBuilder: (context, index) {
                              final mahasiswa = mahasiswas[index];
                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 8,
                                ),
                                child: ListTile(
                                  leading: CircleAvatar(
                                    backgroundColor: Colors.blue,
                                    child: Text(
                                      mahasiswa['nim']?[0] ?? 'M',
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                  ),
                                  title: Text(
                                    mahasiswa['nama'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 4),
                                      Text('NIM: ${mahasiswa['nim'] ?? '-'}'),
                                      if (mahasiswa['prodi'] != null)
                                        Text(
                                          mahasiswa['prodi']['nama'] ?? '-',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey[600],
                                          ),
                                        ),
                                      const SizedBox(height: 4),
                                      Row(
                                        children: [
                                          _IPKBadge(
                                            label: 'IPK',
                                            value: mahasiswa['ipk'] ?? 0.0,
                                          ),
                                          const SizedBox(width: 8),
                                          _IPKBadge(
                                            label: 'IPK Kumulatif',
                                            value:
                                                mahasiswa['ipk_cumulative'] ??
                                                0.0,
                                          ),
                                        ],
                                      ),
                                      Text(
                                        'SKS: ${mahasiswa['total_sks'] ?? 0} (Kumulatif: ${mahasiswa['cumulative_sks'] ?? 0})',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: Icon(
                                    Icons.chevron_right,
                                    color: Colors.grey[400],
                                  ),
                                  onTap: () {
                                    // TODO: Navigate to detail
                                  },
                                ),
                              );
                            },
                          ),
                        ),
                ),

                // Pagination
                if (lastPage > 1)
                  Container(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.chevron_left),
                          onPressed: currentPage > 1
                              ? () {
                                  setState(() {
                                    currentPage--;
                                  });
                                  _loadData();
                                }
                              : null,
                        ),
                        Text(
                          'Halaman $currentPage dari $lastPage',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        IconButton(
                          icon: const Icon(Icons.chevron_right),
                          onPressed: currentPage < lastPage
                              ? () {
                                  setState(() {
                                    currentPage++;
                                  });
                                  _loadData();
                                }
                              : null,
                        ),
                      ],
                    ),
                  ),
              ],
            ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final String title;
  final String value;
  final Color color;

  const _StatCard({
    required this.title,
    required this.value,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      color: color.withOpacity(0.1),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _IPKBadge extends StatelessWidget {
  final String label;
  final double value;

  const _IPKBadge({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    Color color;
    if (value >= 3.5) {
      color = Colors.green;
    } else if (value >= 3.0) {
      color = Colors.blue;
    } else if (value >= 2.5) {
      color = Colors.orange;
    } else {
      color = Colors.red;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color, width: 1),
      ),
      child: Text(
        '$label: ${value.toStringAsFixed(2)}',
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.bold,
          color: color,
        ),
      ),
    );
  }
}
