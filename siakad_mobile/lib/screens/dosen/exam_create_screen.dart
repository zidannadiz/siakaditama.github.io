import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class ExamCreateScreen extends StatefulWidget {
  final int? jadwalId;
  final int? examId; // For edit mode

  const ExamCreateScreen({Key? key, this.jadwalId, this.examId})
      : super(key: key);

  @override
  State<ExamCreateScreen> createState() => _ExamCreateScreenState();
}

class _ExamCreateScreenState extends State<ExamCreateScreen> {
  final _formKey = GlobalKey<FormState>();
  final _judulController = TextEditingController();
  final _deskripsiController = TextEditingController();
  final _durasiController = TextEditingController();
  final _mulaiController = TextEditingController();
  final _selesaiController = TextEditingController();
  final _bobotController = TextEditingController();

  String _tipe = 'pilgan';
  String _status = 'draft';
  bool _randomSoal = false;
  bool _randomPilihan = false;
  bool _tampilkanNilai = false;
  bool _preventCopyPaste = false;
  bool _preventNewTab = false;
  bool _fullscreenMode = false;

  bool isLoading = false;
  bool isEditMode = false;

  @override
  void initState() {
    super.initState();
    isEditMode = widget.examId != null;
    if (isEditMode) {
      _loadExam();
    }
  }

  @override
  void dispose() {
    _judulController.dispose();
    _deskripsiController.dispose();
    _durasiController.dispose();
    _mulaiController.dispose();
    _selesaiController.dispose();
    _bobotController.dispose();
    super.dispose();
  }

  Future<void> _loadExam() async {
    if (widget.examId == null) return;

    setState(() {
      isLoading = true;
    });

    try {
      final result = await ApiService.get('/dosen/exam/${widget.examId}');
      if (result['success'] == true) {
        final exam = result['data']['exam'];
        setState(() {
          _judulController.text = exam['judul'] ?? '';
          _deskripsiController.text = exam['deskripsi'] ?? '';
          _durasiController.text = exam['durasi']?.toString() ?? '60';
          _mulaiController.text = exam['mulai'] != null
              ? DateFormat('yyyy-MM-ddTHH:mm')
                  .format(DateTime.parse(exam['mulai']))
              : '';
          _selesaiController.text = exam['selesai'] != null
              ? DateFormat('yyyy-MM-ddTHH:mm')
                  .format(DateTime.parse(exam['selesai']))
              : '';
          _bobotController.text = exam['bobot']?.toString() ?? '0';
          _tipe = exam['tipe'] ?? 'pilgan';
          _status = exam['status'] ?? 'draft';
          _randomSoal = exam['random_soal'] == true;
          _randomPilihan = exam['random_pilihan'] == true;
          _tampilkanNilai = exam['tampilkan_nilai'] == true;
          _preventCopyPaste = exam['prevent_copy_paste'] == true;
          _preventNewTab = exam['prevent_new_tab'] == true;
          _fullscreenMode = exam['fullscreen_mode'] == true;
          isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
      });
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

  Future<void> _selectDateTime(
      TextEditingController controller, String label) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (picked != null) {
      final TimeOfDay? time = await showTimePicker(
        context: context,
        initialTime: TimeOfDay.now(),
      );
      if (time != null) {
        final DateTime dateTime = DateTime(
          picked.year,
          picked.month,
          picked.day,
          time.hour,
          time.minute,
        );
        setState(() {
          controller.text = DateFormat('yyyy-MM-ddTHH:mm').format(dateTime);
        });
      }
    }
  }

  Future<void> _saveExam() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    if (widget.jadwalId == null && !isEditMode) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Jadwal kuliah tidak ditemukan'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      isLoading = true;
    });

