import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import 'letter_grade_form_screen.dart';

class SystemSettingsScreen extends StatefulWidget {
  const SystemSettingsScreen({Key? key}) : super(key: key);

  @override
  State<SystemSettingsScreen> createState() => _SystemSettingsScreenState();
}

class _SystemSettingsScreenState extends State<SystemSettingsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  Map<String, dynamic>? settings;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 4, vsync: this);
    _loadSettings();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _loadSettings() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/system-settings');
      if (result['success'] == true) {
        setState(() {
          settings = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat settings';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('System Settings'),
        bottom: TabBar(
          controller: _tabController,
          isScrollable: true,
          tabs: const [
            Tab(text: 'Semester Aktif', icon: Icon(Icons.calendar_today)),
            Tab(text: 'Bobot Penilaian', icon: Icon(Icons.percent)),
            Tab(text: 'Huruf Mutu', icon: Icon(Icons.grade)),
            Tab(text: 'Info Aplikasi', icon: Icon(Icons.info)),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadSettings,
            tooltip: 'Refresh',
          ),
        ],
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
                    onPressed: _loadSettings,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : settings == null
          ? const Center(child: Text('Settings tidak tersedia'))
          : TabBarView(
              controller: _tabController,
              children: [
                _SemesterAktifTab(
                  settings: settings!['semester'],
                  onUpdate: _loadSettings,
                ),
                _BobotPenilaianTab(
                  settings: settings!['grading'],
                  onUpdate: _loadSettings,
                ),
                _HurufMutuTab(
                  letterGrades: settings!['letter_grades'],
                  onUpdate: _loadSettings,
                ),
                _InfoAplikasiTab(
                  appInfo: settings!['app_info'],
                  onUpdate: _loadSettings,
                ),
              ],
            ),
    );
  }
}

// Tab: Semester Aktif
class _SemesterAktifTab extends StatefulWidget {
  final Map<String, dynamic> settings;
  final VoidCallback onUpdate;

  const _SemesterAktifTab({required this.settings, required this.onUpdate});

  @override
  State<_SemesterAktifTab> createState() => _SemesterAktifTabState();
}

class _SemesterAktifTabState extends State<_SemesterAktifTab> {
  int? selectedSemesterId;
  bool isSaving = false;

  @override
  void initState() {
    super.initState();
    selectedSemesterId = widget.settings['active_semester']?['id'];
  }

  Future<void> _saveSemester() async {
    if (selectedSemesterId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pilih semester terlebih dahulu'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      isSaving = true;
    });

    try {
      final result = await ApiService.post('/admin/system-settings/semester', {
        'semester_id': selectedSemesterId,
      });

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Semester aktif berhasil diubah'),
              backgroundColor: Colors.green,
            ),
          );
          widget.onUpdate();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal mengubah semester aktif',
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
    final semesters = widget.settings['semesters'] ?? [];
    final activeSemester = widget.settings['active_semester'];

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          if (activeSemester != null)
            Card(
              color: Colors.green[50],
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Row(
                  children: [
                    Icon(Icons.check_circle, color: Colors.green[700]),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Semester Aktif Saat Ini',
                            style: TextStyle(fontSize: 12, color: Colors.grey),
                          ),
                          Text(
                            '${activeSemester['nama']} ${activeSemester['tahun_ajaran']}',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: Colors.green[900],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          const SizedBox(height: 24),
          DropdownButtonFormField<int>(
            value: selectedSemesterId,
            decoration: const InputDecoration(
              labelText: 'Pilih Semester Aktif *',
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.school),
            ),
            items: semesters.map<DropdownMenuItem<int>>((semester) {
              return DropdownMenuItem<int>(
                value: semester['id'],
                child: Text('${semester['nama']} ${semester['tahun_ajaran']}'),
              );
            }).toList(),
            onChanged: (value) {
              setState(() {
                selectedSemesterId = value;
              });
            },
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: isSaving ? null : _saveSemester,
            icon: isSaving
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                    ),
                  )
                : const Icon(Icons.save),
            label: const Text('Simpan'),
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              backgroundColor: Colors.blue,
            ),
          ),
        ],
      ),
    );
  }
}

// Tab: Bobot Penilaian
class _BobotPenilaianTab extends StatefulWidget {
  final Map<String, dynamic> settings;
  final VoidCallback onUpdate;

  const _BobotPenilaianTab({required this.settings, required this.onUpdate});

  @override
  State<_BobotPenilaianTab> createState() => _BobotPenilaianTabState();
}

class _BobotPenilaianTabState extends State<_BobotPenilaianTab> {
  final _tugasController = TextEditingController();
  final _utsController = TextEditingController();
  final _uasController = TextEditingController();
  bool isSaving = false;

