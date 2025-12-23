import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class LaporanPembayaranScreen extends StatefulWidget {
  const LaporanPembayaranScreen({Key? key}) : super(key: key);

  @override
  State<LaporanPembayaranScreen> createState() =>
      _LaporanPembayaranScreenState();
}

class _LaporanPembayaranScreenState extends State<LaporanPembayaranScreen> {
  List<dynamic> payments = [];
  Map<String, dynamic>? stats;
  List<dynamic> banks = [];
  List<dynamic> mahasiswas = [];
  bool isLoading = true;
  String? errorMessage;

  // Filters
  String? selectedStatus;
  String? selectedPaymentType;
  int? selectedBankId;
  int? selectedMahasiswaId;
  DateTime? dateFrom;
  DateTime? dateTo;
  final TextEditingController _searchController = TextEditingController();

  int currentPage = 1;
  int lastPage = 1;
  int total = 0;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadData({bool resetPage = false}) async {
    if (resetPage) {
      currentPage = 1;
    }

    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final queryParams = <String, String>{'page': currentPage.toString()};

      if (selectedStatus != null) {
        queryParams['status'] = selectedStatus!;
      }
      if (selectedPaymentType != null) {
        queryParams['payment_type'] = selectedPaymentType!;
      }
      if (selectedBankId != null) {
        queryParams['bank_id'] = selectedBankId!.toString();
      }
      if (selectedMahasiswaId != null) {
        queryParams['mahasiswa_id'] = selectedMahasiswaId!.toString();
      }
      if (dateFrom != null) {
        queryParams['date_from'] = DateFormat('yyyy-MM-dd').format(dateFrom!);
      }
      if (dateTo != null) {
        queryParams['date_to'] = DateFormat('yyyy-MM-dd').format(dateTo!);
      }
      if (_searchController.text.isNotEmpty) {
        queryParams['search'] = _searchController.text;
      }

      final queryString = queryParams.entries
          .map(
            (e) =>
                '${Uri.encodeComponent(e.key)}=${Uri.encodeComponent(e.value)}',
          )
          .join('&');

      final result = await ApiService.get(
        '/admin/laporan/pembayaran?$queryString',
      );

      if (result['success'] == true) {
        setState(() {
          payments = result['data']['payments'] ?? [];
          stats = result['data']['stats'];
          banks = result['data']['banks'] ?? [];
          mahasiswas = result['data']['mahasiswas'] ?? [];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat data';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text('Filter Laporan'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                DropdownButtonFormField<String>(
                  value: selectedStatus,
                  decoration: const InputDecoration(
                    labelText: 'Status',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(value: null, child: Text('Semua')),
                    const DropdownMenuItem(
                      value: 'pending',
                      child: Text('Pending'),
                    ),
                    const DropdownMenuItem(value: 'paid', child: Text('Paid')),
                    const DropdownMenuItem(
                      value: 'expired',
                      child: Text('Expired'),
                    ),
                    const DropdownMenuItem(
                      value: 'cancelled',
                      child: Text('Cancelled'),
                    ),
                  ],
                  onChanged: (value) {
                    setDialogState(() {
                      selectedStatus = value;
                    });
                  },
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<String>(
                  value: selectedPaymentType,
                  decoration: const InputDecoration(
                    labelText: 'Tipe Pembayaran',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(value: null, child: Text('Semua')),
                    const DropdownMenuItem(value: 'spp', child: Text('SPP')),
                    const DropdownMenuItem(value: 'ukt', child: Text('UKT')),
                    const DropdownMenuItem(
                      value: 'lainnya',
                      child: Text('Lainnya'),
                    ),
                  ],
                  onChanged: (value) {
                    setDialogState(() {
                      selectedPaymentType = value;
                    });
                  },
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<int>(
                  value: selectedBankId,
                  decoration: const InputDecoration(
                    labelText: 'Bank',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(value: null, child: Text('Semua')),
                    ...banks.map(
                      (bank) => DropdownMenuItem(
                        value: bank['id'],
                        child: Text(bank['name'] ?? ''),
                      ),
                    ),
                  ],
                  onChanged: (value) {
                    setDialogState(() {
                      selectedBankId = value;
                    });
                  },
                ),
                const SizedBox(height: 16),
                DropdownButtonFormField<int>(
                  value: selectedMahasiswaId,
                  decoration: const InputDecoration(
                    labelText: 'Mahasiswa',
                    border: OutlineInputBorder(),
                  ),
                  items: [
                    const DropdownMenuItem(value: null, child: Text('Semua')),
                    ...mahasiswas.map(
                      (mhs) => DropdownMenuItem(
                        value: mhs['id'],
                        child: Text('${mhs['nim']} - ${mhs['name']}'),
                      ),
                    ),
                  ],
                  onChanged: (value) {
                    setDialogState(() {
                      selectedMahasiswaId = value;
                    });
                  },
                ),
                const SizedBox(height: 16),
                ListTile(
                  title: const Text('Dari Tanggal'),
                  subtitle: Text(
                    dateFrom == null
                        ? 'Pilih tanggal'
                        : DateFormat('dd/MM/yyyy').format(dateFrom!),
                  ),
                  trailing: const Icon(Icons.calendar_today),
                  onTap: () async {
                    final date = await showDatePicker(
                      context: context,
                      initialDate: dateFrom ?? DateTime.now(),
                      firstDate: DateTime(2020),
                      lastDate: DateTime.now(),
                    );
                    if (date != null) {
                      setDialogState(() {
                        dateFrom = date;
                      });
                    }
                  },
                ),
                ListTile(
                  title: const Text('Sampai Tanggal'),
                  subtitle: Text(
                    dateTo == null
                        ? 'Pilih tanggal'
                        : DateFormat('dd/MM/yyyy').format(dateTo!),
                  ),
                  trailing: const Icon(Icons.calendar_today),
                  onTap: () async {
                    final date = await showDatePicker(
                      context: context,
                      initialDate: dateTo ?? DateTime.now(),
                      firstDate: dateFrom ?? DateTime(2020),
                      lastDate: DateTime.now(),
                    );
                    if (date != null) {
                      setDialogState(() {
                        dateTo = date;
                      });
                    }
                  },
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () {
                setState(() {
                  selectedStatus = null;
                  selectedPaymentType = null;
                  selectedBankId = null;
                  selectedMahasiswaId = null;
                  dateFrom = null;
                  dateTo = null;
                });
                Navigator.pop(context);
                _loadData(resetPage: true);
              },
              child: const Text('Reset'),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                _loadData(resetPage: true);
              },
              child: const Text('Terapkan'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Laporan Pembayaran'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
            tooltip: 'Filter',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadData(resetPage: true),
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
                    onPressed: () => _loadData(resetPage: true),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Statistics Cards
                if (stats != null)
                  Container(
                    padding: const EdgeInsets.all(16),
                    color: Colors.grey[100],
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Statistik',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Row(
                          children: [
                            Expanded(
                              child: _StatCard(
                                title: 'Total',
                                value: stats!['total']?.toString() ?? '0',
                                color: Colors.blue,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _StatCard(
                                title: 'Total Amount',
                                value: NumberFormat.currency(
                                  locale: 'id',
                                  symbol: 'Rp ',
                                  decimalDigits: 0,
                                ).format(stats!['total_amount'] ?? 0),
                                color: Colors.green,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Expanded(
                              child: _StatCard(
                                title: 'Pending',
                                value: stats!['pending']?.toString() ?? '0',
                                color: Colors.orange,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _StatCard(
                                title: 'Paid',
                                value: stats!['paid']?.toString() ?? '0',
                                color: Colors.green,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                // Search Bar
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari invoice, VA, nama...',
                      prefixIcon: const Icon(Icons.search),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _loadData(resetPage: true);
                              },
                            )
                          : null,
                    ),
                    onSubmitted: (_) => _loadData(resetPage: true),
                  ),
                ),

                // Payments List
                Expanded(
                  child: payments.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.payment,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada data pembayaran',
                                style: TextStyle(color: Colors.grey[600]),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () => _loadData(resetPage: true),
                          child: ListView.builder(
                            itemCount: payments.length,
                            itemBuilder: (context, index) {
                              final payment = payments[index];
                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 8,
                                ),
                                child: ListTile(
                                  title: Text(
                                    payment['invoice_number'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 4),
                                      Text(payment['user']?['name'] ?? '-'),
                                      Text(
                                        'VA: ${payment['virtual_account'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey[600],
                                        ),
                                      ),
                                      Text(
                                        '${NumberFormat.currency(locale: 'id', symbol: 'Rp ', decimalDigits: 0).format(payment['total_amount'] ?? 0)} • ${payment['status'] ?? '-'}',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: _getStatusColor(
                                            payment['status'],
                                          ),
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ],
                                  ),
                                  trailing: Icon(
                                    Icons.chevron_right,
                                    color: Colors.grey[400],
                                  ),
                                  onTap: () {
                                    // TODO: Navigate to detail
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
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.chevron_left),
                          onPressed: currentPage > 1
                              ? () {
                                  setState(() {
                                    currentPage--;
                                  });
                                  _loadData();
                                }
                              : null,
                        ),
                        Text(
                          'Halaman $currentPage dari $lastPage',
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        IconButton(
                          icon: const Icon(Icons.chevron_right),
                          onPressed: currentPage < lastPage
                              ? () {
                                  setState(() {
                                    currentPage++;
                                  });
                                  _loadData();
                                }
                              : null,
                        ),
                      ],
                    ),
                  ),
              ],
            ),
    );
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
}

class _StatCard extends StatelessWidget {
  final String title;
  final String value;
  final Color color;

  const _StatCard({
    required this.title,
    required this.value,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      color: color.withOpacity(0.1),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              title,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
