import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class PaymentListScreen extends StatefulWidget {
  const PaymentListScreen({Key? key}) : super(key: key);

  @override
  State<PaymentListScreen> createState() => _PaymentListScreenState();
}

class _PaymentListScreenState extends State<PaymentListScreen> {
  List<dynamic> payments = [];
  Map<String, dynamic>? statistics;
  bool isLoading = true;
  String? errorMessage;
  int currentPage = 1;
  int lastPage = 1;
  int total = 0;
  String? selectedStatus;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadPayments();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadPayments({int page = 1, String? status}) async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      String url = '/admin/payment?page=$page';
      if (status != null && status.isNotEmpty) {
        url += '&status=$status';
      }
      if (_searchController.text.isNotEmpty) {
        url += '&search=${_searchController.text}';
      }

      final result = await ApiService.get(url);
      if (result['success'] == true) {
        setState(() {
          payments = result['data']['payments'] ?? [];
          statistics = result['data']['statistics'];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
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
    if (dateString == null) return '';
    try {
      final date = DateTime.parse(dateString);
      return DateFormat('dd MMM yyyy, HH:mm', 'id_ID').format(date);
    } catch (e) {
      return dateString;
    }
  }

  Color _darkenColor(Color color) {
    return Color.fromRGBO(
      (color.red * 0.7).round(),
      (color.green * 0.7).round(),
      (color.blue * 0.7).round(),
      1.0,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Manajemen Pembayaran'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () =>
                _loadPayments(page: currentPage, status: selectedStatus),
            tooltip: 'Refresh',
          ),
          IconButton(
            icon: const Icon(Icons.bar_chart),
            onPressed: () => context.push('/admin/payment/statistics'),
            tooltip: 'Statistik',
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
                    onPressed: () => _loadPayments(
                      page: currentPage,
                      status: selectedStatus,
                    ),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Statistics Cards
                if (statistics != null)
                  Container(
                    padding: const EdgeInsets.all(8),
                    color: Colors.blue[50],
                    child: Row(
                      children: [
                        Expanded(
                          child: _buildStatCard(
                            'Total',
                            '${statistics!['total'] ?? 0}',
                            Colors.blue,
                            Icons.payments,
                          ),
                        ),
                        Expanded(
                          child: _buildStatCard(
                            'Pending',
                            '${statistics!['pending'] ?? 0}',
                            Colors.orange,
                            Icons.pending,
                          ),
                        ),
                        Expanded(
                          child: _buildStatCard(
                            'Lunas',
                            '${statistics!['paid'] ?? 0}',
                            Colors.green,
                            Icons.check_circle,
                          ),
                        ),
                      ],
                    ),
                  ),

                // Search & Filter
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _searchController,
                          decoration: InputDecoration(
                            hintText: 'Cari invoice/user...',
                            prefixIcon: const Icon(Icons.search),
                            suffixIcon: _searchController.text.isNotEmpty
                                ? IconButton(
                                    icon: const Icon(Icons.clear),
                                    onPressed: () {
                                      _searchController.clear();
                                      _loadPayments();
                                    },
                                  )
                                : null,
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                            contentPadding: const EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 8,
                            ),
                          ),
                          onSubmitted: (value) {
                            _loadPayments(page: 1, status: selectedStatus);
                          },
                        ),
                      ),
                      const SizedBox(width: 8),
                      PopupMenuButton<String>(
                        icon: const Icon(Icons.filter_list),
                        onSelected: (value) {
                          setState(() {
                            selectedStatus = value == 'all' ? null : value;
                          });
                          _loadPayments(page: 1, status: selectedStatus);
                        },
                        itemBuilder: (context) => [
                          const PopupMenuItem(
                            value: 'all',
                            child: Text('Semua Status'),
                          ),
                          const PopupMenuItem(
                            value: 'pending',
                            child: Text('Pending'),
                          ),
                          const PopupMenuItem(
                            value: 'paid',
                            child: Text('Lunas'),
                          ),
                          const PopupMenuItem(
                            value: 'expired',
                            child: Text('Kedaluwarsa'),
                          ),
                          const PopupMenuItem(
                            value: 'cancelled',
                            child: Text('Dibatalkan'),
                          ),
                        ],
                        child: Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.grey),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: const Icon(Icons.filter_list),
                        ),
                      ),
                    ],
                  ),
                ),

                // List
                Expanded(
                  child: payments.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.payment_outlined,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada data pembayaran',
                                style: TextStyle(
                                  fontSize: 16,
                                  color: Colors.grey[600],
                                ),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () => _loadPayments(
                            page: currentPage,
                            status: selectedStatus,
                          ),
                          child: ListView.builder(
                            padding: const EdgeInsets.all(8),
                            itemCount: payments.length,
                            itemBuilder: (context, index) {
                              final payment = payments[index];
                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 8,
                                  vertical: 4,
                                ),
                                child: ListTile(
                                  leading: CircleAvatar(
                                    backgroundColor: _getStatusColor(
                                      payment['status'],
                                    ).withOpacity(0.2),
                                    child: Icon(
                                      Icons.payment,
                                      color: _getStatusColor(payment['status']),
                                    ),
                                  ),
                                  title: Text(
                                    payment['invoice_number'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 4),
                                      Text(
                                        'User: ${payment['user']['name'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      Text(
                                        'Bank: ${payment['bank']['name'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      Text(
                                        'Jumlah: ${_formatCurrency(payment['total_amount'])}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      Text(
                                        'Dibuat: ${_formatDate(payment['created_at'])}',
                                        style: TextStyle(
                                          fontSize: 11,
                                          color: Colors.grey[500],
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: Column(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                          horizontal: 8,
                                          vertical: 4,
                                        ),
                                        decoration: BoxDecoration(
                                          color: _getStatusColor(
                                            payment['status'],
                                          ).withOpacity(0.2),
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Text(
                                          _getStatusLabel(payment['status']),
                                          style: TextStyle(
                                            fontSize: 10,
                                            fontWeight: FontWeight.bold,
                                            color: _getStatusColor(
                                              payment['status'],
                                            ),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                  onTap: () {
                                    context.push(
                                      '/admin/payment/${payment['id']}',
                                    );
                                  },
                                ),
                              );
                            },
                          ),
                        ),
                ),

                // Pagination
                if (lastPage > 1)
                  Container(
                    padding: const EdgeInsets.all(8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.chevron_left),
                          onPressed: currentPage > 1
                              ? () => _loadPayments(
                                  page: currentPage - 1,
                                  status: selectedStatus,
                                )
                              : null,
                        ),
                        Text(
                          'Halaman $currentPage dari $lastPage',
                          style: const TextStyle(fontSize: 14),
                        ),
                        IconButton(
                          icon: const Icon(Icons.chevron_right),
                          onPressed: currentPage < lastPage
                              ? () => _loadPayments(
                                  page: currentPage + 1,
                                  status: selectedStatus,
                                )
                              : null,
                        ),
                      ],
                    ),
                  ),
              ],
            ),
    );
  }

  Widget _buildStatCard(
    String label,
    String value,
    Color color,
    IconData icon,
  ) {
    return Card(
      color: color.withOpacity(0.1),
      child: Padding(
        padding: const EdgeInsets.all(8),
        child: Column(
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: _darkenColor(color),
              ),
            ),
            Text(
              label,
              style: TextStyle(fontSize: 10, color: Colors.grey[700]),
            ),
          ],
        ),
      ),
    );
  }
}
