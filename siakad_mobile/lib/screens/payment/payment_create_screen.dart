import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';

class PaymentCreateScreen extends StatefulWidget {
  const PaymentCreateScreen({Key? key}) : super(key: key);

  @override
  State<PaymentCreateScreen> createState() => _PaymentCreateScreenState();
}

class _PaymentCreateScreenState extends State<PaymentCreateScreen> {
  List<dynamic> banks = [];
  bool isLoadingBanks = true;
  bool isCreating = false;
  String? errorMessage;
  int? selectedBankId;
  final TextEditingController _amountController = TextEditingController();
  final TextEditingController _descriptionController = TextEditingController();
  String? selectedPaymentType;
  final List<String> paymentTypes = [
    'SPP',
    'Uang Pangkal',
    'Biaya Praktikum',
    'Biaya Ujian',
    'Lainnya',
  ];

  @override
  void initState() {
    super.initState();
    _loadBanks();
  }

  @override
  void dispose() {
    _amountController.dispose();
    _descriptionController.dispose();
    super.dispose();
  }

  Future<void> _loadBanks() async {
    setState(() {
      isLoadingBanks = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/payment/banks');
      if (result['success'] == true) {
        setState(() {
          banks = result['data'] ?? [];
          isLoadingBanks = false;
        });
      } else {
        setState(() {
          isLoadingBanks = false;
          errorMessage = result['message'] ?? 'Gagal memuat daftar bank';
        });
      }
    } catch (e) {
      setState(() {
        isLoadingBanks = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _createPayment() async {
    if (selectedBankId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pilih bank terlebih dahulu'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    if (selectedPaymentType == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pilih jenis pembayaran terlebih dahulu'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    final amount = double.tryParse(_amountController.text.trim());
    if (amount == null || amount < 1000) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Jumlah pembayaran minimal Rp 1.000'),
          backgroundColor: Colors.orange,
        ),
      );
      return;
    }

    setState(() {
      isCreating = true;
    });

    try {
      final result = await ApiService.post('/payment', {
        'bank_id': selectedBankId,
        'amount': amount,
        'payment_type': selectedPaymentType,
        'description': _descriptionController.text.trim().isEmpty
            ? null
            : _descriptionController.text.trim(),
      });

      if (result['success'] == true) {
        final paymentId = result['data']['id'];
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Pembayaran berhasil dibuat'),
              backgroundColor: Colors.green,
            ),
          );
          context.pop();
          context.push('/payment/$paymentId');
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal membuat pembayaran'),
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
          isCreating = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Buat Pembayaran')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Payment Type
            const Text(
              'Jenis Pembayaran',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            DropdownButtonFormField<String>(
              value: selectedPaymentType,
              decoration: InputDecoration(
                hintText: 'Pilih jenis pembayaran',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                filled: true,
                fillColor: Colors.grey[100],
              ),
              items: paymentTypes.map((type) {
                return DropdownMenuItem(value: type, child: Text(type));
              }).toList(),
              onChanged: (value) {
                setState(() {
                  selectedPaymentType = value;
                });
              },
            ),
            const SizedBox(height: 24),

            // Amount
            const Text(
              'Jumlah Pembayaran',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            TextFormField(
              controller: _amountController,
              decoration: InputDecoration(
                hintText: 'Masukkan jumlah (min. Rp 1.000)',
                prefixText: 'Rp ',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                filled: true,
                fillColor: Colors.grey[100],
              ),
              keyboardType: TextInputType.number,
            ),
            const SizedBox(height: 24),

            // Description
            const Text(
              'Keterangan (Opsional)',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            TextFormField(
              controller: _descriptionController,
              decoration: InputDecoration(
                hintText: 'Masukkan keterangan',
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                filled: true,
                fillColor: Colors.grey[100],
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 24),

            // Bank Selection
            const Text(
              'Pilih Bank',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            isLoadingBanks
                ? const Center(child: CircularProgressIndicator())
                : errorMessage != null
                ? Center(
                    child: Column(
                      children: [
                        Icon(
                          Icons.error_outline,
                          size: 48,
                          color: Colors.red[300],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          errorMessage!,
                          style: TextStyle(color: Colors.red[700]),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 8),
                        ElevatedButton(
                          onPressed: _loadBanks,
                          child: const Text('Coba Lagi'),
                        ),
                      ],
                    ),
                  )
                : banks.isEmpty
                ? const Center(child: Text('Tidak ada bank tersedia'))
                : ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: banks.length,
                    itemBuilder: (context, index) {
                      final bank = banks[index];
                      final isSelected = selectedBankId == bank['id'];

                      return Card(
                        margin: const EdgeInsets.symmetric(vertical: 4),
                        color: isSelected ? Colors.blue[50] : null,
                        child: ListTile(
                          leading: bank['logo'] != null
                              ? Image.network(
                                  bank['logo'],
                                  width: 40,
                                  height: 40,
                                  errorBuilder: (context, error, stackTrace) {
                                    return Icon(
                                      Icons.account_balance,
                                      color: Colors.blue[700],
                                    );
                                  },
                                )
                              : Icon(
                                  Icons.account_balance,
                                  color: Colors.blue[700],
                                ),
                          title: Text(
                            bank['name'] ?? '-',
                            style: TextStyle(
                              fontWeight: isSelected
                                  ? FontWeight.bold
                                  : FontWeight.normal,
                            ),
                          ),
                          subtitle: Text(bank['code'] ?? ''),
                          trailing: isSelected
                              ? Icon(
                                  Icons.check_circle,
                                  color: Colors.blue[700],
                                )
                              : null,
                          onTap: () {
                            setState(() {
                              selectedBankId = bank['id'];
                            });
                          },
                        ),
                      );
                    },
                  ),
            const SizedBox(height: 24),

            // Info
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.info_outline, color: Colors.blue[700]),
                      const SizedBox(width: 8),
                      Text(
                        'Informasi',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Colors.blue[700],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    '• Biaya admin: 0.5% (maks. Rp 5.000)',
                    style: TextStyle(fontSize: 12, color: Colors.blue[900]),
                  ),
                  Text(
                    '• Virtual account akan dibuat setelah Anda mengisi form',
                    style: TextStyle(fontSize: 12, color: Colors.blue[900]),
                  ),
                  Text(
                    '• Virtual account berlaku selama 24 jam',
                    style: TextStyle(fontSize: 12, color: Colors.blue[900]),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // Submit Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: isCreating ? null : _createPayment,
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
                child: isCreating
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
                    : const Text('Buat Pembayaran'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
