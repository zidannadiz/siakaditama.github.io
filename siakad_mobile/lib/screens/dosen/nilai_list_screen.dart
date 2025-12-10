import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';
import 'nilai_input_screen.dart';

class NilaiListScreen extends StatefulWidget {
  const NilaiListScreen({Key? key}) : super(key: key);

  @override
  State<NilaiListScreen> createState() => _NilaiListScreenState();
}

class _NilaiListScreenState extends State<NilaiListScreen> {
  List<dynamic> jadwals = [];
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadJadwals();
  }

  Future<void> _loadJadwals() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    final result = await ApiService.get('/dosen/nilai');
    if (result['success'] == true) {
      setState(() {
        jadwals = result['data']['jadwals'] ?? [];
        isLoading = false;
      });
    } else {
      setState(() {
        isLoading = false;
        errorMessage = result['message'] ?? 'Gagal memuat data jadwal';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Input Nilai'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadJadwals,
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
                    onPressed: _loadJadwals,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : jadwals.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.class_outlined, size: 64, color: Colors.grey[400]),
                  const SizedBox(height: 16),
                  Text(
                    'Belum ada jadwal',
                    style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Tidak ada jadwal kuliah yang tersedia',
                    style: TextStyle(fontSize: 12, color: Colors.grey[500]),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadJadwals,
              child: ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: jadwals.length,
                itemBuilder: (context, index) {
                  final jadwal = jadwals[index];
                  final totalMahasiswa = jadwal['total_mahasiswa'] ?? 0;
                  final totalNilai = jadwal['total_nilai'] ?? 0;

                  return Card(
                    margin: const EdgeInsets.only(bottom: 12),
                    child: ListTile(
                      contentPadding: const EdgeInsets.all(16),
                      leading: Container(
                        width: 50,
                        height: 50,
                        decoration: BoxDecoration(
                          color: Colors.blue[100],
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(Icons.grade, color: Colors.blue),
                      ),
                      title: Text(
                        jadwal['mata_kuliah'] ?? '-',
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 8),
                          if (jadwal['kode_mk'] != null)
                            Row(
                              children: [
                                const Icon(Icons.code, size: 16),
                                const SizedBox(width: 4),
                                Text('${jadwal['kode_mk'] ?? '-'}'),
                              ],
                            ),
                          const SizedBox(height: 4),
                          if (jadwal['hari'] != null &&
                              jadwal['jam_mulai'] != null)
                            Row(
                              children: [
                                const Icon(Icons.schedule, size: 16),
                                const SizedBox(width: 4),
                                Text(
                                  '${jadwal['hari'] ?? '-'}, ${jadwal['jam_mulai'] ?? '-'} - ${jadwal['jam_selesai'] ?? '-'}',
                                ),
                              ],
                            ),
                          const SizedBox(height: 4),
                          if (jadwal['ruangan'] != null)
                            Row(
                              children: [
                                const Icon(Icons.location_on, size: 16),
                                const SizedBox(width: 4),
                                Text('${jadwal['ruangan'] ?? '-'}'),
                              ],
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
                                  color: totalNilai == totalMahasiswa
                                      ? Colors.green[100]
                                      : Colors.orange[100],
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: Text(
                                  '$totalNilai/$totalMahasiswa',
                                  style: TextStyle(
                                    fontSize: 10,
                                    fontWeight: FontWeight.bold,
                                    color: totalNilai == totalMahasiswa
                                        ? Colors.green[900]
                                        : Colors.orange[900],
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Text(
                                'Nilai Terinput',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                      trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                      onTap: () async {
                        final result = await context.push(
                          '/dosen/nilai/input/${jadwal['id']}',
                        );
                        if (result == true) {
                          _loadJadwals();
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
