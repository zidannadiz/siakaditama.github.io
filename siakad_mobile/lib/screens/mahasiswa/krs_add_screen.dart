import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class KRSAddScreen extends StatefulWidget {
  const KRSAddScreen({Key? key}) : super(key: key);

  @override
  State<KRSAddScreen> createState() => _KRSAddScreenState();
}

class _KRSAddScreenState extends State<KRSAddScreen> {
  List<dynamic> availableCourses = [];
  bool isLoading = true;
  bool isSubmitting = false;
  String? errorMessage;
  String? searchQuery;

  @override
  void initState() {
    super.initState();
    _loadAvailableCourses();
  }

  Future<void> _loadAvailableCourses() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    final result = await ApiService.get('/mahasiswa/krs/create');
    if (result['success'] == true) {
      setState(() {
        availableCourses = result['data']['available_courses'] ?? [];
        isLoading = false;
      });
    } else {
      setState(() {
        isLoading = false;
        errorMessage = result['message'] ?? 'Gagal memuat data mata kuliah';
      });
    }
  }

  Future<void> _addKRS(int jadwalKuliahId) async {
    setState(() {
      isSubmitting = true;
    });

    final result = await ApiService.post('/mahasiswa/krs', {
      'jadwal_kuliah_id': jadwalKuliahId,
    });

    if (result['success'] == true) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('KRS berhasil ditambahkan. Menunggu persetujuan.'),
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
            content: Text(result['message'] ?? 'Gagal menambahkan KRS'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  List<dynamic> get filteredCourses {
    if (searchQuery == null || searchQuery!.isEmpty) {
      return availableCourses;
    }
    final query = searchQuery!.toLowerCase();
    return availableCourses.where((course) {
      final mataKuliah = (course['mata_kuliah'] ?? '').toLowerCase();
      final kode = (course['kode'] ?? '').toLowerCase();
      final dosen = (course['dosen'] ?? '').toLowerCase();
      return mataKuliah.contains(query) ||
          kode.contains(query) ||
          dosen.contains(query);
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tambah KRS'),
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator())
          : errorMessage != null
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
                        onPressed: _loadAvailableCourses,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : Column(
                  children: [
                    // Search Bar
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: TextField(
                        decoration: InputDecoration(
                          hintText: 'Cari mata kuliah, kode, atau dosen...',
                          prefixIcon: const Icon(Icons.search),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          filled: true,
                          fillColor: Colors.grey[100],
                        ),
                        onChanged: (value) {
                          setState(() {
                            searchQuery = value;
                          });
                        },
                      ),
                    ),

                    // Courses List
                    Expanded(
                      child: filteredCourses.isEmpty
                          ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.search_off,
                                      size: 64, color: Colors.grey[400]),
                                  const SizedBox(height: 16),
                                  Text(
                                    searchQuery != null &&
                                            searchQuery!.isNotEmpty
                                        ? 'Tidak ada mata kuliah yang ditemukan'
                                        : 'Tidak ada mata kuliah tersedia',
                                    style: TextStyle(color: Colors.grey[600]),
                                    textAlign: TextAlign.center,
                                  ),
                                ],
                              ),
                            )
                          : RefreshIndicator(
                              onRefresh: _loadAvailableCourses,
                              child: ListView.builder(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                ),
                                itemCount: filteredCourses.length,
                                itemBuilder: (context, index) {
                                  final course = filteredCourses[index];
                                  final isFull = (course['terisi'] ?? 0) >=
                                      (course['kapasitas'] ?? 0);

                                  return Card(
                                    margin: const EdgeInsets.only(bottom: 12),
                                    color: isFull
                                        ? Colors.grey[100]
                                        : null,
                                    child: ListTile(
                                      contentPadding: const EdgeInsets.all(16),
                                      leading: Container(
                                        width: 50,
                                        height: 50,
                                        decoration: BoxDecoration(
                                          color: isFull
                                              ? Colors.grey[300]
                                              : Colors.green[100],
                                          borderRadius:
                                              BorderRadius.circular(8),
                                        ),
                                        child: Icon(
                                          Icons.book,
                                          color: isFull
                                              ? Colors.grey[600]
                                              : Colors.green,
                                        ),
                                      ),
                                      title: Text(
                                        course['mata_kuliah'] ?? '-',
                                        style: TextStyle(
                                          fontWeight: FontWeight.bold,
                                          color: isFull
                                              ? Colors.grey[600]
                                              : null,
                                        ),
                                      ),
                                      subtitle: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          const SizedBox(height: 8),
                                          if (course['kode'] != null)
                                            Row(
                                              children: [
                                                const Icon(Icons.code,
                                                    size: 16),
                                                const SizedBox(width: 4),
                                                Text(
                                                  '${course['kode']}',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    color: Colors.grey[600],
                                                  ),
                                                ),
                                              ],
                                            ),
                                          const SizedBox(height: 4),
                                          if (course['dosen'] != null)
                                            Row(
                                              children: [
                                                const Icon(Icons.person,
                                                    size: 16),
                                                const SizedBox(width: 4),
                                                Text('${course['dosen'] ?? '-'}'),
                                              ],
                                            ),
                                          const SizedBox(height: 4),
                                          if (course['hari'] != null &&
                                              course['jam_mulai'] != null)
                                            Row(
                                              children: [
                                                const Icon(Icons.schedule,
                                                    size: 16),
                                                const SizedBox(width: 4),
                                                Text(
                                                    '${course['hari'] ?? '-'}, ${course['jam_mulai'] ?? '-'} - ${course['jam_selesai'] ?? '-'}'),
                                              ],
                                            ),
                                          const SizedBox(height: 4),
                                          if (course['ruangan'] != null)
                                            Row(
                                              children: [
                                                const Icon(Icons.location_on,
                                                    size: 16),
                                                const SizedBox(width: 4),
                                                Text('${course['ruangan'] ?? '-'}'),
                                              ],
                                            ),
                                          const SizedBox(height: 8),
                                          Row(
                                            children: [
                                              if (course['sks'] != null)
                                                Container(
                                                  padding:
                                                      const EdgeInsets.symmetric(
                                                        horizontal: 8,
                                                        vertical: 4,
                                                      ),
                                                  decoration: BoxDecoration(
                                                    color: Colors.orange[100],
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                            12),
                                                  ),
                                                  child: Text(
                                                    '${course['sks']} SKS',
                                                    style: TextStyle(
                                                      fontSize: 10,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                      color: Colors.orange[900],
                                                    ),
                                                  ),
                                                ),
                                              const SizedBox(width: 8),
                                              Container(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                      horizontal: 8,
                                                      vertical: 4,
                                                    ),
                                                decoration: BoxDecoration(
                                                  color: isFull
                                                      ? Colors.red[100]
                                                      : Colors.blue[100],
                                                  borderRadius:
                                                      BorderRadius.circular(
                                                          12),
                                                ),
                                                child: Text(
                                                  '${course['terisi'] ?? 0}/${course['kapasitas'] ?? 0}',
                                                  style: TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: isFull
                                                        ? Colors.red[900]
                                                        : Colors.blue[900],
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ],
                                      ),
                                      trailing: isFull
                                          ? const Icon(
                                              Icons.block,
                                              color: Colors.red,
                                            )
                                          : isSubmitting
                                              ? const SizedBox(
                                                  width: 20,
                                                  height: 20,
                                                  child:
                                                      CircularProgressIndicator(
                                                    strokeWidth: 2,
                                                  ),
                                                )
                                              : IconButton(
                                                  icon: const Icon(
                                                    Icons.add_circle,
                                                    color: Colors.green,
                                                  ),
                                                  onPressed: () =>
                                                      _addKRS(course['id']),
                                                  tooltip: 'Tambah',
                                                ),
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
