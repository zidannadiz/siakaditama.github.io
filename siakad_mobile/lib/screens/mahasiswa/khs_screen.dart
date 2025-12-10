import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class KHSScreen extends StatefulWidget {
  const KHSScreen({Key? key}) : super(key: key);

  @override
  State<KHSScreen> createState() => _KHSScreenState();
}

class _KHSScreenState extends State<KHSScreen> {
  List<dynamic> semesters = [];
  Map<String, dynamic>? selectedSemester;
  List<dynamic> nilais = [];
  double ipk = 0.0;
  int totalSks = 0;
  bool isLoadingSemesters = true;
  bool isLoadingKHS = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadSemesters();
  }

  Future<void> _loadSemesters() async {
    setState(() {
      isLoadingSemesters = true;
      errorMessage = null;
    });

    final result = await ApiService.get('/mahasiswa/khs');
    if (result['success'] == true) {
      setState(() {
        semesters = result['data']['semesters'] ?? [];
        isLoadingSemesters = false;
        // Auto-select first semester or active semester
        if (semesters.isNotEmpty) {
          final activeSemester = semesters.firstWhere(
            (s) => s['status'] == 'aktif',
            orElse: () => semesters.first,
          );
          _loadKHS(activeSemester['id']);
        }
      });
    } else {
      setState(() {
        isLoadingSemesters = false;
        errorMessage = result['message'] ?? 'Gagal memuat data semester';
      });
    }
  }

  Future<void> _loadKHS(int? semesterId) async {
    if (semesterId == null) return;

    setState(() {
      isLoadingKHS = true;
      selectedSemester = semesters.firstWhere(
        (s) => s['id'] == semesterId,
        orElse: () => {},
      );
    });

    final result = await ApiService.get('/mahasiswa/khs/$semesterId');
    if (result['success'] == true) {
      setState(() {
        nilais = result['data']['nilais'] ?? [];
        ipk = (result['data']['ipk'] ?? 0.0).toDouble();
        totalSks = result['data']['total_sks'] ?? 0;
        selectedSemester = result['data']['semester'];
        isLoadingKHS = false;
      });
    } else {
      setState(() {
        isLoadingKHS = false;
        errorMessage = result['message'] ?? 'Gagal memuat data KHS';
      });
    }
  }

  Color _getGradeColor(String? hurufMutu) {
    if (hurufMutu == null) return Colors.grey;
    switch (hurufMutu.toUpperCase()) {
      case 'A':
        return Colors.green;
      case 'B':
        return Colors.blue;
      case 'C':
        return Colors.orange;
      case 'D':
        return Colors.red;
      case 'E':
        return Colors.red[900]!;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Kartu Hasil Studi (KHS)'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () {
              _loadSemesters();
            },
            tooltip: 'Refresh',
          ),
        ],
      ),
      body: isLoadingSemesters
          ? const Center(child: CircularProgressIndicator())
          : errorMessage != null && semesters.isEmpty
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
                    // Semester Selector
                    Container(
                      padding: const EdgeInsets.all(16),
                      color: Colors.blue[50],
                      child: DropdownButtonFormField<int>(
                        value: selectedSemester?['id'],
                        decoration: InputDecoration(
                          labelText: 'Pilih Semester',
                          prefixIcon: const Icon(Icons.calendar_today),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                          filled: true,
                          fillColor: Colors.white,
                        ),
                        items: semesters.map((semester) {
                          return DropdownMenuItem<int>(
                            value: semester['id'],
                            child: Text(
                              '${semester['nama']} ${semester['status'] == 'aktif' ? '(Aktif)' : ''}',
                            ),
                          );
                        }).toList(),
                        onChanged: (value) {
                          if (value != null) {
                            _loadKHS(value);
                          }
                        },
                      ),
                    ),

                    // IPK and Total SKS Card
                    if (selectedSemester != null)
                      Container(
                        padding: const EdgeInsets.all(16),
                        color: Colors.white,
                        child: Row(
                          children: [
                            Expanded(
                              child: Card(
                                color: Colors.green[50],
                                child: Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Column(
                                    children: [
                                      const Text(
                                        'IPK',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        ipk.toStringAsFixed(2),
                                        style: TextStyle(
                                          fontSize: 28,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.green[700],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Card(
                                color: Colors.orange[50],
                                child: Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Column(
                                    children: [
                                      const Text(
                                        'Total SKS',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        '$totalSks',
                                        style: TextStyle(
                                          fontSize: 28,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.orange[700],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),

                    // KHS List
                    Expanded(
                      child: isLoadingKHS
                          ? const Center(child: CircularProgressIndicator())
                          : nilais.isEmpty
                              ? Center(
                                  child: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.assignment_outlined,
                                          size: 64, color: Colors.grey[400]),
                                      const SizedBox(height: 16),
                                      Text(
                                        'Belum ada nilai',
                                        style: TextStyle(
                                          fontSize: 16,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        'Nilai akan muncul setelah dosen menginput nilai',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[500],
                                        ),
                                        textAlign: TextAlign.center,
                                      ),
                                    ],
                                  ),
                                )
                              : RefreshIndicator(
                                  onRefresh: () => _loadKHS(
                                      selectedSemester?['id']),
                                  child: ListView.builder(
                                    padding: const EdgeInsets.all(16),
                                    itemCount: nilais.length,
                                    itemBuilder: (context, index) {
                                      final nilai = nilais[index];
                                      final hurufMutu =
                                          nilai['huruf_mutu'] ?? '-';
                                      final gradeColor =
                                          _getGradeColor(hurufMutu);

                                      return Card(
                                        margin: const EdgeInsets.only(bottom: 12),
                                        child: ExpansionTile(
                                          leading: Container(
                                            width: 50,
                                            height: 50,
                                            decoration: BoxDecoration(
                                              color: gradeColor.withOpacity(0.1),
                                              borderRadius:
                                                  BorderRadius.circular(8),
                                            ),
                                            child: Center(
                                              child: Text(
                                                hurufMutu,
                                                style: TextStyle(
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.bold,
                                                  color: gradeColor,
                                                ),
                                              ),
                                            ),
                                          ),
                                          title: Text(
                                            nilai['mata_kuliah'] ?? '-',
                                            style: const TextStyle(
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                          subtitle: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              const SizedBox(height: 4),
                                              if (nilai['kode_mk'] != null)
                                                Text(
                                                  'Kode: ${nilai['kode_mk'] ?? '-'}',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    color: Colors.grey[600],
                                                  ),
                                                ),
                                              if (nilai['dosen'] != null)
                                                Text(
                                                  'Dosen: ${nilai['dosen'] ?? '-'}',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    color: Colors.grey[600],
                                                  ),
                                                ),
                                            ],
                                          ),
                                          trailing: Column(
                                            mainAxisAlignment:
                                                MainAxisAlignment.center,
                                            children: [
                                              Text(
                                                '${nilai['sks'] ?? 0} SKS',
                                                style: TextStyle(
                                                  fontSize: 12,
                                                  color: Colors.grey[600],
                                                ),
                                              ),
                                              const SizedBox(height: 4),
                                              Text(
                                                nilai['nilai_akhir'] != null
                                                    ? nilai['nilai_akhir']
                                                        .toStringAsFixed(1)
                                                    : '-',
                                                style: TextStyle(
                                                  fontSize: 16,
                                                  fontWeight: FontWeight.bold,
                                                  color: gradeColor,
                                                ),
                                              ),
                                            ],
                                          ),
                                          children: [
                                            Padding(
                                              padding: const EdgeInsets.all(16),
                                              child: Column(
                                                children: [
                                                  Row(
                                                    mainAxisAlignment:
                                                        MainAxisAlignment
                                                            .spaceAround,
                                                    children: [
                                                      _DetailItem(
                                                        label: 'Tugas',
                                                        value: nilai[
                                                                    'nilai_tugas'] !=
                                                                null
                                                            ? nilai['nilai_tugas']
                                                                .toStringAsFixed(
                                                                    1)
                                                            : '-',
                                                      ),
                                                      _DetailItem(
                                                        label: 'UTS',
                                                        value: nilai[
                                                                    'nilai_uts'] !=
                                                                null
                                                            ? nilai['nilai_uts']
                                                                .toStringAsFixed(
                                                                    1)
                                                            : '-',
                                                      ),
                                                      _DetailItem(
                                                        label: 'UAS',
                                                        value: nilai[
                                                                    'nilai_uas'] !=
                                                                null
                                                            ? nilai['nilai_uas']
                                                                .toStringAsFixed(
                                                                    1)
                                                            : '-',
                                                      ),
                                                    ],
                                                  ),
                                                  const Divider(),
                                                  Row(
                                                    mainAxisAlignment:
                                                        MainAxisAlignment
                                                            .spaceAround,
                                                    children: [
                                                      _DetailItem(
                                                        label: 'Nilai Akhir',
                                                        value: nilai['nilai_akhir'] !=
                                                                null
                                                            ? nilai['nilai_akhir']
                                                                .toStringAsFixed(
                                                                    1)
                                                            : '-',
                                                        isHighlight: true,
                                                      ),
                                                      _DetailItem(
                                                        label: 'Huruf Mutu',
                                                        value: hurufMutu,
                                                        isHighlight: true,
                                                      ),
                                                      _DetailItem(
                                                        label: 'Bobot',
                                                        value: nilai['bobot'] !=
                                                                null
                                                            ? nilai['bobot']
                                                                .toStringAsFixed(
                                                                    1)
                                                            : '-',
                                                        isHighlight: true,
                                                      ),
                                                    ],
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ],
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

class _DetailItem extends StatelessWidget {
  final String label;
  final String value;
  final bool isHighlight;

  const _DetailItem({
    required this.label,
    required this.value,
    this.isHighlight = false,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: 12,
            color: Colors.grey[600],
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
            fontSize: 16,
            fontWeight: isHighlight ? FontWeight.bold : FontWeight.normal,
            color: isHighlight ? Colors.blue[700] : Colors.black87,
          ),
        ),
      ],
    );
  }
}
