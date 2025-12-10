import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';
import 'package:intl/intl.dart';

class PresensiInputScreen extends StatefulWidget {
  final int jadwalId;

  const PresensiInputScreen({Key? key, required this.jadwalId})
    : super(key: key);

  @override
  State<PresensiInputScreen> createState() => _PresensiInputScreenState();
}

class _PresensiInputScreenState extends State<PresensiInputScreen> {
  Map<String, dynamic>? jadwalData;
  List<dynamic> mahasiswaList = [];
  Map<int, String> presensiStatus = {}; // mahasiswa_id -> status
  Map<int, TextEditingController> catatanControllers = {};
  int pertemuan = 1;
  DateTime selectedDate = DateTime.now();
  int? pertemuanTerakhir;
  bool isLoading = true;
  bool isSubmitting = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadFormData();
  }

  @override
  void dispose() {
    for (var controller in catatanControllers.values) {
      controller.dispose();
    }
    super.dispose();
  }

  Future<void> _loadFormData() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    final result = await ApiService.get(
      '/dosen/presensi/create/${widget.jadwalId}',
    );
    if (result['success'] == true) {
      setState(() {
        jadwalData = result['data']['jadwal'];
        mahasiswaList = result['data']['krs_list'] ?? [];
        pertemuanTerakhir = result['data']['pertemuan_terakhir'] ?? 0;
        pertemuan = pertemuanTerakhir! + 1;

        // Initialize presensi status to 'hadir' by default
        for (var krs in mahasiswaList) {
          final mahasiswaId = krs['mahasiswa']?['id'];
          if (mahasiswaId != null) {
            presensiStatus[mahasiswaId] = 'hadir';
            catatanControllers[mahasiswaId] = TextEditingController();
          }
        }
        isLoading = false;
      });
    } else {
      setState(() {
        isLoading = false;
        errorMessage = result['message'] ?? 'Gagal memuat data';
      });
    }
  }

  Future<void> _selectDate() async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: selectedDate,
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
    );
    if (picked != null && picked != selectedDate) {
      setState(() {
        selectedDate = picked;
      });
    }
  }

  Future<void> _submitPresensi() async {
    if (mahasiswaList.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tidak ada mahasiswa untuk diinput presensinya'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    setState(() {
      isSubmitting = true;
    });

    final presensiData = <Map<String, dynamic>>[];
    for (var krs in mahasiswaList) {
      final mahasiswaId = krs['mahasiswa']?['id'];
      if (mahasiswaId != null) {
        presensiData.add({
          'mahasiswa_id': mahasiswaId,
          'status': presensiStatus[mahasiswaId] ?? 'hadir',
          'catatan': catatanControllers[mahasiswaId]?.text.trim(),
        });
      }
    }

    final result = await ApiService.post('/dosen/presensi/${widget.jadwalId}', {
      'pertemuan': pertemuan,
      'tanggal': DateFormat('yyyy-MM-dd').format(selectedDate),
      'presensi': presensiData,
    });

    if (result['success'] == true) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Presensi berhasil disimpan'),
            backgroundColor: Colors.green,
          ),
        );
        context.pop(true); // Return true to indicate success
      }
    } else {
      setState(() {
        isSubmitting = false;
      });
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? 'Gagal menyimpan presensi'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'hadir':
        return Colors.green;
      case 'izin':
        return Colors.blue;
      case 'sakit':
        return Colors.orange;
      case 'alpa':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'hadir':
        return 'Hadir';
      case 'izin':
        return 'Izin';
      case 'sakit':
        return 'Sakit';
      case 'alpa':
        return 'Alpa';
      default:
        return status;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Input Presensi')),
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
                    onPressed: _loadFormData,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Jadwal Info & Form Header
                Container(
                  padding: const EdgeInsets.all(16),
                  color: Colors.green[50],
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        jadwalData?['mata_kuliah'] ?? '-',
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      if (jadwalData?['kode_mk'] != null)
                        Text(
                          'Kode: ${jadwalData!['kode_mk']}',
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey[600],
                          ),
                        ),
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: _FormField(
                              label: 'Pertemuan Ke-',
                              value: pertemuan.toString(),
                              icon: Icons.numbers,
                              onTap: () {
                                showDialog(
                                  context: context,
                                  builder: (context) => AlertDialog(
                                    title: const Text('Pertemuan Ke-'),
                                    content: TextField(
                                      keyboardType: TextInputType.number,
                                      autofocus: true,
                                      controller: TextEditingController(
                                        text: pertemuan.toString(),
                                      ),
                                      onSubmitted: (value) {
                                        final pertemuanValue = int.tryParse(
                                          value.trim(),
                                        );
                                        if (pertemuanValue != null &&
                                            pertemuanValue > 0) {
                                          setState(() {
                                            pertemuan = pertemuanValue;
                                          });
                                          Navigator.pop(context);
                                        } else {
                                          ScaffoldMessenger.of(context)
                                              .showSnackBar(
                                            const SnackBar(
                                              content: Text(
                                                'Pertemuan harus berupa angka positif',
                                              ),
                                              backgroundColor: Colors.red,
                                            ),
                                          );
                                        }
                                      },
                                    ),
                                    actions: [
                                      TextButton(
                                        onPressed: () => Navigator.pop(context),
                                        child: const Text('Batal'),
                                      ),
                                    ],
                                  ),
                                );
                              },
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _FormField(
                              label: 'Tanggal',
                              value: DateFormat(
                                'dd/MM/yyyy',
                              ).format(selectedDate),
                              icon: Icons.calendar_today,
                              onTap: _selectDate,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                // Mahasiswa List with Presensi Input
                Expanded(
                  child: mahasiswaList.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.people_outline,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada mahasiswa',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'Belum ada mahasiswa yang terdaftar di jadwal ini',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[500],
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        )
                      : ListView.builder(
                          padding: const EdgeInsets.all(16),
                          itemCount: mahasiswaList.length,
                          itemBuilder: (context, index) {
                            final krs = mahasiswaList[index];
                            final mahasiswa = krs['mahasiswa'] ?? {};
                            final mahasiswaId = mahasiswa['id'];
                            final currentStatus =
                                presensiStatus[mahasiswaId] ?? 'hadir';

                            return Card(
                              margin: const EdgeInsets.only(bottom: 12),
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Mahasiswa Info
                                    Row(
                                      children: [
                                        CircleAvatar(
                                          radius: 20,
                                          backgroundColor: _getStatusColor(
                                            currentStatus,
                                          ).withOpacity(0.2),
                                          child: Text(
                                            (mahasiswa['nama'] ?? 'M')[0]
                                                .toUpperCase(),
                                            style: TextStyle(
                                              color: _getStatusColor(
                                                currentStatus,
                                              ),
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        ),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                mahasiswa['nama'] ?? '-',
                                                style: const TextStyle(
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                              if (mahasiswa['nim'] != null)
                                                Text(
                                                  'NIM: ${mahasiswa['nim']}',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    color: Colors.grey[600],
                                                  ),
                                                ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 12),

                                    // Status Selection
                                    Wrap(
                                      spacing: 8,
                                      runSpacing: 8,
                                      children:
                                          [
                                            'hadir',
                                            'izin',
                                            'sakit',
                                            'alpa',
                                          ].map((status) {
                                            final isSelected =
                                                currentStatus == status;
                                            return ChoiceChip(
                                              label: Text(
                                                _getStatusText(status),
                                              ),
                                              selected: isSelected,
                                              onSelected: (selected) {
                                                setState(() {
                                                  presensiStatus[mahasiswaId] =
                                                      status;
                                                });
                                              },
                                              selectedColor: _getStatusColor(
                                                status,
                                              ).withOpacity(0.2),
                                              labelStyle: TextStyle(
                                                color: isSelected
                                                    ? _getStatusColor(status)
                                                    : Colors.grey[700],
                                                fontWeight: isSelected
                                                    ? FontWeight.bold
                                                    : FontWeight.normal,
                                              ),
                                              side: BorderSide(
                                                color: isSelected
                                                    ? _getStatusColor(status)
                                                    : Colors.grey[300]!,
                                                width: isSelected ? 2 : 1,
                                              ),
                                            );
                                          }).toList(),
                                    ),

                                    // Catatan Field (if not hadir)
                                    if (currentStatus != 'hadir') ...[
                                      const SizedBox(height: 12),
                                      TextField(
                                        controller:
                                            catatanControllers[mahasiswaId] ??
                                            TextEditingController(),
                                        decoration: InputDecoration(
                                          labelText: 'Catatan',
                                          hintText:
                                              'Masukkan catatan (opsional)',
                                          border: OutlineInputBorder(
                                            borderRadius: BorderRadius.circular(
                                              8,
                                            ),
                                          ),
                                          prefixIcon: const Icon(
                                            Icons.note_outlined,
                                          ),
                                        ),
                                        maxLines: 2,
                                      ),
                                    ],
                                  ],
                                ),
                              ),
                            );
                          },
                        ),
                ),

                // Submit Button
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.grey.withOpacity(0.2),
                        spreadRadius: 1,
                        blurRadius: 5,
                        offset: const Offset(0, -3),
                      ),
                    ],
                  ),
                  child: SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: isSubmitting ? null : _submitPresensi,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.green,
                      ),
                      child: isSubmitting
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                valueColor: AlwaysStoppedAnimation<Color>(
                                  Colors.white,
                                ),
                              ),
                            )
                          : const Text(
                              'Simpan Presensi',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                    ),
                  ),
                ),
              ],
            ),
    );
  }
}

class _FormField extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final VoidCallback onTap;

  const _FormField({
    required this.label,
    required this.value,
    required this.icon,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: Colors.grey[300]!),
        ),
        child: Row(
          children: [
            Icon(icon, size: 20, color: Colors.grey[600]),
            const SizedBox(width: 8),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    label,
                    style: TextStyle(fontSize: 10, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    value,
                    style: const TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
            Icon(Icons.arrow_drop_down, color: Colors.grey[600]),
          ],
        ),
      ),
    );
  }
}
