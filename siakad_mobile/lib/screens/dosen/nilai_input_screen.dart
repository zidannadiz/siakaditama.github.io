import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class NilaiInputScreen extends StatefulWidget {
  final int jadwalId;

  const NilaiInputScreen({Key? key, required this.jadwalId}) : super(key: key);

  @override
  State<NilaiInputScreen> createState() => _NilaiInputScreenState();
}

class _NilaiInputScreenState extends State<NilaiInputScreen> {
  Map<String, dynamic>? jadwalData;
  List<dynamic> mahasiswaList = [];
  Map<int, TextEditingController> tugasControllers = {};
  Map<int, TextEditingController> utsControllers = {};
  Map<int, TextEditingController> uasControllers = {};
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
    for (var controller in tugasControllers.values) {
      controller.dispose();
    }
    for (var controller in utsControllers.values) {
      controller.dispose();
    }
    for (var controller in uasControllers.values) {
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
      '/dosen/nilai/create/${widget.jadwalId}',
    );
    if (result['success'] == true) {
      setState(() {
        jadwalData = result['data']['jadwal'];
        mahasiswaList = result['data']['krs_list'] ?? [];

        // Initialize controllers with existing nilai if any
        for (var krs in mahasiswaList) {
          final krsId = krs['id'];
          tugasControllers[krsId] = TextEditingController(
            text: krs['nilai']?['nilai_tugas']?.toString() ?? '',
          );
          utsControllers[krsId] = TextEditingController(
            text: krs['nilai']?['nilai_uts']?.toString() ?? '',
          );
          uasControllers[krsId] = TextEditingController(
            text: krs['nilai']?['nilai_uas']?.toString() ?? '',
          );
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

  Future<void> _submitNilai() async {
    if (mahasiswaList.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tidak ada mahasiswa untuk diinput nilainya'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    setState(() {
      isSubmitting = true;
    });

    final krsIds = <int>[];
    final nilaiTugas = <double?>[];
    final nilaiUts = <double?>[];
    final nilaiUas = <double?>[];

    for (var krs in mahasiswaList) {
      final krsId = krs['id'];
      krsIds.add(krsId);

      final tugasText = tugasControllers[krsId]?.text.trim() ?? '';
      final utsText = utsControllers[krsId]?.text.trim() ?? '';
      final uasText = uasControllers[krsId]?.text.trim() ?? '';

      nilaiTugas.add(tugasText.isEmpty ? null : double.tryParse(tugasText));
      nilaiUts.add(utsText.isEmpty ? null : double.tryParse(utsText));
      nilaiUas.add(uasText.isEmpty ? null : double.tryParse(uasText));
    }

    final result = await ApiService.post('/dosen/nilai/${widget.jadwalId}', {
      'krs_id': krsIds,
      'nilai_tugas': nilaiTugas,
      'nilai_uts': nilaiUts,
      'nilai_uas': nilaiUas,
    });

    if (result['success'] == true) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Nilai berhasil disimpan'),
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
            content: Text(result['message'] ?? 'Gagal menyimpan nilai'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Input Nilai')),
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
                // Jadwal Info Card
                if (jadwalData != null)
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: Colors.blue[50],
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          jadwalData!['mata_kuliah'] ?? '-',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        if (jadwalData!['kode_mk'] != null)
                          Text(
                            'Kode: ${jadwalData!['kode_mk']}',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                        if (jadwalData!['hari'] != null)
                          Text(
                            '${jadwalData!['hari'] ?? '-'}, ${jadwalData!['jam_mulai'] ?? '-'} - ${jadwalData!['jam_selesai'] ?? '-'}',
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                      ],
                    ),
                  ),

                // Mahasiswa List with Nilai Input
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
                            final krsId = krs['id'];
                            final mahasiswa = krs['mahasiswa'] ?? {};

                            return Card(
                              margin: const EdgeInsets.only(bottom: 16),
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
                                          backgroundColor: Colors.blue[100],
                                          child: Text(
                                            (mahasiswa['nama'] ?? 'M')[0]
                                                .toUpperCase(),
                                            style: TextStyle(
                                              color: Colors.blue[700],
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
                                    const Divider(),
                                    const SizedBox(height: 8),

                                    // Nilai Input Fields
                                    Row(
                                      children: [
                                        Expanded(
                                          child: _NilaiInputField(
                                            label: 'Tugas (30%)',
                                            controller:
                                                tugasControllers[krsId]!,
                                            icon: Icons.assignment,
                                            color: Colors.blue,
                                          ),
                                        ),
                                        const SizedBox(width: 8),
                                        Expanded(
                                          child: _NilaiInputField(
                                            label: 'UTS (30%)',
                                            controller: utsControllers[krsId]!,
                                            icon: Icons.quiz,
                                            color: Colors.orange,
                                          ),
                                        ),
                                        const SizedBox(width: 8),
                                        Expanded(
                                          child: _NilaiInputField(
                                            label: 'UAS (40%)',
                                            controller: uasControllers[krsId]!,
                                            icon: Icons.school,
                                            color: Colors.green,
                                          ),
                                        ),
                                      ],
                                    ),

                                    // Nilai Akhir Preview (if all filled)
                                    if (tugasControllers[krsId]!
                                            .text
                                            .isNotEmpty &&
                                        utsControllers[krsId]!
                                            .text
                                            .isNotEmpty &&
                                        uasControllers[krsId]!.text.isNotEmpty)
                                      Container(
                                        margin: const EdgeInsets.only(top: 8),
                                        padding: const EdgeInsets.all(8),
                                        decoration: BoxDecoration(
                                          color: Colors.green[50],
                                          borderRadius: BorderRadius.circular(
                                            8,
                                          ),
                                        ),
                                        child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            const Text(
                                              'Nilai Akhir: ',
                                              style: TextStyle(
                                                fontSize: 12,
                                                fontWeight: FontWeight.bold,
                                              ),
                                            ),
                                            Text(
                                              _calculateNilaiAkhir(
                                                tugasControllers[krsId]!.text,
                                                utsControllers[krsId]!.text,
                                                uasControllers[krsId]!.text,
                                              ),
                                              style: TextStyle(
                                                fontSize: 14,
                                                fontWeight: FontWeight.bold,
                                                color: Colors.green[700],
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
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
                      onPressed: isSubmitting ? null : _submitNilai,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.blue,
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
                              'Simpan Nilai',
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

  String _calculateNilaiAkhir(String tugas, String uts, String uas) {
    final tugasValue = double.tryParse(tugas);
    final utsValue = double.tryParse(uts);
    final uasValue = double.tryParse(uas);

    if (tugasValue == null || utsValue == null || uasValue == null) {
      return '-';
    }

    final nilaiAkhir = (tugasValue * 0.3) + (utsValue * 0.3) + (uasValue * 0.4);
    return nilaiAkhir.toStringAsFixed(1);
  }
}

class _NilaiInputField extends StatelessWidget {
  final String label;
  final TextEditingController controller;
  final IconData icon;
  final Color color;

  const _NilaiInputField({
    required this.label,
    required this.controller,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 16, color: color),
            const SizedBox(width: 4),
            Text(
              label,
              style: TextStyle(
                fontSize: 10,
                color: Colors.grey[600],
                fontWeight: FontWeight.bold,
              ),
            ),
          ],
        ),
        const SizedBox(height: 4),
        TextField(
          controller: controller,
          keyboardType: TextInputType.numberWithOptions(decimal: true),
          decoration: InputDecoration(
            hintText: '0-100',
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 12,
              vertical: 8,
            ),
          ),
          style: const TextStyle(fontSize: 14),
        ),
      ],
    );
  }
}
