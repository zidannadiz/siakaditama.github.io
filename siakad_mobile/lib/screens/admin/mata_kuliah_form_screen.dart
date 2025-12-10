import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class MataKuliahFormScreen extends StatefulWidget {
  final int? mataKuliahId;

  const MataKuliahFormScreen({Key? key, this.mataKuliahId}) : super(key: key);

  @override
  State<MataKuliahFormScreen> createState() => _MataKuliahFormScreenState();
}

class _MataKuliahFormScreenState extends State<MataKuliahFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _kodeMkController = TextEditingController();
  final _namaController = TextEditingController();
  final _sksController = TextEditingController();
  final _semesterController = TextEditingController();
  final _deskripsiController = TextEditingController();

  List<dynamic> prodis = [];
  int? selectedProdiId;
  String? selectedJenis;
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadProdis();
    if (widget.mataKuliahId != null) {
      _loadMataKuliah();
    } else {
      isLoading = false;
    }
  }

  @override
  void dispose() {
    _kodeMkController.dispose();
    _namaController.dispose();
    _sksController.dispose();
    _semesterController.dispose();
    _deskripsiController.dispose();
    super.dispose();
  }

  Future<void> _loadProdis() async {
    try {
      final result = await ApiService.get('/admin/prodi');
      if (result['success'] == true) {
        setState(() {
          prodis = result['data'] ?? [];
        });
      }
    } catch (e) {
      // Ignore error
    }
  }

  Future<void> _loadMataKuliah() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result =
          await ApiService.get('/admin/mata-kuliah/${widget.mataKuliahId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _kodeMkController.text = data['kode_mk'] ?? '';
          _namaController.text = data['nama'] ?? '';
          _sksController.text = data['sks']?.toString() ?? '';
          _semesterController.text = data['semester']?.toString() ?? '';
          _deskripsiController.text = data['deskripsi'] ?? '';
          selectedProdiId = data['prodi_id'];
          selectedJenis = data['jenis'] ?? 'wajib';
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data mata kuliah';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _saveMataKuliah() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'kode_mk': _kodeMkController.text.trim(),
        'nama': _namaController.text.trim(),
        'sks': int.tryParse(_sksController.text.trim()) ?? 1,
        'prodi_id': selectedProdiId,
        'semester': _semesterController.text.trim().isNotEmpty
            ? int.tryParse(_semesterController.text.trim())
            : null,
        'deskripsi': _deskripsiController.text.trim().isNotEmpty
            ? _deskripsiController.text.trim()
            : null,
        'jenis': selectedJenis ?? 'wajib',
      };

      final result = widget.mataKuliahId == null
          ? await ApiService.post('/admin/mata-kuliah', data)
          : await ApiService.put(
              '/admin/mata-kuliah/${widget.mataKuliahId}', data);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.mataKuliahId == null
                        ? 'Mata kuliah berhasil ditambahkan'
                        : 'Mata kuliah berhasil diperbarui'),
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
              content: Text(result['message'] ?? 'Gagal menyimpan mata kuliah'),
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
          widget.mataKuliahId == null ? 'Tambah Mata Kuliah' : 'Edit Mata Kuliah',
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
                          controller: _kodeMkController,
                          decoration: const InputDecoration(
                            labelText: 'Kode Mata Kuliah *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.code),
                            helperText: 'Maksimal 20 karakter',
                          ),
                          maxLength: 20,
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Kode mata kuliah harus diisi';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _namaController,
                          decoration: const InputDecoration(
                            labelText: 'Nama Mata Kuliah *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.menu_book),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Nama mata kuliah harus diisi';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        DropdownButtonFormField<int>(
                          value: selectedProdiId,
                          decoration: const InputDecoration(
                            labelText: 'Program Studi *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.school),
                          ),
                          items: prodis.map((prodi) {
                            return DropdownMenuItem<int>(
                              value: prodi['id'],
                              child: Text('${prodi['kode']} - ${prodi['nama']}'),
                            );
                          }).toList(),
                          onChanged: (value) {
                            setState(() {
                              selectedProdiId = value;
                            });
                          },
                          validator: (value) {
                            if (value == null) {
                              return 'Program Studi harus dipilih';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _sksController,
                          decoration: const InputDecoration(
                            labelText: 'SKS *',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.credit_card),
                            helperText: 'Minimal 1',
                          ),
                          keyboardType: TextInputType.number,
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'SKS harus diisi';
                            }
                            final sks = int.tryParse(value);
                            if (sks == null || sks < 1) {
                              return 'SKS minimal 1';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _semesterController,
                          decoration: const InputDecoration(
                            labelText: 'Semester',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.numbers),
                            helperText: '1-14 (opsional)',
                          ),
                          keyboardType: TextInputType.number,
                          validator: (value) {
                            if (value != null && value.isNotEmpty) {
                              final semester = int.tryParse(value);
                              if (semester == null || semester < 1 || semester > 14) {
                                return 'Semester harus antara 1-14';
                              }
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        DropdownButtonFormField<String>(
                          value: selectedJenis,
                          decoration: const InputDecoration(
                            labelText: 'Jenis',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.category),
                          ),
                          items: const [
                            DropdownMenuItem(
                                value: 'wajib', child: Text('Wajib')),
                            DropdownMenuItem(
                                value: 'pilihan', child: Text('Pilihan')),
                          ],
                          onChanged: (value) {
                            setState(() {
                              selectedJenis = value;
                            });
                          },
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _deskripsiController,
                          decoration: const InputDecoration(
                            labelText: 'Deskripsi',
                            border: OutlineInputBorder(),
                            prefixIcon: Icon(Icons.description),
                            helperText: 'Opsional',
                          ),
                          maxLines: 3,
                        ),
                        const SizedBox(height: 24),
                        ElevatedButton(
                          onPressed: isSaving ? null : _saveMataKuliah,
                          style: ElevatedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 16),
                            backgroundColor: Colors.deepPurple,
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

