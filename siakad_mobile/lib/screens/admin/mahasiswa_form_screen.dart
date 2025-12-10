import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class MahasiswaFormScreen extends StatefulWidget {
  final int? mahasiswaId;

  const MahasiswaFormScreen({Key? key, this.mahasiswaId}) : super(key: key);

  @override
  State<MahasiswaFormScreen> createState() => _MahasiswaFormScreenState();
}

class _MahasiswaFormScreenState extends State<MahasiswaFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nimController = TextEditingController();
  final _namaController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _semesterController = TextEditingController();

  List<dynamic> prodis = [];
  int? selectedProdiId;
  String? selectedJenisKelamin;
  String? selectedStatus;
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadProdis();
    if (widget.mahasiswaId != null) {
      _loadMahasiswa();
    }
  }

  @override
  void dispose() {
    _nimController.dispose();
    _namaController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _semesterController.dispose();
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

  Future<void> _loadMahasiswa() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get(
        '/admin/mahasiswa/${widget.mahasiswaId}',
      );
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _nimController.text = data['nim'] ?? '';
          _namaController.text = data['nama'] ?? '';
          _emailController.text = data['email'] ?? '';
          selectedProdiId = data['prodi_id'];
          selectedJenisKelamin = data['jenis_kelamin'];
          _semesterController.text = data['semester']?.toString() ?? '';
          selectedStatus = data['status'];
          // Password tidak di-load untuk keamanan
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data mahasiswa';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _saveMahasiswa() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'nim': _nimController.text.trim(),
        'nama': _namaController.text.trim(),
        'email': _emailController.text.trim(),
        'prodi_id': selectedProdiId,
        'jenis_kelamin': selectedJenisKelamin,
        'semester': int.tryParse(_semesterController.text.trim()) ?? 1,
        'status': selectedStatus,
      };

      // Add password only for new mahasiswa
      if (widget.mahasiswaId == null) {
        data['password'] = _passwordController.text.trim();
      }

      final result = widget.mahasiswaId == null
          ? await ApiService.post('/admin/mahasiswa', data)
          : await ApiService.put(
              '/admin/mahasiswa/${widget.mahasiswaId}',
              data,
            );

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.mahasiswaId == null
                        ? 'Mahasiswa berhasil ditambahkan'
                        : 'Mahasiswa berhasil diperbarui'),
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
              content: Text(result['message'] ?? 'Gagal menyimpan mahasiswa'),
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
          widget.mahasiswaId == null ? 'Tambah Mahasiswa' : 'Edit Mahasiswa',
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
                      controller: _nimController,
                      decoration: const InputDecoration(
                        labelText: 'NIM *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.badge),
                      ),
                      enabled: widget.mahasiswaId == null,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'NIM harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _namaController,
                      decoration: const InputDecoration(
                        labelText: 'Nama *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.person),
                      ),
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Nama harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _emailController,
                      decoration: const InputDecoration(
                        labelText: 'Email *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.email),
                      ),
                      enabled: widget.mahasiswaId == null,
                      keyboardType: TextInputType.emailAddress,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Email harus diisi';
                        }
                        if (!value.contains('@')) {
                          return 'Email tidak valid';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    if (widget.mahasiswaId == null) ...[
                      TextFormField(
                        controller: _passwordController,
                        decoration: const InputDecoration(
                          labelText: 'Password *',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.lock),
                        ),
                        obscureText: true,
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Password harus diisi';
                          }
                          if (value.length < 8) {
                            return 'Password minimal 8 karakter';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                    ],
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
                    DropdownButtonFormField<String>(
                      value: selectedJenisKelamin,
                      decoration: const InputDecoration(
                        labelText: 'Jenis Kelamin *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.person_outline),
                      ),
                      items: const [
                        DropdownMenuItem(value: 'L', child: Text('Laki-laki')),
                        DropdownMenuItem(value: 'P', child: Text('Perempuan')),
                      ],
                      onChanged: (value) {
                        setState(() {
                          selectedJenisKelamin = value;
                        });
                      },
                      validator: (value) {
                        if (value == null) {
                          return 'Jenis Kelamin harus dipilih';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _semesterController,
                      decoration: const InputDecoration(
                        labelText: 'Semester *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.numbers),
                        helperText: '1-14',
                      ),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Semester harus diisi';
                        }
                        final semester = int.tryParse(value);
                        if (semester == null || semester < 1 || semester > 14) {
                          return 'Semester harus antara 1-14';
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
                        DropdownMenuItem(value: 'lulus', child: Text('Lulus')),
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
                      onPressed: isSaving ? null : _saveMahasiswa,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.blue,
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
