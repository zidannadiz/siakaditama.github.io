import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class SemesterFormScreen extends StatefulWidget {
  final int? semesterId;

  const SemesterFormScreen({Key? key, this.semesterId}) : super(key: key);

  @override
  State<SemesterFormScreen> createState() => _SemesterFormScreenState();
}

class _SemesterFormScreenState extends State<SemesterFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _namaController = TextEditingController();
  final _tahunAjaranController = TextEditingController();

  String? selectedJenis;
  String? selectedStatus;
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    if (widget.semesterId != null) {
      _loadSemester();
    } else {
      isLoading = false;
    }
  }

  @override
  void dispose() {
    _namaController.dispose();
    _tahunAjaranController.dispose();
    super.dispose();
  }

  Future<void> _loadSemester() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result =
          await ApiService.get('/admin/semester/${widget.semesterId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _namaController.text = data['nama'] ?? '';
          _tahunAjaranController.text = data['tahun_ajaran'] ?? '';
          selectedJenis = data['jenis'];
          selectedStatus = data['status'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data semester';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _saveSemester() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'nama': _namaController.text.trim(),
        'tahun_ajaran': _tahunAjaranController.text.trim(),
        'jenis': selectedJenis,
        'status': selectedStatus,
      };

      final result = widget.semesterId == null
          ? await ApiService.post('/admin/semester', data)
          : await ApiService.put('/admin/semester/${widget.semesterId}', data);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.semesterId == null
                        ? 'Semester berhasil ditambahkan'
                        : 'Semester berhasil diperbarui'),
              ),
              backgroundColor: Colors.green,
            ),
          );
          context.pop(true);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menyimpan semester'),
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
    } finally {
      if (mounted) {
        setState(() {
          isSaving = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          widget.semesterId == null ? 'Tambah Semester' : 'Edit Semester',
        ),
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
                        onPressed: () => context.pop(),
                        child: const Text('Kembali'),
                      ),
                    ],
                  ),
                )
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        TextFormField(
                          controller: _namaController,
                          decoration: const InputDecoration(
                            labelText: 'Nama Semester *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.calendar_today),
                            helperText: 'Contoh: Semester Ganjil 2024/2025',
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Nama semester harus diisi';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _tahunAjaranController,
                          decoration: const InputDecoration(
                            labelText: 'Tahun Ajaran *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.date_range),
                            helperText: 'Contoh: 2024/2025',
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Tahun ajaran harus diisi';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        DropdownButtonFormField<String>(
                          value: selectedJenis,
                          decoration: const InputDecoration(
                            labelText: 'Jenis *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.category),
                          ),
                          items: const [
                            DropdownMenuItem(
                                value: 'ganjil', child: Text('Ganjil')),
                            DropdownMenuItem(
                                value: 'genap', child: Text('Genap')),
                          ],
                          onChanged: (value) {
                            setState(() {
                              selectedJenis = value;
                            });
                          },
                          validator: (value) {
                            if (value == null) {
                              return 'Jenis harus dipilih';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        DropdownButtonFormField<String>(
                          value: selectedStatus,
                          decoration: const InputDecoration(
                            labelText: 'Status *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.info),
                            helperText: 'Hanya satu semester yang bisa aktif',
                          ),
                          items: const [
                            DropdownMenuItem(
                                value: 'aktif', child: Text('Aktif')),
                            DropdownMenuItem(
                                value: 'nonaktif', child: Text('Nonaktif')),
                          ],
                          onChanged: (value) {
                            setState(() {
                              selectedStatus = value;
                            });
                          },
                          validator: (value) {
                            if (value == null) {
                              return 'Status harus dipilih';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 24),
                        ElevatedButton(
                          onPressed: isSaving ? null : _saveSemester,
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            backgroundColor: Colors.amber,
                          ),
                          child: isSaving
                              ? const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    valueColor: AlwaysStoppedAnimation<Color>(
                                      Colors.white,
                                    ),
                                  ),
                                )
                              : const Text(
                                  'Simpan',
                                  style: TextStyle(fontSize: 16),
                                ),
                        ),
                      ],
                    ),
                  ),
                ),
    );
  }
}

