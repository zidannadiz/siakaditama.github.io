import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../../services/api_service.dart';

class QrPresensiScanScreen extends StatefulWidget {
  const QrPresensiScanScreen({Key? key}) : super(key: key);

  @override
  State<QrPresensiScanScreen> createState() => _QrPresensiScanScreenState();
}

class _QrPresensiScanScreenState extends State<QrPresensiScanScreen> {
  final MobileScannerController _controller = MobileScannerController();
  bool _isProcessing = false;
  String? _lastScannedToken;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _handleScan(String? token) async {
    if (token == null || token.isEmpty) return;
    if (_isProcessing) return;
    if (_lastScannedToken == token) return; // Prevent duplicate scans

    setState(() {
      _isProcessing = true;
      _lastScannedToken = token;
    });

    try {
      final result = await ApiService.post(
        '/mahasiswa/qr-presensi/scan',
        {'token': token},
      );

      if (result['success'] == true) {
        if (mounted) {
          _controller.stop();
          await showDialog(
            context: context,
            barrierDismissible: false,
            builder: (context) => AlertDialog(
              title: const Row(
                children: [
                  Icon(Icons.check_circle, color: Colors.green),
                  SizedBox(width: 8),
                  Text('Presensi Berhasil!'),
                ],
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('Pertemuan: ${result['data']['pertemuan']}'),
                  Text('Tanggal: ${result['data']['tanggal']}'),
                  Text('Status: ${result['data']['status']}'),
                  if (result['data']['jadwal_kuliah'] != null)
                    Text(
                      'Mata Kuliah: ${result['data']['jadwal_kuliah']['mata_kuliah']['nama']}',
                    ),
                ],
              ),
              actions: [
                ElevatedButton(
                  onPressed: () {
                    Navigator.pop(context);
                    context.pop();
                  },
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
                  child: const Text('OK'),
                ),
              ],
            ),
          );
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal melakukan presensi'),
              backgroundColor: Colors.red,
            ),
          );
          // Reset untuk scan ulang
          setState(() {
            _lastScannedToken = null;
          });
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
        // Reset untuk scan ulang
        setState(() {
          _lastScannedToken = null;
        });
      }
    } finally {
      if (mounted) {
        setState(() {
          _isProcessing = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Scan QR Code Presensi'),
      ),
      body: Stack(
        children: [
          MobileScanner(
            controller: _controller,
            onDetect: (capture) {
              final List<Barcode> barcodes = capture.barcodes;
              for (final barcode in barcodes) {
                if (barcode.rawValue != null) {
                  _handleScan(barcode.rawValue);
                  break;
                }
              }
            },
          ),
          if (_isProcessing)
            Container(
              color: Colors.black54,
              child: const Center(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    CircularProgressIndicator(),
                    SizedBox(height: 16),
                    Text(
                      'Memproses presensi...',
                      style: TextStyle(color: Colors.white),
                    ),
                  ],
                ),
              ),
            ),
          // Overlay dengan instruksi
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            child: Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.transparent,
                    Colors.black.withOpacity(0.8),
                  ],
                ),
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(
                    Icons.qr_code_scanner,
                    size: 48,
                    color: Colors.white,
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Arahkan kamera ke QR Code',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Pastikan QR Code berada di dalam frame',
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 14,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
