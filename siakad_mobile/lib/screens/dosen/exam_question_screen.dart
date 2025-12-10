import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class ExamQuestionScreen extends StatefulWidget {
  final int examId;
  final int? questionId; // For edit mode

  const ExamQuestionScreen({
    Key? key,
    required this.examId,
    this.questionId,
  }) : super(key: key);

  @override
  State<ExamQuestionScreen> createState() => _ExamQuestionScreenState();
}

class _ExamQuestionScreenState extends State<ExamQuestionScreen> {
  final _formKey = GlobalKey<FormState>();
  final _pertanyaanController = TextEditingController();
  final _bobotController = TextEditingController();
  final _penjelasanController = TextEditingController();
  final _jawabanBenarEssayController = TextEditingController();

  String _tipe = 'pilgan';
  Map<String, String> _pilihan = {
    'A': '',
    'B': '',
    'C': '',
    'D': '',
  };
  String _jawabanBenar = 'A';
  bool isLoading = false;
  bool isEditMode = false;

  @override
  void initState() {
    super.initState();
    isEditMode = widget.questionId != null;
    if (isEditMode) {
      _loadQuestion();
    }
  }

  @override
  void dispose() {
    _pertanyaanController.dispose();
    _bobotController.dispose();
    _penjelasanController.dispose();
    _jawabanBenarEssayController.dispose();
    super.dispose();
  }

  Future<void> _loadQuestion() async {
    if (widget.questionId == null) return;

    setState(() {
      isLoading = true;
    });

    try {
      final result =
          await ApiService.get('/dosen/exam/${widget.examId}');
      if (result['success'] == true) {
        final questions = result['data']['questions'] ?? [];
        final question = questions.firstWhere(
          (q) => q['id'] == widget.questionId,
          orElse: () => null,
        );

        if (question != null) {
          setState(() {
            _pertanyaanController.text = question['pertanyaan'] ?? '';
            _bobotController.text = question['bobot']?.toString() ?? '0';
            _penjelasanController.text = question['penjelasan'] ?? '';
            _tipe = question['tipe'] ?? 'pilgan';
            _jawabanBenar = question['jawaban_benar'] ?? 'A';
            _jawabanBenarEssayController.text =
                question['jawaban_benar_essay'] ?? '';

            if (question['pilihan'] != null) {
              final pilihan = question['pilihan'] as Map<String, dynamic>;
              _pilihan = Map<String, String>.from(
                pilihan.map((key, value) => MapEntry(key, value.toString())),
              );
            }

            isLoading = false;
          });
        }
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

  Future<void> _saveQuestion() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    if (_tipe == 'pilgan') {
      // Validate pilihan
      bool hasEmpty = _pilihan.values.any((value) => value.trim().isEmpty);
      if (hasEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Semua pilihan harus diisi'),
            backgroundColor: Colors.orange,
          ),
        );
        return;
      }
    }

    setState(() {
      isLoading = true;
    });

    try {
      final data = {
        'tipe': _tipe,
        'pertanyaan': _pertanyaanController.text.trim(),
        'bobot': double.parse(_bobotController.text),
        'penjelasan': _penjelasanController.text.trim(),
        if (_tipe == 'pilgan') 'pilihan': _pilihan,
        if (_tipe == 'pilgan') 'jawaban_benar': _jawabanBenar,
        if (_tipe == 'essay')
          'jawaban_benar_essay': _jawabanBenarEssayController.text.trim(),
      };

      final result = isEditMode
          ? await ApiService.put(
              '/dosen/exam/${widget.examId}/question/${widget.questionId}',
              data)
          : await ApiService.post('/dosen/exam/${widget.examId}/question', data);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(isEditMode
                  ? 'Soal berhasil diperbarui'
                  : 'Soal berhasil ditambahkan'),
              backgroundColor: Colors.green,
            ),
          );
          context.pop(true);
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menyimpan soal'),
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
        title: Text(isEditMode ? 'Edit Soal' : 'Tambah Soal'),
      ),
      body: isLoading && isEditMode
          ? const Center(child: CircularProgressIndicator())
          : Form(
              key: _formKey,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    DropdownButtonFormField<String>(
                      value: _tipe,
                      decoration: InputDecoration(
                        labelText: 'Tipe Soal *',
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
                      controller: _pertanyaanController,
                      decoration: InputDecoration(
                        labelText: 'Pertanyaan *',
                        hintText: 'Masukkan pertanyaan',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      maxLines: 5,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Pertanyaan wajib diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    if (_tipe == 'pilgan') ...[
                      const Text(
                        'Pilihan Jawaban *',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      ..._pilihan.entries.map((entry) {
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 8),
                          child: Row(
                            children: [
                              Container(
                                width: 40,
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.blue[100],
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Text(
                                  entry.key,
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: Colors.blue[900],
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: TextFormField(
                                  initialValue: entry.value,
                                  decoration: InputDecoration(
                                    hintText: 'Masukkan pilihan ${entry.key}',
                                    border: OutlineInputBorder(
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    filled: true,
                                    fillColor: Colors.grey[100],
                                  ),
                                  onChanged: (value) {
                                    setState(() {
                                      _pilihan[entry.key] = value;
                                    });
                                  },
                                ),
                              ),
                            ],
                          ),
                        );
                      }),
                      const SizedBox(height: 16),
                      DropdownButtonFormField<String>(
                        value: _jawabanBenar,
                        decoration: InputDecoration(
                          labelText: 'Jawaban Benar *',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          filled: true,
                          fillColor: Colors.grey[100],
                        ),
                        items: _pilihan.keys.map((key) {
                          return DropdownMenuItem(
                            value: key,
                            child: Text('$key: ${_pilihan[key] ?? ""}'),
                          );
                        }).toList(),
                        onChanged: (value) {
                          if (value != null) {
                            setState(() {
                              _jawabanBenar = value;
                            });
                          }
                        },
                      ),
                    ] else if (_tipe == 'essay') ...[
                      TextFormField(
                        controller: _jawabanBenarEssayController,
                        decoration: InputDecoration(
                          labelText: 'Jawaban Benar (Opsional)',
                          hintText: 'Masukkan jawaban benar untuk referensi',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          filled: true,
                          fillColor: Colors.grey[100],
                        ),
                        maxLines: 5,
                      ),
                    ],
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _bobotController,
                      decoration: InputDecoration(
                        labelText: 'Bobot *',
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
                        if (bobot == null || bobot < 0) {
                          return 'Bobot harus angka positif';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      controller: _penjelasanController,
                      decoration: InputDecoration(
                        labelText: 'Penjelasan',
                        hintText: 'Masukkan penjelasan jawaban',
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        filled: true,
                        fillColor: Colors.grey[100],
                      ),
                      maxLines: 3,
                    ),
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: isLoading ? null : _saveQuestion,
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