  @override
  void initState() {
    super.initState();
    _tugasController.text = (widget.settings['weight_tugas'] ?? 30).toString();
    _utsController.text = (widget.settings['weight_uts'] ?? 30).toString();
    _uasController.text = (widget.settings['weight_uas'] ?? 40).toString();
  }

  @override
  void dispose() {
    _tugasController.dispose();
    _utsController.dispose();
    _uasController.dispose();
    super.dispose();
  }

  double _calculateTotal() {
    final tugas = double.tryParse(_tugasController.text) ?? 0;
    final uts = double.tryParse(_utsController.text) ?? 0;
    final uas = double.tryParse(_uasController.text) ?? 0;
    return tugas + uts + uas;
  }

  Future<void> _saveGrading() async {
    final tugas = double.tryParse(_tugasController.text);
    final uts = double.tryParse(_utsController.text);
    final uas = double.tryParse(_uasController.text);

    if (tugas == null || uts == null || uas == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Semua field harus diisi dengan angka'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    final total = _calculateTotal();
    if ((total - 100).abs() > 0.01) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Total bobot harus 100%. Saat ini: ${total.toStringAsFixed(1)}%',
          ),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      isSaving = true;
    });

    try {
      final result = await ApiService.post('/admin/system-settings/grading', {
        'weight_tugas': tugas,
        'weight_uts': uts,
        'weight_uas': uas,
      });

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Bobot penilaian berhasil diperbarui'),
              backgroundColor: Colors.green,
            ),
          );
          widget.onUpdate();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal memperbarui bobot penilaian',
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
    final total = _calculateTotal();
    final isValid = (total - 100).abs() < 0.01;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Card(
            color: isValid ? Colors.green[50] : Colors.red[50],
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Icon(
                    isValid ? Icons.check_circle : Icons.error,
                    color: isValid ? Colors.green[700] : Colors.red[700],
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      'Total: ${total.toStringAsFixed(1)}%',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: isValid ? Colors.green[900] : Colors.red[900],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),
          TextFormField(
            controller: _tugasController,
            decoration: const InputDecoration(
              labelText: 'Bobot Tugas (%) *',
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.assignment),
              helperText: 'Bobot untuk nilai tugas',
            ),
            keyboardType: TextInputType.number,
            onChanged: (_) => setState(() {}),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _utsController,
            decoration: const InputDecoration(
              labelText: 'Bobot UTS (%) *',
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.quiz),
              helperText: 'Bobot untuk nilai UTS',
            ),
            keyboardType: TextInputType.number,
            onChanged: (_) => setState(() {}),
          ),
          const SizedBox(height: 16),
          TextFormField(
            controller: _uasController,
            decoration: const InputDecoration(
              labelText: 'Bobot UAS (%) *',
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.assessment),
              helperText: 'Bobot untuk nilai UAS',
            ),
            keyboardType: TextInputType.number,
            onChanged: (_) => setState(() {}),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: isSaving || !isValid ? null : _saveGrading,
            icon: isSaving
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                    ),
                  )
                : const Icon(Icons.save),
            label: const Text('Simpan'),
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              backgroundColor: Colors.blue,
            ),
          ),
        ],
      ),
    );
  }
}

// Tab: Huruf Mutu
class _HurufMutuTab extends StatefulWidget {
  final List<dynamic> letterGrades;
  final VoidCallback onUpdate;

  const _HurufMutuTab({required this.letterGrades, required this.onUpdate});

  @override
  State<_HurufMutuTab> createState() => _HurufMutuTabState();
}

class _HurufMutuTabState extends State<_HurufMutuTab> {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.all(16),
          child: ElevatedButton.icon(
            onPressed: () async {
              await Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (context) => const LetterGradeFormScreen(),
                ),
              );
              widget.onUpdate();
            },
            icon: const Icon(Icons.add),
            label: const Text('Tambah Huruf Mutu'),
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 16),
              backgroundColor: Colors.green,
            ),
          ),
        ),
        Expanded(
          child: widget.letterGrades.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.grade, size: 64, color: Colors.grey[400]),
                      const SizedBox(height: 16),
                      Text(
                        'Belum ada huruf mutu',
                        style: TextStyle(fontSize: 16, color: Colors.grey[600]),
                      ),
                    ],
                  ),
                )
              : ListView.builder(
                  padding: const EdgeInsets.all(8),
                  itemCount: widget.letterGrades.length,
                  itemBuilder: (context, index) {
                    final grade = widget.letterGrades[index];
                    return Card(
                      margin: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: Colors.blue,
                          child: Text(
                            grade['letter'] ?? '-',
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                        title: Text(
                          'Huruf Mutu: ${grade['letter'] ?? '-'}',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        subtitle: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 4),
                            Text(
                              'Range: ${grade['min_score'] ?? 0} - ${grade['max_score'] ?? 100}',
                            ),
                            Text('Bobot: ${grade['bobot'] ?? 0}'),
                          ],
                        ),
                        trailing: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            IconButton(
                              icon: const Icon(Icons.edit),
                              onPressed: () async {
                                await Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (context) => LetterGradeFormScreen(
                                      letterGrade: grade,
                                    ),
                                  ),
                                );
                                widget.onUpdate();
                              },
                              tooltip: 'Edit',
                            ),
                            IconButton(
                              icon: const Icon(Icons.delete),
                              onPressed: () => _deleteLetterGrade(grade['id']),
                              tooltip: 'Hapus',
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
        ),
      ],
    );
  }

  Future<void> _deleteLetterGrade(int id) async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Huruf Mutu'),
        content: const Text(
          'Apakah Anda yakin ingin menghapus huruf mutu ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final result = await ApiService.delete(
        '/admin/system-settings/letter-grades/$id',
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Huruf mutu berhasil dihapus'),
              backgroundColor: Colors.green,
            ),
          );
          widget.onUpdate();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghapus huruf mutu'),
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
    }
  }
}

