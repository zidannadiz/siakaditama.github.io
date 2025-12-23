import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class JadwalKuliahFormScreen extends StatefulWidget {
  final int? jadwalKuliahId;

  const JadwalKuliahFormScreen({Key? key, this.jadwalKuliahId})
    : super(key: key);

  @override
  State<JadwalKuliahFormScreen> createState() => _JadwalKuliahFormScreenState();
}

class _JadwalKuliahFormScreenState extends State<JadwalKuliahFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _ruanganController = TextEditingController();
  final _kuotaController = TextEditingController();
  final _jamMulaiController = TextEditingController();
  final _jamSelesaiController = TextEditingController();

  List<dynamic> mataKuliahs = [];
  List<dynamic> dosens = [];
  List<dynamic> semesters = [];
  int? selectedMataKuliahId;
  int? selectedDosenId;
  int? selectedSemesterId;
  String? selectedHari;
  String? selectedStatus;
  TimeOfDay? jamMulai;
  TimeOfDay? jamSelesai;
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  final List<String> hariList = [
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jumat',
    'Sabtu',
    'Minggu',
  ];

  @override
  void initState() {
    super.initState();
    _loadData();
    if (widget.jadwalKuliahId != null) {
      _loadJadwalKuliah();
    } else {
      isLoading = false;
    }
  }

  @override
  void dispose() {
    _ruanganController.dispose();
    _kuotaController.dispose();
    _jamMulaiController.dispose();
    _jamSelesaiController.dispose();
    super.dispose();
  }

  Future<void> _loadData() async {
    try {
      final results = await Future.wait([
        ApiService.get('/admin/mata-kuliah?page=1'),
        ApiService.get('/admin/dosen?page=1'),
        ApiService.get('/admin/semester'),
      ]);

      if (results[0]['success'] == true) {
        setState(() {
          mataKuliahs = results[0]['data']['mata_kuliahs'] ?? [];
        });
      }

      if (results[1]['success'] == true) {
        setState(() {
          dosens = results[1]['data']['dosens'] ?? [];
        });
      }

      if (results[2]['success'] == true) {
        setState(() {
          semesters = results[2]['data'] ?? [];
        });
      }
    } catch (e) {
      // Ignore error
    }
  }

  Future<void> _loadJadwalKuliah() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/admin/jadwal-kuliah/${widget.jadwalKuliahId}',
      );
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          selectedMataKuliahId = data['mata_kuliah_id'];
          selectedDosenId = data['dosen_id'];
          selectedSemesterId = data['semester_id'];
          selectedHari = data['hari'];
          _ruanganController.text = data['ruangan'] ?? '';
          _kuotaController.text = data['kuota']?.toString() ?? '';
          selectedStatus = data['status'];

          // Parse jam
          if (data['jam_mulai'] != null) {
            final jamMulaiStr = data['jam_mulai'];
            final parts = jamMulaiStr.split(':');
            if (parts.length == 2) {
              jamMulai = TimeOfDay(
                hour: int.parse(parts[0]),
                minute: int.parse(parts[1]),
              );
              _jamMulaiController.text = jamMulaiStr;
            }
          }

          if (data['jam_selesai'] != null) {
            final jamSelesaiStr = data['jam_selesai'];
            final parts = jamSelesaiStr.split(':');
            if (parts.length == 2) {
              jamSelesai = TimeOfDay(
                hour: int.parse(parts[0]),
                minute: int.parse(parts[1]),
              );
              _jamSelesaiController.text = jamSelesaiStr;
            }
          }

          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data jadwal kuliah';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _selectTime(BuildContext context, bool isJamMulai) async {
    final TimeOfDay? picked = await showTimePicker(
      context: context,
      initialTime: isJamMulai
          ? (jamMulai ?? TimeOfDay.now())
          : (jamSelesai ?? TimeOfDay.now()),
    );

    if (picked != null) {
      final formattedTime =
          '${picked.hour.toString().padLeft(2, '0')}:${picked.minute.toString().padLeft(2, '0')}';
      setState(() {
        if (isJamMulai) {
          jamMulai = picked;
          _jamMulaiController.text = formattedTime;
        } else {
          jamSelesai = picked;
          _jamSelesaiController.text = formattedTime;
        }
      });
    }
  }

  Future<void> _saveJadwalKuliah() async {
    if (!_formKey.currentState!.validate()) return;

    if (jamMulai == null || jamSelesai == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Jam mulai dan jam selesai harus diisi'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'mata_kuliah_id': selectedMataKuliahId,
        'dosen_id': selectedDosenId,
        'semester_id': selectedSemesterId,
        'hari': selectedHari,
        'jam_mulai': _jamMulaiController.text.trim(),
        'jam_selesai': _jamSelesaiController.text.trim(),
        'ruangan': _ruanganController.text.trim().isNotEmpty
            ? _ruanganController.text.trim()
            : null,
        'kuota': int.tryParse(_kuotaController.text.trim()) ?? 1,
        'status': selectedStatus,
      };

      final result = widget.jadwalKuliahId == null
          ? await ApiService.post('/admin/jadwal-kuliah', data)
          : await ApiService.put(
              '/admin/jadwal-kuliah/${widget.jadwalKuliahId}',
              data,
            );

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.jadwalKuliahId == null
                        ? 'Jadwal kuliah berhasil ditambahkan'
                        : 'Jadwal kuliah berhasil diperbarui'),
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
              content: Text(
                result['message'] ?? 'Gagal menyimpan jadwal kuliah',
              ),
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
          widget.jadwalKuliahId == null
              ? 'Tambah Jadwal Kuliah'
              : 'Edit Jadwal Kuliah',
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
                    DropdownButtonFormField<int>(
                      value: selectedMataKuliahId,
                      decoration: const InputDecoration(
                        labelText: 'Mata Kuliah *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.menu_book),
                      ),
                      items: mataKuliahs.map((mk) {
                        return DropdownMenuItem<int>(
                          value: mk['id'],
                          child: Text('${mk['kode_mk']} - ${mk['nama']}'),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          selectedMataKuliahId = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Mata Kuliah harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<int>(
                      value: selectedDosenId,
                      decoration: const InputDecoration(
                        labelText: 'Dosen *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.person_outline),
                      ),
                      items: dosens.map((dosen) {
                        return DropdownMenuItem<int>(
                          value: dosen['id'],
                          child: Text('${dosen['nidn']} - ${dosen['nama']}'),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          selectedDosenId = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Dosen harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<int>(
                      value: selectedSemesterId,
                      decoration: const InputDecoration(
                        labelText: 'Semester *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.calendar_today),
                      ),
                      items: semesters.map((semester) {
                        return DropdownMenuItem<int>(
                          value: semester['id'],
                          child: Text(
                            '${semester['nama']} (${semester['tahun_ajaran']})',
                          ),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          selectedSemesterId = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Semester harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    DropdownButtonFormField<String>(
                      value: selectedHari,
                      decoration: const InputDecoration(
                        labelText: 'Hari *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.calendar_view_week),
                      ),
                      items: hariList.map((hari) {
                        return DropdownMenuItem<String>(
                          value: hari,
                          child: Text(hari),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          selectedHari = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Hari harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _jamMulaiController,
                      decoration: InputDecoration(
                        labelText: 'Jam Mulai *',
                        border: const OutlineInputBorder(),
                        prefixIcon: const Icon(Icons.access_time),
                        suffixIcon: IconButton(
                          icon: const Icon(Icons.schedule),
                          onPressed: () => _selectTime(context, true),
                        ),
                        helperText: 'Format: HH:mm',
                      ),
                      readOnly: true,
                      onTap: () => _selectTime(context, true),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Jam mulai harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _jamSelesaiController,
                      decoration: InputDecoration(
                        labelText: 'Jam Selesai *',
                        border: const OutlineInputBorder(),
                        prefixIcon: const Icon(Icons.access_time),
                        suffixIcon: IconButton(
                          icon: const Icon(Icons.schedule),
                          onPressed: () => _selectTime(context, false),
                        ),
                        helperText: 'Format: HH:mm',
                      ),
                      readOnly: true,
                      onTap: () => _selectTime(context, false),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Jam selesai harus diisi';
                        }
                        if (_jamMulaiController.text.isNotEmpty &&
                            value.compareTo(_jamMulaiController.text) <= 0) {
                          return 'Jam selesai harus setelah jam mulai';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _ruanganController,
                      decoration: const InputDecoration(
                        labelText: 'Ruangan',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.room),
                        helperText: 'Opsional',
                      ),
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _kuotaController,
                      decoration: const InputDecoration(
                        labelText: 'Kuota *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.people),
                        helperText: 'Minimal 1',
                      ),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Kuota harus diisi';
                        }
                        final kuota = int.tryParse(value);
                        if (kuota == null || kuota < 1) {
                          return 'Kuota minimal 1';
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
                      ),
                      items: const [
                        DropdownMenuItem(value: 'aktif', child: Text('Aktif')),
                        DropdownMenuItem(
                          value: 'nonaktif',
                          child: Text('Nonaktif'),
                        ),
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
                      onPressed: isSaving ? null : _saveJadwalKuliah,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.pink,
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
