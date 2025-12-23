import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class PaymentStatisticsScreen extends StatefulWidget {
  const PaymentStatisticsScreen({Key? key}) : super(key: key);

  @override
  State<PaymentStatisticsScreen> createState() =>
      _PaymentStatisticsScreenState();
}

class _PaymentStatisticsScreenState extends State<PaymentStatisticsScreen> {
  Map<String, dynamic>? statistics;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadStatistics();
  }

  Future<void> _loadStatistics() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/payment/statistics');
      if (result['success'] == true) {
        setState(() {
          statistics = result['data'];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat statistik';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
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
        return Colors.blue;
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
        return status ?? 'Unknown';
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Statistik Pembayaran'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadStatistics,
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
                    onPressed: _loadStatistics,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : statistics == null
          ? const Center(child: Text('Data tidak tersedia'))
          : RefreshIndicator(
              onRefresh: _loadStatistics,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Today Stats
                    Card(
                      color: Colors.blue[50],
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.today, color: Colors.blue[700]),
                                const SizedBox(width: 8),
                                const Text(
                                  'Hari Ini',
                                  style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                Expanded(
                                  child: _buildStatItem(
                                    'Total',
                                    '${statistics!['today']['count'] ?? 0}',
                                    Colors.blue,
                                  ),
                                ),
                                Expanded(
                                  child: _buildStatItem(
                                    'Lunas',
                                    '${statistics!['today']['paid'] ?? 0}',
                                    Colors.green,
                                  ),
                                ),
                                Expanded(
                                  child: _buildStatItem(
                                    'Jumlah',
                                    _formatCurrency(
                                      statistics!['today']['amount'],
                                    ),
                                    Colors.orange,
                                    isCurrency: true,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // This Month Stats
                    Card(
                      color: Colors.green[50],
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(
                                  Icons.calendar_month,
                                  color: Colors.green[700],
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Bulan Ini (${DateFormat('MMMM yyyy', 'id_ID').format(DateTime.now())})',
                                  style: const TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                Expanded(
                                  child: _buildStatItem(
                                    'Total',
                                    '${statistics!['this_month']['count'] ?? 0}',
                                    Colors.green,
                                  ),
                                ),
                                Expanded(
                                  child: _buildStatItem(
                                    'Jumlah Total',
                                    _formatCurrency(
                                      statistics!['this_month']['amount'],
                                    ),
                                    Colors.blue,
                                    isCurrency: true,
                                  ),
                                ),
                                Expanded(
                                  child: _buildStatItem(
                                    'Lunas',
                                    _formatCurrency(
                                      statistics!['this_month']['paid_amount'] ??
                                          0,
                                    ),
                                    Colors.green,
                                    isCurrency: true,
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // By Status
                    Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Berdasarkan Status',
                              style: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            if (statistics!['by_status'] != null)
                              ...(statistics!['by_status'] as List).map((item) {
                                return Padding(
                                  padding: const EdgeInsets.only(bottom: 8),
                                  child: Row(
                                    children: [
                                      Container(
                                        width: 12,
                                        height: 12,
                                        decoration: BoxDecoration(
                                          color: _getStatusColor(
                                            item['status'],
                                          ),
                                          shape: BoxShape.circle,
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          _getStatusLabel(item['status']),
                                          style: const TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.w600,
                                          ),
                                        ),
                                      ),
                                      Text(
                                        '${item['total'] ?? 0}',
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(width: 16),
                                      Text(
                                        _formatCurrency(item['amount']),
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.green[700],
                                        ),
                                      ),
                                    ],
                                  ),
                                );
                              }).toList(),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // By Bank
                    if (statistics!['by_bank'] != null &&
                        (statistics!['by_bank'] as List).isNotEmpty) ...[
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Berdasarkan Bank',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 12),
                              ...(statistics!['by_bank'] as List).map((item) {
                                return Padding(
                                  padding: const EdgeInsets.only(bottom: 8),
                                  child: Row(
                                    children: [
                                      const Icon(
                                        Icons.account_balance,
                                        size: 20,
                                      ),
                                      const SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          item['name'] ?? '-',
                                          style: const TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.w600,
                                          ),
                                        ),
                                      ),
                                      Text(
                                        '${item['total'] ?? 0}',
                                        style: const TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(width: 16),
                                      Text(
                                        _formatCurrency(item['amount']),
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.green[700],
                                        ),
                                      ),
                                    ],
                                  ),
                                );
                              }).toList(),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ),
    );
  }

  Color _darkenColor(Color color) {
    return Color.fromRGBO(
      (color.red * 0.7).round(),
      (color.green * 0.7).round(),
      (color.blue * 0.7).round(),
      1.0,
    );
  }

  Widget _buildStatItem(
    String label,
    String value,
    Color color, {
    bool isCurrency = false,
  }) {
    return Column(
      children: [
        Text(
          value,
          style: TextStyle(
            fontSize: isCurrency ? 14 : 20,
            fontWeight: FontWeight.bold,
            color: _darkenColor(color),
          ),
          textAlign: TextAlign.center,
        ),
        Text(
          label,
          style: TextStyle(fontSize: 12, color: Colors.grey[700]),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }
}
