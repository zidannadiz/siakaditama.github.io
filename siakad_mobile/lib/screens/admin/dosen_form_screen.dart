import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class DosenFormScreen extends StatefulWidget {
  final int? dosenId;

  const DosenFormScreen({Key? key, this.dosenId}) : super(key: key);

  @override
  State<DosenFormScreen> createState() => _DosenFormScreenState();
}

class _DosenFormScreenState extends State<DosenFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nidnController = TextEditingController();
  final _namaController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  String? selectedStatus;
  bool isLoading = true;
  bool isSaving = false;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    if (widget.dosenId != null) {
      _loadDosen();
    } else {
      isLoading = false;
    }
  }

  @override
  void dispose() {
    _nidnController.dispose();
    _namaController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  Future<void> _loadDosen() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/dosen/${widget.dosenId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          _nidnController.text = data['nidn'] ?? '';
          _namaController.text = data['nama'] ?? '';
          _emailController.text = data['email'] ?? '';
          selectedStatus = data['status'];
          // Password tidak di-load untuk keamanan
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data dosen';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _saveDosen() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final data = {
        'nidn': _nidnController.text.trim(),
        'nama': _namaController.text.trim(),
        'email': _emailController.text.trim(),
        'status': selectedStatus,
      };

      // Add password only for new dosen
      if (widget.dosenId == null) {
        data['password'] = _passwordController.text.trim();
      }

      final result = widget.dosenId == null
          ? await ApiService.post('/admin/dosen', data)
          : await ApiService.put('/admin/dosen/${widget.dosenId}', data);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ??
                    (widget.dosenId == null
                        ? 'Dosen berhasil ditambahkan'
                        : 'Dosen berhasil diperbarui'),
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
              content: Text(result['message'] ?? 'Gagal menyimpan dosen'),
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
        title: Text(widget.dosenId == null ? 'Tambah Dosen' : 'Edit Dosen'),
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
                      controller: _nidnController,
                      decoration: const InputDecoration(
                        labelText: 'NIDN *',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.badge),
                      ),
                      enabled: widget.dosenId == null,
                      validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'NIDN harus diisi';
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
                      enabled: widget.dosenId == null,
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
                    if (widget.dosenId == null) ...[
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
                      onPressed: isSaving ? null : _saveDosen,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        backgroundColor: Colors.teal,
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
