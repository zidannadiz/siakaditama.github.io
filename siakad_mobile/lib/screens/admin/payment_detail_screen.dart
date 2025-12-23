import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class PaymentDetailScreen extends StatefulWidget {
  final int paymentId;

  const PaymentDetailScreen({Key? key, required this.paymentId})
    : super(key: key);

  @override
  State<PaymentDetailScreen> createState() => _PaymentDetailScreenState();
}

class _PaymentDetailScreenState extends State<PaymentDetailScreen> {
  Map<String, dynamic>? payment;
  bool isLoading = true;
  bool isProcessing = false;
  String? errorMessage;
  final TextEditingController _notesController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadPayment();
  }

  @override
  void dispose() {
    _notesController.dispose();
    super.dispose();
  }

  Future<void> _loadPayment() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/payment/${widget.paymentId}');
      if (result['success'] == true) {
        setState(() {
          payment = result['data'];
          if (payment != null && payment!['notes'] != null) {
            _notesController.text = payment!['notes'];
          }
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data pembayaran';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _verifyPayment() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Verifikasi Pembayaran'),
        content: const Text(
          'Apakah Anda yakin ingin memverifikasi pembayaran ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
            child: const Text('Verifikasi'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    // Show notes input dialog
    final notes = await showDialog<String>(
      context: context,
      builder: (context) {
        final controller = TextEditingController();
        return AlertDialog(
          title: const Text('Catatan Verifikasi'),
          content: TextField(
            controller: controller,
            decoration: const InputDecoration(
              hintText: 'Masukkan catatan (opsional)',
              border: OutlineInputBorder(),
            ),
            maxLines: 3,
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context, null),
              child: const Text('Batal'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(context, controller.text),
              style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
              child: const Text('Verifikasi'),
            ),
          ],
        );
      },
    );

    if (notes == null && payment!['status'] != 'pending') return;

    setState(() {
      isProcessing = true;
    });

    try {
      final result = await ApiService.post(
        '/admin/payment/${widget.paymentId}/verify',
        {'notes': notes ?? ''},
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Pembayaran berhasil diverifikasi'),
              backgroundColor: Colors.green,
            ),
          );
          _loadPayment();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal memverifikasi pembayaran',
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
          isProcessing = false;
        });
      }
    }
  }

  Future<void> _cancelPayment() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Batalkan Pembayaran'),
        content: const Text(
          'Apakah Anda yakin ingin membatalkan pembayaran ini?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Batalkan'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    setState(() {
      isProcessing = true;
    });

    try {
      final result = await ApiService.post(
        '/admin/payment/${widget.paymentId}/cancel',
        {},
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Pembayaran berhasil dibatalkan'),
              backgroundColor: Colors.orange,
            ),
          );
          _loadPayment();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                result['message'] ?? 'Gagal membatalkan pembayaran',
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
          isProcessing = false;
        });
      }
    }
  }

  Color _getStatusColor(String? status) {
    switch (status) {
      case 'paid':
        return Colors.green;
      case 'pending':
        return Colors.orange;
      case 'expired':
        return Colors.red;
      case 'cancelled':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  String _getStatusLabel(String? status) {
    switch (status) {
      case 'paid':
        return 'Lunas';
      case 'pending':
        return 'Pending';
      case 'expired':
        return 'Kedaluwarsa';
      case 'cancelled':
        return 'Dibatalkan';
      default:
        return 'Unknown';
    }
  }

  String _formatCurrency(dynamic amount) {
    if (amount == null) return 'Rp 0';
    try {
      final formatter = NumberFormat.currency(
        locale: 'id_ID',
        symbol: 'Rp ',
        decimalDigits: 0,
      );
      return formatter.format(double.parse(amount.toString()));
    } catch (e) {
      return 'Rp ${amount.toString()}';
    }
  }

  String _formatDate(String? dateString) {
    if (dateString == null) return '-';
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
        title: const Text('Detail Pembayaran'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadPayment,
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
                    onPressed: _loadPayment,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : payment == null
          ? const Center(child: Text('Pembayaran tidak ditemukan'))
          : RefreshIndicator(
              onRefresh: _loadPayment,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Status Card
                    Card(
                      color: _getStatusColor(
                        payment!['status'],
                      ).withOpacity(0.1),
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Row(
                          children: [
                            Icon(
                              Icons.payment,
                              color: _getStatusColor(payment!['status']),
                              size: 32,
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    payment!['invoice_number'] ?? '-',
                                    style: const TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 8,
                                      vertical: 4,
                                    ),
                                    decoration: BoxDecoration(
                                      color: _getStatusColor(
                                        payment!['status'],
                                      ).withOpacity(0.2),
                                      borderRadius: BorderRadius.circular(12),
                                    ),
                                    child: Text(
                                      _getStatusLabel(payment!['status']),
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: _getStatusColor(
                                          payment!['status'],
                                        ),
                                      ),
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

                    // User Info
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Informasi User',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.person,
                              'Nama',
                              payment!['user']['name'] ?? '-',
                            ),
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.email,
                              'Email',
                              payment!['user']['email'] ?? '-',
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Payment Info
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Informasi Pembayaran',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.account_balance,
                              'Bank',
                              payment!['bank']['name'] ?? '-',
                            ),
                            if (payment!['bank']['account_number'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.numbers,
                                'No. Rekening',
                                payment!['bank']['account_number'] ?? '-',
                              ),
                            ],
                            if (payment!['virtual_account'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.qr_code,
                                'Virtual Account',
                                payment!['virtual_account'] ?? '-',
                              ),
                            ],
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.attach_money,
                              'Jumlah',
                              _formatCurrency(payment!['amount']),
                            ),
                            if (payment!['fee'] != null &&
                                payment!['fee'] > 0) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.receipt,
                                'Biaya',
                                _formatCurrency(payment!['fee']),
                              ),
                            ],
                            const SizedBox(height: 8),
                            _buildInfoRow(
                              Icons.payments,
                              'Total',
                              _formatCurrency(payment!['total_amount']),
                              isBold: true,
                            ),
                            if (payment!['description'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.description,
                                'Deskripsi',
                                payment!['description'] ?? '-',
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Dates Info
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Tanggal',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _buildInfoRow(
                              Icons.calendar_today,
                              'Dibuat',
                              _formatDate(payment!['created_at']),
                            ),
                            if (payment!['expired_at'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.timer_off,
                                'Kedaluwarsa',
                                _formatDate(payment!['expired_at']),
                              ),
                            ],
                            if (payment!['paid_at'] != null) ...[
                              const SizedBox(height: 8),
                              _buildInfoRow(
                                Icons.check_circle,
                                'Dibayar',
                                _formatDate(payment!['paid_at']),
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),

                    // Notes
                    if (payment!['notes'] != null &&
                        payment!['notes'].toString().isNotEmpty) ...[
                      const SizedBox(height: 16),
                      Card(
                        color: Colors.blue[50],
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Catatan',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                payment!['notes'],
                                style: const TextStyle(fontSize: 14),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],

                    // Action Buttons
                    if (payment!['status'] == 'pending') ...[
                      const SizedBox(height: 16),
                      Row(
                        children: [
                          Expanded(
                            child: ElevatedButton.icon(
                              onPressed: isProcessing ? null : _verifyPayment,
                              icon: const Icon(Icons.check),
                              label: const Text('Verifikasi'),
                              style: ElevatedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 16,
                                ),
                                backgroundColor: Colors.green,
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: ElevatedButton.icon(
                              onPressed: isProcessing ? null : _cancelPayment,
                              icon: const Icon(Icons.close),
                              label: const Text('Batalkan'),
                              style: ElevatedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 16,
                                ),
                                backgroundColor: Colors.red,
                              ),
                            ),
                          ),
                        ],
                      ),
                      if (isProcessing)
                        const Padding(
                          padding: EdgeInsets.only(top: 16),
                          child: Center(child: CircularProgressIndicator()),
                        ),
                    ],
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildInfoRow(
    IconData icon,
    String label,
    String value, {
    bool isBold = false,
  }) {
    return Row(
      children: [
        Icon(icon, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Text(
          '$label: ',
          style: TextStyle(fontSize: 14, color: Colors.grey[700]),
        ),
        Expanded(
          child: Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
            ),
          ),
        ),
      ],
    );
  }
}
