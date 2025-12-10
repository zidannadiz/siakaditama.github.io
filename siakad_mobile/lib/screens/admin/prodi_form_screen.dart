import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class ProdiFormScreen extends StatefulWidget {
  final int? prodiId;

  const ProdiFormScreen({Key? key, this.prodiId}) : super(key: key);

  @override
  State<ProdiFormScreen> createState() => _ProdiFormScreenState();
}

class _ProdiFormScreenState extends State<ProdiFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _kodeController = TextEditingController();
  final _namaController = TextEditingController();

  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    if (widget.prodiId != null) {
      _loadProdi();
    } else {
      isLoading = false;
    }
  }

  @override
  void dispose() {
    _kodeController.dispose();
    _namaController.dispose();
    super.dispose();
  }

  Future<void> _loadProdi() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/prodi/${widget.prodiId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _kodeController.text = data['kode'] ?? '';
          _namaController.text = data['nama'] ?? '';
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data prodi';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _saveProdi() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'kode': _kodeController.text.trim(),
        'nama': _namaController.text.trim(),
      };

      final result = widget.prodiId == null
          ? await ApiService.post('/admin/prodi', data)
          : await ApiService.put('/admin/prodi/${widget.prodiId}', data);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.prodiId == null
                        ? 'Prodi berhasil ditambahkan'
                        : 'Prodi berhasil diperbarui'),
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
              content: Text(result['message'] ?? 'Gagal menyimpan prodi'),
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
        title: Text(widget.prodiId == null ? 'Tambah Prodi' : 'Edit Prodi'),
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
                      controller: _kodeController,
                      decoration: const InputDecoration(
                        labelText: 'Kode Prodi *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.code),
                        helperText: 'Maksimal 10 karakter',
                      ),
                      maxLength: 10,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Kode prodi harus diisi';
                        }
                        if (value.length > 10) {
                          return 'Kode prodi maksimal 10 karakter';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _namaController,
                      decoration: const InputDecoration(
                        labelText: 'Nama Prodi *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.school),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Nama prodi harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton(
                      onPressed: isSaving ? null : _saveProdi,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.indigo,
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
