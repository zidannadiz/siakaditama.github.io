import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:file_picker/file_picker.dart';
import 'dart:io';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../config/api_config.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;

class AssignmentDetailScreen extends StatefulWidget {
  final int assignmentId;

  const AssignmentDetailScreen({Key? key, required this.assignmentId})
      : super(key: key);

  @override
  State<AssignmentDetailScreen> createState() =>
      _AssignmentDetailScreenState();
}

class _AssignmentDetailScreenState extends State<AssignmentDetailScreen> {
  Map<String, dynamic>? assignment;
  Map<String, dynamic>? submission;
  bool isLoading = true;
  bool isSubmitting = false;
  String? errorMessage;
  final TextEditingController _jawabanController = TextEditingController();
  File? selectedFile;
  String? selectedFileName;

  @override
  void initState() {
    super.initState();
    _loadAssignment();
  }

  @override
  void dispose() {
    _jawabanController.dispose();
    super.dispose();
  }

  Future<void> _loadAssignment() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result =
          await ApiService.get('/mahasiswa/assignment/${widget.assignmentId}');
      if (result['success'] == true) {
        final data = result['data'];
        setState(() {
          assignment = data['assignment'];
          submission = data['submission'];
          if (submission != null && submission!['jawaban'] != null) {
            _jawabanController.text = submission!['jawaban'];
          }
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat tugas';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _pickFile() async {
    try {
      FilePickerResult? result = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowedExtensions: ['pdf', 'doc', 'docx', 'zip', 'rar'],
      );

      if (result != null) {
        if (result.files.single.path != null) {
          // For mobile/desktop
          setState(() {
            selectedFile = File(result.files.single.path!);
            selectedFileName = result.files.single.name;
          });
        } else if (result.files.single.bytes != null) {
          // For web - store bytes for later upload
          setState(() {
            selectedFileName = result.files.single.name;
            // Note: Web file upload with bytes needs special handling
          });
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error memilih file: ${e.toString()}'),
            backgroundColor: Colors.red,
          ),
        );
      }
    }
  }

  Future<void> _submitAssignment() async {
    if (_jawabanController.text.trim().isEmpty && selectedFile == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Masukkan jawaban atau upload file'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    setState(() {
      isSubmitting = true;
    });

