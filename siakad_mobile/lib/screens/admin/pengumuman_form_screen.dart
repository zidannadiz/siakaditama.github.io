import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class PengumumanFormScreen extends StatefulWidget {
  final int? pengumumanId;

  const PengumumanFormScreen({Key? key, this.pengumumanId}) : super(key: key);

  @override
  State<PengumumanFormScreen> createState() => _PengumumanFormScreenState();
}

class _PengumumanFormScreenState extends State<PengumumanFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _judulController = TextEditingController();
  final _isiController = TextEditingController();

  String? selectedTarget;
  bool isPinned = false;
  DateTime? publishedAt;
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    if (widget.pengumumanId != null) {
      _loadPengumuman();
    } else {
      isLoading = false;
      publishedAt = DateTime.now();
    }
  }

  @override
  void dispose() {
    _judulController.dispose();
    _isiController.dispose();
    super.dispose();
  }

  Future<void> _loadPengumuman() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/admin/pengumuman/${widget.pengumumanId}',
      );
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _judulController.text = data['judul'] ?? '';
          _isiController.text = data['isi'] ?? '';
          selectedTarget = data['target'];
          isPinned = data['is_pinned'] ?? false;
          if (data['published_at'] != null) {
            publishedAt = DateTime.parse(data['published_at']);
          }
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data pengumuman';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _selectDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: publishedAt ?? DateTime.now(),
      firstDate: DateTime(2000),
      lastDate: DateTime(2100),
    );

    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.fromDateTime(publishedAt ?? DateTime.now()),
      );

      if (time != null) {
        setState(() {
          publishedAt = DateTime(
            picked.year,
            picked.month,
            picked.day,
            time.hour,
            time.minute,
          );
        });
      }
    }
  }

  Future<void> _savePengumuman() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'judul': _judulController.text.trim(),
        'isi': _isiController.text.trim(),
        'target': selectedTarget,
        'is_pinned': isPinned,
        'published_at': publishedAt?.toIso8601String(),
      };

      final result = widget.pengumumanId == null
          ? await ApiService.post('/admin/pengumuman', data)
          : await ApiService.put(
              '/admin/pengumuman/${widget.pengumumanId}',
              data,
            );

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.pengumumanId == null
                        ? 'Pengumuman berhasil ditambahkan'
                        : 'Pengumuman berhasil diperbarui'),
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
              content: Text(result['message'] ?? 'Gagal menyimpan pengumuman'),
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
          widget.pengumumanId == null ? 'Tambah Pengumuman' : 'Edit Pengumuman',
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
                      controller: _judulController,
                      decoration: const InputDecoration(
                        labelText: 'Judul *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.title),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Judul harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _isiController,
                      decoration: const InputDecoration(
                        labelText: 'Isi Pengumuman *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.description),
                        alignLabelWithHint: true,
                      ),
                      maxLines: 8,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Isi pengumuman harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      value: selectedTarget,
                      decoration: const InputDecoration(
                        labelText: 'Target *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.people),
                      ),
                      items: const [
                        DropdownMenuItem(value: 'semua', child: Text('Semua')),
                        DropdownMenuItem(
                          value: 'mahasiswa',
                          child: Text('Mahasiswa'),
                        ),
                        DropdownMenuItem(value: 'dosen', child: Text('Dosen')),
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedTarget = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Target harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    SwitchListTile(
                      title: const Text('Pin Pengumuman'),
                      subtitle: const Text(
                        'Pengumuman yang di-pin akan muncul di atas',
                      ),
                      value: isPinned,
                      onChanged: (value) {
                        setState(() {
                          isPinned = value;
                        });
                      },
                      secondary: Icon(
                        isPinned ? Icons.push_pin : Icons.push_pin_outlined,
                        color: isPinned ? Colors.orange : Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 16),
                    InkWell(
                      onTap: () => _selectDate(context),
                      child: InputDecorator(
                        decoration: const InputDecoration(
                          labelText: 'Tanggal Publikasi',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.calendar_today),
                          helperText:
                              'Opsional, kosongkan untuk publikasi sekarang',
                        ),
                        child: Text(
                          publishedAt != null
                              ? DateFormat(
                                  'dd MMMM yyyy, HH:mm',
                                  'id_ID',
                                ).format(publishedAt!)
                              : 'Pilih tanggal (opsional)',
                          style: TextStyle(
                            color: publishedAt != null
                                ? Colors.black87
                                : Colors.grey[600],
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton(
                      onPressed: isSaving ? null : _savePengumuman,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.orange,
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
