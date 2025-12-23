import 'dart:async';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../services/api_service.dart';

class QrPresensiShowScreen extends StatefulWidget {
  final String token;

  const QrPresensiShowScreen({Key? key, required this.token})
      : super(key: key);

  @override
  State<QrPresensiShowScreen> createState() => _QrPresensiShowScreenState();
}

class _QrPresensiShowScreenState extends State<QrPresensiShowScreen> {
  Map<String, dynamic>? qrSession;
  bool isLoading = true;
  String? errorMessage;
  Timer? _timer;
  int _expiresInSeconds = 0;

  @override
  void initState() {
    super.initState();
    _loadQrSession();
    _startTimer();
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  void _startTimer() {
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (qrSession != null && _expiresInSeconds > 0) {
        setState(() {
          _expiresInSeconds--;
        });
        if (_expiresInSeconds <= 0) {
          _loadQrSession();
        }
      }
    });
  }

  Future<void> _loadQrSession() async {
    try {
      final result = await ApiService.get('/dosen/qr-presensi/${widget.token}');
      if (result['success'] == true) {
        setState(() {
          qrSession = result['data'];
          _expiresInSeconds = result['data']['expires_in_seconds'] ?? 0;
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat QR Code';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _stopQrSession() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Stop QR Code'),
        content: const Text(
          'Apakah Anda yakin ingin menghentikan QR Code ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Stop'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    try {
      final result = await ApiService.post(
        '/dosen/qr-presensi/${widget.token}/stop',
        {},
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('QR Code telah dihentikan'),
              backgroundColor: Colors.orange,
            ),
          );
          context.pop();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal menghentikan QR Code'),
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

  String _formatDuration(int seconds) {
    final minutes = seconds ~/ 60;
    final secs = seconds % 60;
    return '${minutes.toString().padLeft(2, '0')}:${secs.toString().padLeft(2, '0')}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('QR Code Presensi'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadQrSession,
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
                        onPressed: _loadQrSession,
                        child: const Text('Coba Lagi'),
                      ),
                    ],
                  ),
                )
              : qrSession == null
                  ? const Center(child: Text('QR Code tidak ditemukan'))
                  : RefreshIndicator(
                      onRefresh: _loadQrSession,
                      child: SingleChildScrollView(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          children: [
                            // Info Card
                            Card(
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      qrSession!['jadwal_kuliah']['mata_kuliah']
                                          ['nama'],
                                      style: const TextStyle(
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    _buildInfoRow(
                                      Icons.numbers,
                                      'Pertemuan',
                                      '${qrSession!['pertemuan']}',
                                    ),
                                    const SizedBox(height: 4),
                                    _buildInfoRow(
                                      Icons.calendar_today,
                                      'Tanggal',
                                      qrSession!['tanggal'],
                                    ),
                                    const SizedBox(height: 4),
                                    _buildInfoRow(
                                      Icons.room,
                                      'Ruangan',
                                      qrSession!['jadwal_kuliah']['ruangan'] ??
                                          '-',
                                    ),
                                  ],
                                ),
                              ),
                            ),
                            const SizedBox(height: 24),

                            // QR Code
                            Card(
                              elevation: 4,
                              child: Padding(
                                padding: const EdgeInsets.all(24),
                                child: Column(
                                  children: [
                                    QrImageView(
                                      data: widget.token,
                                      version: QrVersions.auto,
                                      size: 250.0,
                                      backgroundColor: Colors.white,
                                    ),
                                    const SizedBox(height: 16),
                                    if (_expiresInSeconds > 0)
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 16,
                                          vertical: 8,
                                        ),
                                        decoration: BoxDecoration(
                                          color: _expiresInSeconds < 60
                                              ? Colors.red[50]
                                              : Colors.green[50],
                                          borderRadius:
                                              BorderRadius.circular(20),
                                        ),
                                        child: Row(
                                          mainAxisSize: MainAxisSize.min,
                                          children: [
                                            Icon(
                                              Icons.timer,
                                              size: 16,
                                              color: _expiresInSeconds < 60
                                                  ? Colors.red[700]
                                                  : Colors.green[700],
                                            ),
                                            const SizedBox(width: 8),
                                            Text(
                                              'Berlaku: ${_formatDuration(_expiresInSeconds)}',
                                              style: TextStyle(
                                                fontSize: 14,
                                                fontWeight: FontWeight.bold,
                                                color: _expiresInSeconds < 60
                                                    ? Colors.red[700]
                                                    : Colors.green[700],
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

                            // Stop Button
                            ElevatedButton.icon(
                              onPressed: _stopQrSession,
                              icon: const Icon(Icons.stop),
                              label: const Text('Stop QR Code'),
                              style: ElevatedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 16,
                                ),
                                backgroundColor: Colors.red,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
    );
  }

  Widget _buildInfoRow(IconData icon, String label, String value) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey[700],
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}