// Tab: Info Aplikasi
class _InfoAplikasiTab extends StatefulWidget {
  final Map<String, dynamic> appInfo;
  final VoidCallback onUpdate;

  const _InfoAplikasiTab({required this.appInfo, required this.onUpdate});

  @override
  State<_InfoAplikasiTab> createState() => _InfoAplikasiTabState();
}

class _InfoAplikasiTabState extends State<_InfoAplikasiTab> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _institutionController = TextEditingController();
  final _addressController = TextEditingController();
  final _phoneController = TextEditingController();
  final _emailController = TextEditingController();
  final _websiteController = TextEditingController();
  bool isSaving = false;

  @override
  void initState() {
    super.initState();
    _nameController.text = widget.appInfo['name'] ?? '';
    _institutionController.text = widget.appInfo['institution'] ?? '';
    _addressController.text = widget.appInfo['address'] ?? '';
    _phoneController.text = widget.appInfo['phone'] ?? '';
    _emailController.text = widget.appInfo['email'] ?? '';
    _websiteController.text = widget.appInfo['website'] ?? '';
  }

  @override
  void dispose() {
    _nameController.dispose();
    _institutionController.dispose();
    _addressController.dispose();
    _phoneController.dispose();
    _emailController.dispose();
    _websiteController.dispose();
    super.dispose();
  }

  Future<void> _saveAppInfo() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isSaving = true;
    });

    try {
      final result = await ApiService.post('/admin/system-settings/app-info', {
        'name': _nameController.text.trim(),
        'institution': _institutionController.text.trim(),
        'address': _addressController.text.trim(),
        'phone': _phoneController.text.trim(),
        'email': _emailController.text.trim(),
        'website': _websiteController.text.trim(),
      });

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Informasi aplikasi berhasil diperbarui'),
              backgroundColor: Colors.green,
            ),
          );
          widget.onUpdate();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal memperbarui informasi aplikasi',
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
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            TextFormField(
              controller: _nameController,
              decoration: const InputDecoration(
                labelText: 'Nama Aplikasi *',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.apps),
              ),
              validator: (value) {
                if (value == null || value.isEmpty) {
                  return 'Nama aplikasi harus diisi';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _institutionController,
              decoration: const InputDecoration(
                labelText: 'Nama Institusi',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.school),
              ),
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _addressController,
              decoration: const InputDecoration(
                labelText: 'Alamat',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.location_on),
                alignLabelWithHint: true,
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _phoneController,
              decoration: const InputDecoration(
                labelText: 'Nomor Telepon',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.phone),
              ),
              keyboardType: TextInputType.phone,
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _emailController,
              decoration: const InputDecoration(
                labelText: 'Email',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.email),
              ),
              keyboardType: TextInputType.emailAddress,
              validator: (value) {
                if (value != null && value.isNotEmpty && !value.contains('@')) {
                  return 'Email tidak valid';
                }
                return null;
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: _websiteController,
              decoration: const InputDecoration(
                labelText: 'Website',
                border: OutlineInputBorder(),
                prefixIcon: Icon(Icons.language),
              ),
              keyboardType: TextInputType.url,
              validator: (value) {
                if (value != null &&
                    value.isNotEmpty &&
                    !value.startsWith('http')) {
                  return 'URL harus dimulai dengan http:// atau https://';
                }
                return null;
              },
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: isSaving ? null : _saveAppInfo,
              icon: isSaving
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                      ),
                    )
                  : const Icon(Icons.save),
              label: const Text('Simpan'),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 16),
                backgroundColor: Colors.blue,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
