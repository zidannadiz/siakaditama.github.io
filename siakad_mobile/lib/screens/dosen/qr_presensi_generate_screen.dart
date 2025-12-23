import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class QrPresensiGenerateScreen extends StatefulWidget {
  final int jadwalId;

  const QrPresensiGenerateScreen({Key? key, required this.jadwalId})
    : super(key: key);

  @override
  State<QrPresensiGenerateScreen> createState() =>
      _QrPresensiGenerateScreenState();
}

class _QrPresensiGenerateScreenState extends State<QrPresensiGenerateScreen> {
  final _formKey = GlobalKey<FormState>();
  final _pertemuanController = TextEditingController();
  DateTime? _selectedDate;
  int _durationMinutes = 30;
  bool _isGenerating = false;

  @override
  void initState() {
    super.initState();
    _selectedDate = DateTime.now();
    _pertemuanController.text = '1';
  }

  @override
  void dispose() {
    _pertemuanController.dispose();
    super.dispose();
  }

  Future<void> _selectDate(BuildContext context) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate ?? DateTime.now(),
      firstDate: DateTime.now().subtract(const Duration(days: 30)),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );
    if (picked != null) {
      setState(() {
        _selectedDate = picked;
      });
    }
  }

  Future<void> _generateQrCode() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isGenerating = true;
    });

    try {
      final result = await ApiService.post(
        '/dosen/qr-presensi/generate/${widget.jadwalId}',
        {
          'pertemuan': int.parse(_pertemuanController.text),
          'tanggal': _selectedDate!.toIso8601String().split('T')[0],
          'duration_minutes': _durationMinutes,
        },
      );

      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('QR Code berhasil digenerate!'),
              backgroundColor: Colors.green,
            ),
          );
          context.push('/dosen/qr-presensi/${result['data']['token']}');
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal generate QR Code'),
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
          _isGenerating = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Generate QR Code Presensi')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              TextFormField(
                controller: _pertemuanController,
                decoration: const InputDecoration(
                  labelText: 'Pertemuan *',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.numbers),
                  helperText: 'Nomor pertemuan',
                ),
                keyboardType: TextInputType.number,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Pertemuan harus diisi';
                  }
                  final pertemuan = int.tryParse(value);
                  if (pertemuan == null || pertemuan < 1) {
                    return 'Pertemuan harus berupa angka positif';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              InkWell(
                onTap: () => _selectDate(context),
                child: InputDecorator(
                  decoration: const InputDecoration(
                    labelText: 'Tanggal *',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.calendar_today),
                  ),
                  child: Text(
                    _selectedDate != null
                        ? DateFormat(
                            'dd MMMM yyyy',
                            'id_ID',
                          ).format(_selectedDate!)
                        : 'Pilih tanggal',
                    style: TextStyle(
                      color: _selectedDate != null
                          ? Colors.black87
                          : Colors.grey[600],
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<int>(
                value: _durationMinutes,
                decoration: const InputDecoration(
                  labelText: 'Durasi (menit) *',
                  border: OutlineInputBorder(),
                  prefixIcon: Icon(Icons.timer),
                  helperText: 'Berapa lama QR Code berlaku',
                ),
                items: [15, 30, 45, 60, 90, 120].map((minutes) {
                  return DropdownMenuItem<int>(
                    value: minutes,
                    child: Text('$minutes menit'),
                  );
                }).toList(),
                onChanged: (value) {
                  if (value != null) {
                    setState(() {
                      _durationMinutes = value;
                    });
                  }
                },
              ),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: _isGenerating ? null : _generateQrCode,
                icon: _isGenerating
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
                    : const Icon(Icons.qr_code_2),
                label: const Text(
                  'Generate QR Code',
                  style: TextStyle(fontSize: 16),
                ),
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  backgroundColor: Colors.green,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
