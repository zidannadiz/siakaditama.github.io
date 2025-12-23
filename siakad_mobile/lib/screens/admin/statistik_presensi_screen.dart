import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class StatistikPresensiScreen extends StatefulWidget {
  const StatistikPresensiScreen({Key? key}) : super(key: key);

  @override
  State<StatistikPresensiScreen> createState() =>
      _StatistikPresensiScreenState();
}

class _StatistikPresensiScreenState extends State<StatistikPresensiScreen> {
  List<dynamic> statistik = [];
  List<dynamic> semesters = [];
  int? selectedSemesterId;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final queryParams = <String, String>{};
      if (selectedSemesterId != null) {
        queryParams['semester_id'] = selectedSemesterId!.toString();
      }

      final queryString = queryParams.entries
          .map(
            (e) =>
                '${Uri.encodeComponent(e.key)}=${Uri.encodeComponent(e.value)}',
          )
          .join('&');

      final result = await ApiService.get(
        '/admin/statistik-presensi${queryString.isNotEmpty ? '?$queryString' : ''}',
      );

      if (result['success'] == true) {
        setState(() {
          statistik = result['data']['statistik'] ?? [];
          semesters = result['data']['semesters'] ?? [];
          if (selectedSemesterId == null &&
              result['data']['selected_semester_id'] != null) {
            selectedSemesterId = result['data']['selected_semester_id'];
          }
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

  void _showSemesterDialog() {
    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text('Pilih Semester'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
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
                  selectedSemesterId = null;
                });
                Navigator.pop(context);
                _loadData();
              },
              child: const Text('Reset'),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                _loadData();
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
        title: const Text('Statistik Presensi'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showSemesterDialog,
            tooltip: 'Filter Semester',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadData,
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
                    onPressed: _loadData,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : statistik.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.bar_chart, size: 64, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'Tidak ada data statistik presensi',
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ],
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadData,
              child: ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: statistik.length,
                itemBuilder: (context, index) {
                  final item = statistik[index];
                  final totalPresensi = item['total_presensi'] ?? 0;
                  final hadir = item['hadir'] ?? 0;
                  final izin = item['izin'] ?? 0;
                  final sakit = item['sakit'] ?? 0;
                  final alpa = item['alpa'] ?? 0;
                  final persentase = totalPresensi > 0
                      ? (hadir / totalPresensi * 100)
                      : 0.0;

                  return Card(
                    margin: const EdgeInsets.only(bottom: 16),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      item['mata_kuliah'] ?? '-',
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    if (item['kode_mk'] != null)
                                      Text(
                                        item['kode_mk'],
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                    if (item['dosen'] != null)
                                      Text(
                                        'Dosen: ${item['dosen']}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                  ],
                                ),
                              ),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 12,
                                  vertical: 6,
                                ),
                                decoration: BoxDecoration(
                                  color: persentase >= 75
                                      ? Colors.green.withOpacity(0.1)
                                      : persentase >= 50
                                      ? Colors.orange.withOpacity(0.1)
                                      : Colors.red.withOpacity(0.1),
                                  borderRadius: BorderRadius.circular(8),
                                  border: Border.all(
                                    color: persentase >= 75
                                        ? Colors.green
                                        : persentase >= 50
                                        ? Colors.orange
                                        : Colors.red,
                                    width: 1,
                                  ),
                                ),
                                child: Text(
                                  '${persentase.toStringAsFixed(1)}%',
                                  style: TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.bold,
                                    color: persentase >= 75
                                        ? Colors.green[700]
                                        : persentase >= 50
                                        ? Colors.orange[700]
                                        : Colors.red[700],
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),
                          Row(
                            children: [
                              Expanded(
                                child: _PresensiStatItem(
                                  label: 'Total',
                                  value: totalPresensi.toString(),
                                  color: Colors.blue,
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: _PresensiStatItem(
                                  label: 'Hadir',
                                  value: hadir.toString(),
                                  color: Colors.green,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Row(
                            children: [
                              Expanded(
                                child: _PresensiStatItem(
                                  label: 'Izin',
                                  value: izin.toString(),
                                  color: Colors.orange,
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: _PresensiStatItem(
                                  label: 'Sakit',
                                  value: sakit.toString(),
                                  color: Colors.blue,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          _PresensiStatItem(
                            label: 'Alpa',
                            value: alpa.toString(),
                            color: Colors.red,
                          ),
                          const SizedBox(height: 8),
                          Text(
                            'Total Mahasiswa: ${item['total_mahasiswa'] ?? 0}',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
    );
  }
}

class _PresensiStatItem extends StatelessWidget {
  final String label;
  final String value;
  final Color color;

  const _PresensiStatItem({
    required this.label,
    required this.value,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        children: [
          Text(
            value,
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(label, style: TextStyle(fontSize: 11, color: Colors.grey[600])),
        ],
      ),
    );
  }
}