    try {
      // Get token from storage
      final tokenValue = await StorageService.getToken();
      if (tokenValue == null) {
        throw Exception('Not authenticated');
      }
      
      // Create multipart request
      final uri = Uri.parse(
          '${ApiConfig.baseUrl}/mahasiswa/assignment/${widget.assignmentId}/submit');
      final request = http.MultipartRequest('POST', uri);

      // Add headers
      request.headers['Authorization'] = 'Bearer $tokenValue';
      request.headers['Accept'] = 'application/json';

      // Add jawaban
      if (_jawabanController.text.trim().isNotEmpty) {
        request.fields['jawaban'] = _jawabanController.text.trim();
      }

      // Add file if selected
      if (selectedFile != null) {
        request.files.add(
          await http.MultipartFile.fromPath('file', selectedFile!.path),
        );
      } else if (selectedFileName != null) {
        // For web platform, file handling would be different
        // This is a placeholder - web file upload needs platform-specific handling
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      final result = jsonDecode(response.body);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Tugas berhasil dikumpulkan'),
              backgroundColor: Colors.green,
            ),
          );
          _loadAssignment();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal mengumpulkan tugas'),
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
          isSubmitting = false;
        });
      }
    }
  }

  Future<void> _updateSubmission() async {
    if (_jawabanController.text.trim().isEmpty && selectedFile == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Masukkan jawaban atau upload file'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    setState(() {
      isSubmitting = true;
    });

    try {
      // Similar to submit but using PUT method
      final tokenValue = await StorageService.getToken();
      if (tokenValue == null) {
        throw Exception('Not authenticated');
      }
      
      final uri = Uri.parse(
          '${ApiConfig.baseUrl}/mahasiswa/assignment/${widget.assignmentId}/submission/${submission!['id']}');
      final request = http.MultipartRequest('PUT', uri);

      request.headers['Authorization'] = 'Bearer $tokenValue';
      request.headers['Accept'] = 'application/json';

      if (_jawabanController.text.trim().isNotEmpty) {
        request.fields['jawaban'] = _jawabanController.text.trim();
      }

      if (selectedFile != null) {
        request.files.add(
          await http.MultipartFile.fromPath('file', selectedFile!.path),
        );
      }

      final streamedResponse = await request.send();
      final response = await http.Response.fromStream(streamedResponse);
      final result = jsonDecode(response.body);

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Tugas berhasil diperbarui'),
              backgroundColor: Colors.green,
            ),
          );
          _loadAssignment();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal memperbarui tugas'),
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
          isSubmitting = false;
        });
      }
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Tugas'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadAssignment,
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
                      Icon(Icons.error_outline,
                          size: 64, color: Colors.red[300]),
                      const SizedBox(height: 16),
                      Text(
                        errorMessage!,
                        style: TextStyle(color: Colors.red[700]),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 16),
                      ElevatedButton(
                        onPressed: _loadAssignment,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : assignment == null
                  ? const Center(child: Text('Tugas tidak ditemukan'))
                  : RefreshIndicator(
                      onRefresh: _loadAssignment,
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Assignment Info Card
                            Card(
                              color: Colors.blue[50],
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      assignment!['judul'] ?? '-',
                                      style: const TextStyle(
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    Text(
                                      assignment!['deskripsi'] ?? '-',
                                      style: const TextStyle(
                                        fontSize: 14,
                                        height: 1.6,
                                      ),
                                    ),
                                    const SizedBox(height: 12),
                                    Row(
                                      children: [
                                        Icon(Icons.school,
                                            size: 16, color: Colors.grey[600]),
                                        const SizedBox(width: 8),
                                        Text(
                                          '${assignment!['mata_kuliah'] ?? '-'} (${assignment!['kode_mk'] ?? '-'})',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey[700],
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    Row(
                                      children: [
                                        Icon(Icons.person,
                                            size: 16, color: Colors.grey[600]),
                                        const SizedBox(width: 8),
                                        Text(
                                          'Dosen: ${assignment!['dosen'] ?? '-'}',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey[700],
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 8),
                                    Container(
                                      padding: const EdgeInsets.all(12),
                                      decoration: BoxDecoration(
                                        color: assignment!['is_expired'] == true
                                            ? Colors.red[100]
                                            : Colors.orange[100],
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Row(
                                        children: [
                                          Icon(
                                            Icons.access_time,
                                            color: assignment!['is_expired'] ==
                                                    true
                                                ? Colors.red[700]
                                                : Colors.orange[700],
                                          ),
                                          const SizedBox(width: 8),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                Text(
                                                  'Deadline:',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    color: Colors.grey[600],
                                                  ),
                                                ),
                                                Text(
                                                  _formatDate(
                                                      assignment!['deadline']),
                                                  style: TextStyle(
                                                    fontSize: 14,
                                                    fontWeight: FontWeight.bold,
                                                    color: assignment![
                                                                'is_expired'] ==
                                                            true
                                                        ? Colors.red[700]
                                                        : Colors.orange[700],
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),

                            // Submission Status
                            if (submission != null)
                              Card(
                                color: Colors.green[50],
                                child: Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Icon(Icons.check_circle,
                                              color: Colors.green[700]),
                                          const SizedBox(width: 8),
                                          const Text(
                                            'Sudah Dikumpulkan',
                                            style: TextStyle(
                                              fontSize: 16,
                                              fontWeight: FontWeight.bold,
                                              color: Colors.green,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 12),
                                      Text(
                                        'Dikumpulkan: ${_formatDate(submission!['submitted_at'])}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[700],
                                        ),
                                      ),
                                      if (submission!['nilai'] != null) ...[
                                        const SizedBox(height: 8),
                                        Container(
                                          padding: const EdgeInsets.all(12),
                                          decoration: BoxDecoration(
                                            color: Colors.blue[100],
                                            borderRadius:
                                                BorderRadius.circular(8),
                                          ),
                                          child: Row(
                                            children: [
                                              const Icon(Icons.grade,
                                                  color: Colors.blue),
                                              const SizedBox(width: 8),
                                              Text(
                                                'Nilai: ${submission!['nilai']}',
                                                style: const TextStyle(
                                                  fontSize: 18,
                                                  fontWeight: FontWeight.bold,
                                                  color: Colors.blue,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                      if (submission!['feedback'] != null) ...[
                                        const SizedBox(height: 8),
                                        Text(
                                          'Feedback:',
                                          style: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: Colors.grey[700],
                                          ),
                                        ),
                                        Text(
                                          submission!['feedback'],
                                          style: const TextStyle(
                                            fontSize: 14,
                                            height: 1.5,
                                          ),
                                        ),
                                      ],
                                    ],
                                  ),
                                ),
                              ),
                            const SizedBox(height: 16),

                            // Submission Form (if not submitted or can update)
                            if (submission == null ||
                                (assignment!['is_expired'] != true))
                              Card(
                                child: Padding(
                                  padding: const EdgeInsets.all(16),
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        submission == null
                                            ? 'Kumpulkan Tugas'
                                            : 'Perbarui Tugas',
                                        style: const TextStyle(
                                          fontSize: 16,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 16),
                                      TextFormField(
                                        controller: _jawabanController,
                                        decoration: InputDecoration(
                                          labelText: 'Jawaban',
                                          hintText: 'Tulis jawaban tugas...',
                                          border: OutlineInputBorder(
                                            borderRadius:
                                                BorderRadius.circular(12),
                                          ),
                                          filled: true,
                                          fillColor: Colors.grey[100],
                                        ),
                                        maxLines: 10,
                                      ),
                                      const SizedBox(height: 16),
                                      Row(
                                        children: [
                                          Expanded(
                                            child: OutlinedButton.icon(
                                              onPressed: _pickFile,
                                              icon: const Icon(Icons.attach_file),
                                              label: Text(
                                                selectedFileName ??
                                                    'Pilih File',
                                              ),
                                            ),
                                          ),
                                          if (selectedFile != null ||
                                              (submission != null &&
                                                  submission!['file_name'] !=
                                                      null))
                                            IconButton(
                                              onPressed: () {
                                                setState(() {
                                                  selectedFile = null;
                                                  selectedFileName = null;
                                                });
                                              },
                                              icon: const Icon(Icons.close),
                                              color: Colors.red,
                                            ),
                                        ],
                                      ),
                                      if (selectedFileName != null ||
                                          (submission != null &&
                                              submission!['file_name'] != null))
                                        Padding(
                                          padding: const EdgeInsets.only(top: 8),
                                          child: Text(
                                            selectedFileName ??
                                                submission!['file_name'] ??
                                                '',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey[600],
                                            ),
                                          ),
                                        ),
                                      const SizedBox(height: 16),
                                      SizedBox(
                                        width: double.infinity,
                                        child: ElevatedButton(
                                          onPressed: isSubmitting
                                              ? null
                                              : (submission == null
                                                  ? _submitAssignment
                                                  : _updateSubmission),
                                          style: ElevatedButton.styleFrom(
                                            padding: const EdgeInsets.symmetric(
                                              vertical: 16,
                                            ),
                                          ),
                                          child: isSubmitting
                                              ? const SizedBox(
                                                  height: 20,
                                                  width: 20,
                                                  child:
                                                      CircularProgressIndicator(
                                                    strokeWidth: 2,
                                                    valueColor:
                                                        AlwaysStoppedAnimation<
                                                            Color>(Colors.white),
                                                  ),
                                                )
                                              : Text(submission == null
                                                  ? 'Kumpulkan Tugas'
                                                  : 'Perbarui Tugas'),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
    );
  }
}