    try {
      final data = {
        if (!isEditMode) 'jadwal_kuliah_id': widget.jadwalId,
        'judul': _judulController.text.trim(),
        'deskripsi': _deskripsiController.text.trim(),
        'tipe': _tipe,
        'durasi': int.parse(_durasiController.text),
        if (_mulaiController.text.isNotEmpty) 'mulai': _mulaiController.text,
        'selesai': _selesaiController.text,
        'bobot': double.parse(_bobotController.text),
        'random_soal': _randomSoal,
        'random_pilihan': _randomPilihan,
        'tampilkan_nilai': _tampilkanNilai,
        'prevent_copy_paste': _preventCopyPaste,
        'prevent_new_tab': _preventNewTab,
        'fullscreen_mode': _fullscreenMode,
        'status': _status,
      };

      final result = isEditMode
          ? await ApiService.put('/dosen/exam/${widget.examId}', data)
          : await ApiService.post('/dosen/exam', data);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(isEditMode
                  ? 'Ujian berhasil diperbarui'
                  : 'Ujian berhasil dibuat'),
              backgroundColor: Colors.green,
            ),
          );
          context.pop(true);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menyimpan ujian'),
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
          isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(isEditMode ? 'Edit Ujian' : 'Tambah Ujian'),
      ),
      body: isLoading && !isEditMode
          ? const Center(child: CircularProgressIndicator())
          : Form(
              key: _formKey,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    TextFormField(
                      controller: _judulController,
                      decoration: InputDecoration(
                        labelText: 'Judul *',
                        hintText: 'Masukkan judul ujian',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Judul wajib diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _deskripsiController,
                      decoration: InputDecoration(
                        labelText: 'Deskripsi',
                        hintText: 'Masukkan deskripsi ujian',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      maxLines: 3,
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      value: _tipe,
                      decoration: InputDecoration(
                        labelText: 'Tipe Ujian *',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      items: const [
                        DropdownMenuItem(
                          value: 'pilgan',
                          child: Text('Pilihan Ganda'),
                        ),
                        DropdownMenuItem(
                          value: 'essay',
                          child: Text('Essay'),
                        ),
                        DropdownMenuItem(
                          value: 'campuran',
                          child: Text('Campuran'),
                        ),
                      ],
                      onChanged: (value) {
                        if (value != null) {
                          setState(() {
                            _tipe = value;
                          });
                        }
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _durasiController,
                      decoration: InputDecoration(
                        labelText: 'Durasi (menit) *',
                        hintText: 'Masukkan durasi',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Durasi wajib diisi';
                        }
                        final durasi = int.tryParse(value);
                        if (durasi == null || durasi < 1 || durasi > 600) {
                          return 'Durasi harus antara 1-600 menit';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _mulaiController,
                      decoration: InputDecoration(
                        labelText: 'Waktu Mulai',
                        hintText: 'Pilih waktu mulai',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                        suffixIcon: const Icon(Icons.calendar_today),
                      ),
                      readOnly: true,
                      onTap: () => _selectDateTime(_mulaiController, 'Mulai'),
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _selesaiController,
                      decoration: InputDecoration(
                        labelText: 'Waktu Selesai *',
                        hintText: 'Pilih waktu selesai',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                        suffixIcon: const Icon(Icons.calendar_today),
                      ),
                      readOnly: true,
                      onTap: () => _selectDateTime(_selesaiController, 'Selesai'),
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Waktu selesai wajib diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _bobotController,
                      decoration: InputDecoration(
                        labelText: 'Bobot (0-100) *',
                        hintText: 'Masukkan bobot',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Bobot wajib diisi';
                        }
                        final bobot = double.tryParse(value);
                        if (bobot == null || bobot < 0 || bobot > 100) {
                          return 'Bobot harus antara 0-100';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      value: _status,
                      decoration: InputDecoration(
                        labelText: 'Status *',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      items: const [
                        DropdownMenuItem(
                          value: 'draft',
                          child: Text('Draft'),
                        ),
                        DropdownMenuItem(
                          value: 'published',
                          child: Text('Published'),
                        ),
                      ],
                      onChanged: (value) {
                        if (value != null) {
                          setState(() {
                            _status = value;
                          });
                        }
                      },
                    ),
                    const SizedBox(height: 16),
                    // Checkboxes
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Pengaturan',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            CheckboxListTile(
                              title: const Text('Acak Soal'),
                              value: _randomSoal,
                              onChanged: (value) {
                                setState(() {
                                  _randomSoal = value ?? false;
                                });
                              },
                            ),
                            CheckboxListTile(
                              title: const Text('Acak Pilihan'),
                              value: _randomPilihan,
                              onChanged: (value) {
                                setState(() {
                                  _randomPilihan = value ?? false;
                                });
                              },
                            ),
                            CheckboxListTile(
                              title: const Text('Tampilkan Nilai'),
                              value: _tampilkanNilai,
                              onChanged: (value) {
                                setState(() {
                                  _tampilkanNilai = value ?? false;
                                });
                              },
                            ),
                            CheckboxListTile(
                              title: const Text('Prevent Copy Paste'),
                              value: _preventCopyPaste,
                              onChanged: (value) {
                                setState(() {
                                  _preventCopyPaste = value ?? false;
                                });
                              },
                            ),
                            CheckboxListTile(
                              title: const Text('Prevent New Tab'),
                              value: _preventNewTab,
                              onChanged: (value) {
                                setState(() {
                                  _preventNewTab = value ?? false;
                                });
                              },
                            ),
                            CheckboxListTile(
                              title: const Text('Fullscreen Mode'),
                              value: _fullscreenMode,
                              onChanged: (value) {
                                setState(() {
                                  _fullscreenMode = value ?? false;
                                });
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: isLoading ? null : _saveExam,
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                        ),
                        child: isLoading
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor:
                                      AlwaysStoppedAnimation<Color>(Colors.white),
                                ),
                              )
                            : Text(isEditMode ? 'Perbarui' : 'Simpan'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}

