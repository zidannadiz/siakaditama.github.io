import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class QnADetailScreen extends StatefulWidget {
  final int questionId;

  const QnADetailScreen({Key? key, required this.questionId}) : super(key: key);

  @override
  State<QnADetailScreen> createState() => _QnADetailScreenState();
}

class _QnADetailScreenState extends State<QnADetailScreen> {
  Map<String, dynamic>? question;
  List<dynamic> answers = [];
  bool isLoading = true;
  bool isAnswering = false;
  String? errorMessage;
  final TextEditingController _answerController = TextEditingController();
  int? currentUserId;

  @override
  void initState() {
    super.initState();
    _loadCurrentUserId();
    _loadQuestion();
  }

  @override
  void dispose() {
    _answerController.dispose();
    super.dispose();
  }

  Future<void> _loadCurrentUserId() async {
    try {
      final user = await ApiService.getCurrentUser();
      if (user['success'] == true && user['data'] != null) {
        setState(() {
          currentUserId = user['data']['id'];
        });
      }
    } catch (e) {
      // Ignore error
    }
  }

  Future<void> _loadQuestion() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/qna/${widget.questionId}');
      if (result['success'] == true) {
        setState(() {
          question = result['data'];
          answers = result['data']['answers'] ?? [];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat pertanyaan';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _submitAnswer() async {
    if (_answerController.text.trim().isEmpty) {
      return;
    }

    setState(() {
      isAnswering = true;
    });

    try {
      final result = await ApiService.post('/qna/${widget.questionId}/answer', {
        'content': _answerController.text.trim(),
      });

      if (result['success'] == true) {
        _answerController.clear();
        _loadQuestion();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Jawaban berhasil dikirim'),
              backgroundColor: Colors.green,
            ),
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal mengirim jawaban'),
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
          isAnswering = false;
        });
      }
    }
  }

  Future<void> _markBestAnswer(int answerId) async {
    try {
      final result = await ApiService.post(
        '/qna/${widget.questionId}/best-answer/$answerId',
        {},
      );

      if (result['success'] == true) {
        _loadQuestion();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Jawaban terbaik telah dipilih'),
              backgroundColor: Colors.green,
            ),
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal memilih jawaban terbaik',
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
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  Color _getCategoryColor(String? category) {
    switch (category) {
      case 'akademik':
        return Colors.green;
      case 'administrasi':
        return Colors.blue;
      case 'teknologi':
        return Colors.purple;
      case 'umum':
        return Colors.orange;
      default:
        return Colors.grey;
    }
  }

  String _getCategoryLabel(String? category) {
    switch (category) {
      case 'akademik':
        return 'Akademik';
      case 'administrasi':
        return 'Administrasi';
      case 'teknologi':
        return 'Teknologi';
      case 'umum':
        return 'Umum';
      default:
        return category ?? 'Umum';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Pertanyaan'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadQuestion,
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
                    onPressed: _loadQuestion,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : question == null
          ? const Center(child: Text('Pertanyaan tidak ditemukan'))
          : Column(
              children: [
                Expanded(
                  child: RefreshIndicator(
                    onRefresh: _loadQuestion,
                    child: SingleChildScrollView(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Question Card
                          Card(
                            color: Colors.blue[50],
                            child: Padding(
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Expanded(
                                        child: Text(
                                          question!['title'] ?? '-',
                                          style: const TextStyle(
                                            fontSize: 18,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                      ),
                                      if (question!['best_answer'] != null)
                                        Icon(
                                          Icons.star,
                                          color: Colors.amber[700],
                                          size: 24,
                                        ),
                                    ],
                                  ),
                                  const SizedBox(height: 12),
                                  Text(
                                    question!['content'] ?? '-',
                                    style: const TextStyle(
                                      fontSize: 14,
                                      height: 1.6,
                                    ),
                                  ),
                                  const SizedBox(height: 12),
                                  Row(
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 8,
                                          vertical: 4,
                                        ),
                                        decoration: BoxDecoration(
                                          color: _getCategoryColor(
                                            question!['category'],
                                          ).withOpacity(0.1),
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Text(
                                          _getCategoryLabel(
                                            question!['category'],
                                          ),
                                          style: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.bold,
                                            color: _getCategoryColor(
                                              question!['category'],
                                            ),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      Text(
                                        'Oleh: ${question!['user']?['name'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      Text(
                                        _formatDate(question!['created_at']),
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[500],
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 16),

                          // Answers
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              const Text(
                                'Jawaban',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              Text(
                                '${answers.length} jawaban',
                                style: TextStyle(
                                  fontSize: 14,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          answers.isEmpty
                              ? Card(
                                  child: Padding(
                                    padding: const EdgeInsets.all(24),
                                    child: Center(
                                      child: Text(
                                        'Belum ada jawaban',
                                        style: TextStyle(
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                    ),
                                  ),
                                )
                              : ListView.builder(
                                  shrinkWrap: true,
                                  physics: const NeverScrollableScrollPhysics(),
                                  itemCount: answers.length,
                                  itemBuilder: (context, index) {
                                    final answer = answers[index];
                                    final user = answer['user'] ?? {};
                                    final isBestAnswer =
                                        answer['is_best_answer'] == true;
                                    final canMarkBest =
                                        currentUserId != null &&
                                        question!['user_id'] == currentUserId &&
                                        !isBestAnswer;

                                    return Card(
                                      margin: const EdgeInsets.symmetric(
                                        vertical: 4,
                                      ),
                                      color: isBestAnswer
                                          ? Colors.amber[50]
                                          : null,
                                      child: Padding(
                                        padding: const EdgeInsets.all(12),
                                        child: Column(
                                          crossAxisAlignment:
                                              CrossAxisAlignment.start,
                                          children: [
                                            Row(
                                              children: [
                                                CircleAvatar(
                                                  radius: 16,
                                                  backgroundColor: isBestAnswer
                                                      ? Colors.amber[100]
                                                      : Colors.blue[100],
                                                  child: Text(
                                                    (user['name'] ?? 'U')
                                                        .substring(0, 1)
                                                        .toUpperCase(),
                                                    style: TextStyle(
                                                      color: isBestAnswer
                                                          ? Colors.amber[900]
                                                          : Colors.blue[700],
                                                      fontSize: 12,
                                                    ),
                                                  ),
                                                ),
                                                const SizedBox(width: 8),
                                                Expanded(
                                                  child: Column(
                                                    crossAxisAlignment:
                                                        CrossAxisAlignment
                                                            .start,
                                                    children: [
                                                      Row(
                                                        children: [
                                                          Text(
                                                            user['name'] ?? '-',
                                                            style:
                                                                const TextStyle(
                                                                  fontWeight:
                                                                      FontWeight
                                                                          .bold,
                                                                ),
                                                          ),
                                                          if (isBestAnswer)
                                                            Padding(
                                                              padding:
                                                                  const EdgeInsets.only(
                                                                    left: 8,
                                                                  ),
                                                              child: Icon(
                                                                Icons.star,
                                                                size: 16,
                                                                color: Colors
                                                                    .amber[700],
                                                              ),
                                                            ),
                                                        ],
                                                      ),
                                                      Text(
                                                        _formatDate(
                                                          answer['created_at'],
                                                        ),
                                                        style: TextStyle(
                                                          fontSize: 10,
                                                          color:
                                                              Colors.grey[600],
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                                if (canMarkBest)
                                                  TextButton.icon(
                                                    onPressed: () =>
                                                        _markBestAnswer(
                                                          answer['id'],
                                                        ),
                                                    icon: const Icon(
                                                      Icons.star_border,
                                                      size: 16,
                                                    ),
                                                    label: const Text(
                                                      'Jawaban Terbaik',
                                                      style: TextStyle(
                                                        fontSize: 12,
                                                      ),
                                                    ),
                                                  ),
                                              ],
                                            ),
                                            const SizedBox(height: 8),
                                            Text(
                                              answer['content'] ?? '-',
                                              style: const TextStyle(
                                                fontSize: 14,
                                                height: 1.5,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    );
                                  },
                                ),
                        ],
                      ),
                    ),
                  ),
                ),

                // Answer Input
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    boxShadow: [
                      BoxShadow(
                        color: Colors.grey[300]!,
                        blurRadius: 4,
                        offset: const Offset(0, -2),
                      ),
                    ],
                  ),
                  child: Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _answerController,
                          decoration: InputDecoration(
                            hintText: 'Tulis jawaban...',
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(24),
                            ),
                            filled: true,
                            fillColor: Colors.grey[100],
                            contentPadding: const EdgeInsets.symmetric(
                              horizontal: 16,
                              vertical: 12,
                            ),
                          ),
                          maxLines: null,
                          textInputAction: TextInputAction.send,
                          onSubmitted: (_) => _submitAnswer(),
                        ),
                      ),
                      const SizedBox(width: 8),
                      IconButton(
                        onPressed: isAnswering ? null : _submitAnswer,
                        icon: isAnswering
                            ? const SizedBox(
                                width: 24,
                                height: 24,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                ),
                              )
                            : Icon(
                                Icons.send,
                                color: isAnswering
                                    ? Colors.grey
                                    : Colors.blue[700],
                              ),
                        tooltip: 'Kirim',
                      ),
                    ],
                  ),
                ),
              ],
            ),
    );
  }
}
