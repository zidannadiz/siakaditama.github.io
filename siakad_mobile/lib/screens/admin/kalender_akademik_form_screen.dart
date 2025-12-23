import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class KalenderAkademikFormScreen extends StatefulWidget {
  final int? eventId;

  const KalenderAkademikFormScreen({Key? key, this.eventId}) : super(key: key);

  @override
  State<KalenderAkademikFormScreen> createState() =>
      _KalenderAkademikFormScreenState();
}

class _KalenderAkademikFormScreenState
    extends State<KalenderAkademikFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _judulController = TextEditingController();
  final _deskripsiController = TextEditingController();
  final _linkController = TextEditingController();

  DateTime? _tanggalMulai;
  DateTime? _tanggalSelesai;
  TimeOfDay? _jamMulai;
  TimeOfDay? _jamSelesai;
  String? selectedJenis;
  String? selectedTargetRole;
  int? selectedSemesterId;
  bool isImportant = false;
  List<dynamic> semesters = [];

  bool isLoading = false;
  bool isLoadingData = true;
  String? errorMessage;

  final Map<String, String> jenisOptions = {
    'semester': 'Semester',
    'krs': 'KRS',
    'pembayaran': 'Pembayaran',
    'ujian': 'Ujian',
    'libur': 'Libur',
    'kegiatan': 'Kegiatan',
    'pengumuman': 'Pengumuman',
    'lainnya': 'Lainnya',
  };

  @override
  void initState() {
    super.initState();
    _loadSemesters();
    if (widget.eventId != null) {
      _loadEvent();
    } else {
      setState(() {
        isLoadingData = false;
        selectedJenis = 'lainnya';
        selectedTargetRole = 'semua';
      });
    }
  }

  @override
  void dispose() {
    _judulController.dispose();
    _deskripsiController.dispose();
    _linkController.dispose();
    super.dispose();
  }

  Future<void> _loadSemesters() async {
    try {
      final result = await ApiService.get('/admin/semester');
      if (result['success'] == true) {
        setState(() {
          semesters = result['data'] ?? [];
        });
      }
    } catch (e) {
      // Ignore error
    }
  }

  Future<void> _loadEvent() async {
    setState(() {
      isLoadingData = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/admin/kalender-akademik/${widget.eventId}',
      );
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _judulController.text = data['judul'] ?? '';
          _deskripsiController.text = data['deskripsi'] ?? '';
          _linkController.text = data['link'] ?? '';
          _tanggalMulai = DateTime.parse(data['tanggal_mulai']);
          _tanggalSelesai = data['tanggal_selesai'] != null
              ? DateTime.parse(data['tanggal_selesai'])
              : null;
          if (data['jam_mulai'] != null) {
            final time = data['jam_mulai'].split(':');
            _jamMulai = TimeOfDay(
              hour: int.parse(time[0]),
              minute: int.parse(time[1]),
            );
          }
          if (data['jam_selesai'] != null) {
            final time = data['jam_selesai'].split(':');
            _jamSelesai = TimeOfDay(
              hour: int.parse(time[0]),
              minute: int.parse(time[1]),
            );
          }
          selectedJenis = data['jenis'];
          selectedTargetRole = data['target_role'];
          selectedSemesterId = data['semester']?['id'];
          isImportant = data['is_important'] == true;
          isLoadingData = false;
        });
      } else {
        setState(() {
          isLoadingData = false;
          errorMessage = result['message'] ?? 'Gagal memuat event';
        });
      }
    } catch (e) {
      setState(() {
        isLoadingData = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _selectDate(BuildContext context, bool isMulai) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: isMulai
          ? (_tanggalMulai ?? DateTime.now())
          : (_tanggalSelesai ?? _tanggalMulai ?? DateTime.now()),
      firstDate: DateTime(2020),
      lastDate: DateTime(2030),
    );
    if (picked != null) {
      setState(() {
        if (isMulai) {
          _tanggalMulai = picked;
          if (_tanggalSelesai != null && _tanggalSelesai!.isBefore(picked)) {
            _tanggalSelesai = null;
          }
        } else {
          _tanggalSelesai = picked;
        }
      });
    }
  }

  Future<void> _selectTime(BuildContext context, bool isMulai) async {
    final picked = await showTimePicker(
      context: context,
      initialTime: isMulai
          ? (_jamMulai ?? TimeOfDay.now())
          : (_jamSelesai ?? TimeOfDay.now()),
    );
    if (picked != null) {
      setState(() {
        if (isMulai) {
          _jamMulai = picked;
        } else {
          _jamSelesai = picked;
        }
      });
    }
  }

  Future<void> _saveEvent() async {
    if (!_formKey.currentState!.validate()) return;
    if (_tanggalMulai == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tanggal mulai harus diisi'),
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
        'judul': _judulController.text.trim(),
        'deskripsi': _deskripsiController.text.trim().isEmpty
            ? null
            : _deskripsiController.text.trim(),
        'tanggal_mulai': _tanggalMulai!.toIso8601String().split('T')[0],
        'tanggal_selesai': _tanggalSelesai?.toIso8601String().split('T')[0],
        'jam_mulai': _jamMulai != null
            ? '${_jamMulai!.hour.toString().padLeft(2, '0')}:${_jamMulai!.minute.toString().padLeft(2, '0')}'
            : null,
        'jam_selesai': _jamSelesai != null
            ? '${_jamSelesai!.hour.toString().padLeft(2, '0')}:${_jamSelesai!.minute.toString().padLeft(2, '0')}'
            : null,
        'jenis': selectedJenis,
        'target_role': selectedTargetRole,
        'semester_id': selectedSemesterId,
        'is_important': isImportant,
        'link': _linkController.text.trim().isEmpty
            ? null
            : _linkController.text.trim(),
      };

      final result = widget.eventId == null
          ? await ApiService.post('/admin/kalender-akademik', data)
          : await ApiService.put(
              '/admin/kalender-akademik/${widget.eventId}',
              data,
            );

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                widget.eventId == null
                    ? 'Event berhasil ditambahkan'
                    : 'Event berhasil diperbarui',
              ),
              backgroundColor: Colors.green,
            ),
          );
          context.pop();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menyimpan event'),
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
        title: Text(widget.eventId == null ? 'Tambah Event' : 'Edit Event'),
      ),
      body: isLoadingData
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
                      controller: _deskripsiController,
                      decoration: const InputDecoration(
                        labelText: 'Deskripsi',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.description),
                        alignLabelWithHint: true,
                      ),
                      maxLines: 4,
                    ),
                    const SizedBox(height: 16),
                    InkWell(
                      onTap: () => _selectDate(context, true),
                      child: InputDecorator(
                        decoration: const InputDecoration(
                          labelText: 'Tanggal Mulai *',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.calendar_today),
                        ),
                        child: Text(
                          _tanggalMulai != null
                              ? DateFormat(
                                  'dd MMMM yyyy',
                                  'id_ID',
                                ).format(_tanggalMulai!)
                              : 'Pilih tanggal',
                          style: TextStyle(
                            color: _tanggalMulai != null
                                ? Colors.black87
                                : Colors.grey[600],
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    InkWell(
                      onTap: () => _selectDate(context, false),
                      child: InputDecorator(
                        decoration: const InputDecoration(
                          labelText: 'Tanggal Selesai (Opsional)',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.calendar_today),
                        ),
                        child: Text(
                          _tanggalSelesai != null
                              ? DateFormat(
                                  'dd MMMM yyyy',
                                  'id_ID',
                                ).format(_tanggalSelesai!)
                              : 'Pilih tanggal (opsional)',
                          style: TextStyle(
                            color: _tanggalSelesai != null
                                ? Colors.black87
                                : Colors.grey[600],
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: InkWell(
                            onTap: () => _selectTime(context, true),
                            child: InputDecorator(
                              decoration: const InputDecoration(
                                labelText: 'Jam Mulai',
                                border: OutlineInputBorder(),
                                prefixIcon: Icon(Icons.access_time),
                              ),
                              child: Text(
                                _jamMulai != null
                                    ? _jamMulai!.format(context)
                                    : 'Pilih jam (opsional)',
                                style: TextStyle(
                                  color: _jamMulai != null
                                      ? Colors.black87
                                      : Colors.grey[600],
                                ),
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: InkWell(
                            onTap: () => _selectTime(context, false),
                            child: InputDecorator(
                              decoration: const InputDecoration(
                                labelText: 'Jam Selesai',
                                border: OutlineInputBorder(),
                                prefixIcon: Icon(Icons.access_time),
                              ),
                              child: Text(
                                _jamSelesai != null
                                    ? _jamSelesai!.format(context)
                                    : 'Pilih jam (opsional)',
                                style: TextStyle(
                                  color: _jamSelesai != null
                                      ? Colors.black87
                                      : Colors.grey[600],
                                ),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      value: selectedJenis,
                      decoration: const InputDecoration(
                        labelText: 'Jenis *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.category),
                      ),
                      items: jenisOptions.entries.map((entry) {
                        return DropdownMenuItem(
                          value: entry.key,
                          child: Text(entry.value),
                        );
                      }).toList(),
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
                      value: selectedTargetRole,
                      decoration: const InputDecoration(
                        labelText: 'Target Role *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.people),
                      ),
                      items: const [
                        DropdownMenuItem(value: 'semua', child: Text('Semua')),
                        DropdownMenuItem(value: 'admin', child: Text('Admin')),
                        DropdownMenuItem(value: 'dosen', child: Text('Dosen')),
                        DropdownMenuItem(
                          value: 'mahasiswa',
                          child: Text('Mahasiswa'),
                        ),
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedTargetRole = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Target role harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    if (semesters.isNotEmpty)
                      DropdownButtonFormField<int>(
                        value: selectedSemesterId,
                        decoration: const InputDecoration(
                          labelText: 'Semester (Opsional)',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.school),
                        ),
                        items: [
                          const DropdownMenuItem<int>(
                            value: null,
                            child: Text('Tidak ada'),
                          ),
                          ...semesters.map((semester) {
                            return DropdownMenuItem<int>(
                              value: semester['id'],
                              child: Text(
                                '${semester['nama']} ${semester['tahun_ajaran']}',
                              ),
                            );
                          }),
                        ],
                        onChanged: (value) {
                          setState(() {
                            selectedSemesterId = value;
                          });
                        },
                      ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _linkController,
                      decoration: const InputDecoration(
                        labelText: 'Link (Opsional)',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.link),
                        helperText: 'URL terkait event',
                      ),
                      keyboardType: TextInputType.url,
                    ),
                    const SizedBox(height: 16),
                    SwitchListTile(
                      title: const Text('Event Penting'),
                      subtitle: const Text(
                        'Event penting akan mengirim notifikasi',
                      ),
                      value: isImportant,
                      onChanged: (value) {
                        setState(() {
                          isImportant = value;
                        });
                      },
                      secondary: Icon(
                        isImportant ? Icons.star : Icons.star_border,
                        color: isImportant ? Colors.orange : Colors.grey,
                      ),
                    ),
                    const SizedBox(height: 24),
                    ElevatedButton(
                      onPressed: isLoading ? null : _saveEvent,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.blue,
                      ),
                      child: isLoading
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
