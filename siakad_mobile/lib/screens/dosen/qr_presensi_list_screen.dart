import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class QrPresensiListScreen extends StatefulWidget {
  const QrPresensiListScreen({Key? key}) : super(key: key);

  @override
  State<QrPresensiListScreen> createState() => _QrPresensiListScreenState();
}

class _QrPresensiListScreenState extends State<QrPresensiListScreen> {
  List<dynamic> jadwals = [];
  Map<String, dynamic>? selectedJadwal;
  Map<String, dynamic>? qrSession;
  int pertemuanTerakhir = 0;
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
      String url = '/dosen/qr-presensi';
      if (jadwalId != null) {
        url += '?jadwal_id=$jadwalId';
      }

      final result = await ApiService.get(url);
      if (result['success'] == true) {
        setState(() {
          jadwals = result['data']['jadwals'] ?? [];
          selectedJadwal = result['data']['selected_jadwal'];
          qrSession = result['data']['qr_session'];
          pertemuanTerakhir = result['data']['pertemuan_terakhir'] ?? 0;
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('QR Code Presensi'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(jadwalId: selectedJadwal?['id']),
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
                    onPressed: () => _loadData(jadwalId: selectedJadwal?['id']),
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
                      DropdownButtonFormField<int>(
                        value: selectedJadwal?['id'],
                        decoration: const InputDecoration(
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.schedule),
                        ),
                        items: jadwals.map((jadwal) {
                          return DropdownMenuItem<int>(
                            value: jadwal['id'],
                            child: Text(
                              '${jadwal['mata_kuliah']['kode']} - ${jadwal['mata_kuliah']['nama']}',
                              overflow: TextOverflow.ellipsis,
                            ),
                          );
                        }).toList(),
                        onChanged: (value) {
                          if (value != null) {
                            _loadData(jadwalId: value);
                          }
                        },
                      ),
                    ],
                  ),
                ),

                // Content
                Expanded(
                  child: selectedJadwal == null
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.qr_code_scanner,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Pilih jadwal kuliah untuk memulai',
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
                              _loadData(jadwalId: selectedJadwal!['id']),
                          child: SingleChildScrollView(
                            padding: const EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Jadwal Info Card
                                Card(
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          selectedJadwal!['mata_kuliah']['nama'],
                                          style: const TextStyle(
                                            fontSize: 18,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        _buildInfoRow(
                                          Icons.code,
                                          'Kode',
                                          selectedJadwal!['mata_kuliah']['kode'],
                                        ),
                                        const SizedBox(height: 4),
                                        _buildInfoRow(
                                          Icons.calendar_today,
                                          'Hari',
                                          selectedJadwal!['hari'],
                                        ),
                                        const SizedBox(height: 4),
                                        _buildInfoRow(
                                          Icons.access_time,
                                          'Waktu',
                                          '${selectedJadwal!['jam_mulai']} - ${selectedJadwal!['jam_selesai']}',
                                        ),
                                        const SizedBox(height: 4),
                                        _buildInfoRow(
                                          Icons.room,
                                          'Ruangan',
                                          selectedJadwal!['ruangan'] ?? '-',
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 16),

                                // QR Session Status
                                if (qrSession != null) ...[
                                  Card(
                                    color: qrSession!['is_valid']
                                        ? Colors.green[50]
                                        : Colors.red[50],
                                    child: Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            children: [
                                              Icon(
                                                qrSession!['is_valid']
                                                    ? Icons.check_circle
                                                    : Icons.cancel,
                                                color: qrSession!['is_valid']
                                                    ? Colors.green
                                                    : Colors.red,
                                              ),
                                              const SizedBox(width: 8),
                                              Text(
                                                qrSession!['is_valid']
                                                    ? 'QR Code Aktif'
                                                    : 'QR Code Tidak Aktif',
                                                style: TextStyle(
                                                  fontSize: 16,
                                                  fontWeight: FontWeight.bold,
                                                  color: qrSession!['is_valid']
                                                      ? Colors.green[700]
                                                      : Colors.red[700],
                                                ),
                                              ),
                                            ],
                                          ),
                                          const SizedBox(height: 8),
                                          _buildInfoRow(
                                            Icons.numbers,
                                            'Pertemuan',
                                            '${qrSession!['pertemuan']}',
                                          ),
                                          const SizedBox(height: 4),
                                          _buildInfoRow(
                                            Icons.calendar_today,
                                            'Tanggal',
                                            qrSession!['tanggal'],
                                          ),
                                          const SizedBox(height: 4),
                                          _buildInfoRow(
                                            Icons.timer,
                                            'Berlaku hingga',
                                            _formatExpiresAt(
                                              qrSession!['expires_at'],
                                            ),
                                          ),
                                          const SizedBox(height: 16),
                                          Row(
                                            children: [
                                              Expanded(
                                                child: ElevatedButton.icon(
                                                  onPressed: () {
                                                    context.push(
                                                      '/dosen/qr-presensi/${qrSession!['token']}',
                                                    );
                                                  },
                                                  icon: const Icon(
                                                    Icons.qr_code,
                                                  ),
                                                  label: const Text(
                                                    'Lihat QR Code',
                                                  ),
                                                  style:
                                                      ElevatedButton.styleFrom(
                                                        backgroundColor:
                                                            Colors.blue,
                                                      ),
                                                ),
                                              ),
                                              const SizedBox(width: 8),
                                              Expanded(
                                                child: ElevatedButton.icon(
                                                  onPressed: () =>
                                                      _stopQrSession(
                                                        qrSession!['token'],
                                                      ),
                                                  icon: const Icon(Icons.stop),
                                                  label: const Text('Stop'),
                                                  style:
                                                      ElevatedButton.styleFrom(
                                                        backgroundColor:
                                                            Colors.red,
                                                      ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  const SizedBox(height: 16),
                                ],

                                // Generate QR Button
                                ElevatedButton.icon(
                                  onPressed: () {
                                    context.push(
                                      '/dosen/qr-presensi/generate/${selectedJadwal!['id']}',
                                    );
                                  },
                                  icon: const Icon(Icons.qr_code_2),
                                  label: Text(
                                    qrSession != null
                                        ? 'Generate QR Code Baru'
                                        : 'Generate QR Code',
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    padding: const EdgeInsets.symmetric(
                                      vertical: 16,
                                    ),
                                    backgroundColor: Colors.green,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                ),
              ],
            ),
    );
  }

  Future<void> _stopQrSession(String token) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Stop QR Code'),
        content: const Text(
          'Apakah Anda yakin ingin menghentikan QR Code ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Stop'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final result = await ApiService.post(
        '/dosen/qr-presensi/$token/stop',
        {},
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('QR Code telah dihentikan'),
              backgroundColor: Colors.orange,
            ),
          );
          _loadData(jadwalId: selectedJadwal?['id']);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghentikan QR Code'),
              backgroundColor: Colors.red,
            ),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: ${e.toString()}'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  String _formatExpiresAt(String? expiresAt) {
    if (expiresAt == null) return '-';
    try {
      final date = DateTime.parse(expiresAt);
      return '${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')}';
    } catch (e) {
      return expiresAt;
    }
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: TextStyle(fontSize: 14, color: Colors.grey[700]),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
          ),
        ),
      ],
    );
  }
}
