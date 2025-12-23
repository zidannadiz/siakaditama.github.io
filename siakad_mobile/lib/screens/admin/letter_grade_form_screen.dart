import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class LetterGradeFormScreen extends StatefulWidget {
  final Map<String, dynamic>? letterGrade;

  const LetterGradeFormScreen({Key? key, this.letterGrade}) : super(key: key);

  @override
  State<LetterGradeFormScreen> createState() => _LetterGradeFormScreenState();
}

class _LetterGradeFormScreenState extends State<LetterGradeFormScreen> {
  final _formKey = GlobalKey<FormState>();
  final _letterController = TextEditingController();
  final _bobotController = TextEditingController();
  final _minScoreController = TextEditingController();
  final _maxScoreController = TextEditingController();
  bool isSaving = false;

  @override
  void initState() {
    super.initState();
    if (widget.letterGrade != null) {
      _letterController.text = widget.letterGrade!['letter'] ?? '';
      _bobotController.text = widget.letterGrade!['bobot']?.toString() ?? '';
      _minScoreController.text =
          widget.letterGrade!['min_score']?.toString() ?? '';
      _maxScoreController.text =
          widget.letterGrade!['max_score']?.toString() ?? '';
    }
  }

  @override
  void dispose() {
    _letterController.dispose();
    _bobotController.dispose();
    _minScoreController.dispose();
    _maxScoreController.dispose();
    super.dispose();
  }

  Future<void> _saveLetterGrade() async {
    if (!_formKey.currentState!.validate()) return;

    final letter = _letterController.text.trim().toUpperCase();
    final bobot = double.tryParse(_bobotController.text);
    final minScore = int.tryParse(_minScoreController.text);
    final maxScore = _maxScoreController.text.trim().isEmpty
        ? null
        : int.tryParse(_maxScoreController.text);

    if (bobot == null || minScore == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Bobot dan nilai minimal harus diisi'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    if (maxScore != null && maxScore < minScore) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Nilai maksimal harus lebih besar atau sama dengan nilai minimal',
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
      final data = {
        'letter': letter,
        'bobot': bobot,
        'min_score': minScore,
        'max_score': maxScore,
      };

      final result = widget.letterGrade == null
          ? await ApiService.post('/admin/system-settings/letter-grades', data)
          : await ApiService.put(
              '/admin/system-settings/letter-grades/${widget.letterGrade!['id']}',
              data,
            );

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                widget.letterGrade == null
                    ? 'Huruf mutu berhasil ditambahkan'
                    : 'Huruf mutu berhasil diperbarui',
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
              content: Text(result['message'] ?? 'Gagal menyimpan huruf mutu'),
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
          widget.letterGrade == null ? 'Tambah Huruf Mutu' : 'Edit Huruf Mutu',
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              TextFormField(
                controller: _letterController,
                decoration: const InputDecoration(
                  labelText: 'Huruf Mutu *',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.text_fields),
                  helperText: 'Contoh: A, B+, B, C, D, E',
                ),
                textCapitalization: TextCapitalization.characters,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Huruf mutu harus diisi';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _bobotController,
                decoration: const InputDecoration(
                  labelText: 'Bobot *',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.numbers),
                  helperText: 'Range: 0.00 - 4.00',
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Bobot harus diisi';
                  }
                  final bobot = double.tryParse(value);
                  if (bobot == null || bobot < 0 || bobot > 4) {
                    return 'Bobot harus antara 0.00 - 4.00';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _minScoreController,
                decoration: const InputDecoration(
                  labelText: 'Nilai Minimal *',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.trending_down),
                  helperText: 'Range: 0 - 100',
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Nilai minimal harus diisi';
                  }
                  final score = int.tryParse(value);
                  if (score == null || score < 0 || score > 100) {
                    return 'Nilai minimal harus antara 0 - 100';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _maxScoreController,
                decoration: const InputDecoration(
                  labelText: 'Nilai Maksimal (Opsional)',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.trending_up),
                  helperText: 'Kosongkan untuk nilai maksimal (100)',
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value != null && value.isNotEmpty) {
                    final score = int.tryParse(value);
                    if (score == null || score < 0 || score > 100) {
                      return 'Nilai maksimal harus antara 0 - 100';
                    }
                    final minScore = int.tryParse(_minScoreController.text);
                    if (minScore != null && score < minScore) {
                      return 'Nilai maksimal harus >= nilai minimal';
                    }
                  }
                  return null;
                },
              ),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: isSaving ? null : _saveLetterGrade,
                icon: isSaving
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(
                            Colors.white,
                          ),
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
      ),
    );
  }
}
