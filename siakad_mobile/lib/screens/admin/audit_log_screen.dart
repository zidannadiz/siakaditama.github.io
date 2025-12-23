import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class AuditLogScreen extends StatefulWidget {
  const AuditLogScreen({Key? key}) : super(key: key);

  @override
  State<AuditLogScreen> createState() => _AuditLogScreenState();
}

class _AuditLogScreenState extends State<AuditLogScreen> {
  List<dynamic> logs = [];
  Map<String, dynamic>? filters;
  bool isLoading = true;
  String? errorMessage;

  // Filter values
  String? selectedAction;
  String? selectedModelType;
  int? selectedUserId;
  DateTime? startDate;
  DateTime? endDate;
  final TextEditingController _searchController = TextEditingController();

  int currentPage = 1;
  int lastPage = 1;
  int total = 0;

  @override
  void initState() {
    super.initState();
    _loadLogs();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> _loadLogs({bool resetPage = false}) async {
    if (resetPage) {
      currentPage = 1;
    }

    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final queryParams = <String, String>{'page': currentPage.toString()};

      if (selectedAction != null) {
        queryParams['action'] = selectedAction!;
      }
      if (selectedModelType != null) {
        queryParams['model_type'] = selectedModelType!;
      }
      if (selectedUserId != null) {
        queryParams['user_id'] = selectedUserId!.toString();
      }
      if (startDate != null) {
        queryParams['start_date'] = DateFormat('yyyy-MM-dd').format(startDate!);
      }
      if (endDate != null) {
        queryParams['end_date'] = DateFormat('yyyy-MM-dd').format(endDate!);
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

      final result = await ApiService.get('/admin/audit-log?$queryString');

      if (result['success'] == true) {
        setState(() {
          logs = result['data']['logs'] ?? [];
          filters = result['data']['filters'];
          currentPage = result['data']['pagination']['current_page'] ?? 1;
          lastPage = result['data']['pagination']['last_page'] ?? 1;
          total = result['data']['pagination']['total'] ?? 0;
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat log';
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
          title: const Text('Filter Audit Log'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                if (filters != null && filters!['actions'] != null)
                  DropdownButtonFormField<String>(
                    value: selectedAction,
                    decoration: const InputDecoration(
                      labelText: 'Action',
                      border: OutlineInputBorder(),
                    ),
                    items: [
                      const DropdownMenuItem(value: null, child: Text('Semua')),
                      ...(filters!['actions'] as List).map(
                        (action) => DropdownMenuItem(
                          value: action,
                          child: Text(action),
                        ),
                      ),
                    ],
                    onChanged: (value) {
                      setDialogState(() {
                        selectedAction = value;
                      });
                    },
                  ),
                const SizedBox(height: 16),
                if (filters != null && filters!['model_types'] != null)
                  DropdownButtonFormField<String>(
                    value: selectedModelType,
                    decoration: const InputDecoration(
                      labelText: 'Model Type',
                      border: OutlineInputBorder(),
                    ),
                    items: [
                      const DropdownMenuItem(value: null, child: Text('Semua')),
                      ...(filters!['model_types'] as List).map(
                        (type) =>
                            DropdownMenuItem(value: type, child: Text(type)),
                      ),
                    ],
                    onChanged: (value) {
                      setDialogState(() {
                        selectedModelType = value;
                      });
                    },
                  ),
                const SizedBox(height: 16),
                if (filters != null && filters!['users'] != null)
                  DropdownButtonFormField<int>(
                    value: selectedUserId,
                    decoration: const InputDecoration(
                      labelText: 'User',
                      border: OutlineInputBorder(),
                    ),
                    items: [
                      const DropdownMenuItem(value: null, child: Text('Semua')),
                      ...(filters!['users'] as List).map(
                        (user) => DropdownMenuItem(
                          value: user['id'],
                          child: Text(user['name'] ?? user['email'] ?? ''),
                        ),
                      ),
                    ],
                    onChanged: (value) {
                      setDialogState(() {
                        selectedUserId = value;
                      });
                    },
                  ),
                const SizedBox(height: 16),
                ListTile(
                  title: const Text('Dari Tanggal'),
                  subtitle: Text(
                    startDate == null
                        ? 'Pilih tanggal'
                        : DateFormat('dd/MM/yyyy').format(startDate!),
                  ),
                  trailing: const Icon(Icons.calendar_today),
                  onTap: () async {
                    final date = await showDatePicker(
                      context: context,
                      initialDate: startDate ?? DateTime.now(),
                      firstDate: DateTime(2020),
                      lastDate: DateTime.now(),
                    );
                    if (date != null) {
                      setDialogState(() {
                        startDate = date;
                      });
                    }
                  },
                ),
                ListTile(
                  title: const Text('Sampai Tanggal'),
                  subtitle: Text(
                    endDate == null
                        ? 'Pilih tanggal'
                        : DateFormat('dd/MM/yyyy').format(endDate!),
                  ),
                  trailing: const Icon(Icons.calendar_today),
                  onTap: () async {
                    final date = await showDatePicker(
                      context: context,
                      initialDate: endDate ?? DateTime.now(),
                      firstDate: startDate ?? DateTime(2020),
                      lastDate: DateTime.now(),
                    );
                    if (date != null) {
                      setDialogState(() {
                        endDate = date;
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
                  selectedAction = null;
                  selectedModelType = null;
                  selectedUserId = null;
                  startDate = null;
                  endDate = null;
                });
                Navigator.pop(context);
                _loadLogs(resetPage: true);
              },
              child: const Text('Reset'),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(context);
                _loadLogs(resetPage: true);
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
        title: const Text('Audit Log'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: _showFilterDialog,
            tooltip: 'Filter',
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _loadLogs(resetPage: true),
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
                    onPressed: () => _loadLogs(resetPage: true),
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : Column(
              children: [
                // Search Bar
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: TextField(
                    controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari log...',
                      prefixIcon: const Icon(Icons.search),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      suffixIcon: _searchController.text.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear),
                              onPressed: () {
                                _searchController.clear();
                                _loadLogs(resetPage: true);
                              },
                            )
                          : null,
                    ),
                    onSubmitted: (_) => _loadLogs(resetPage: true),
                  ),
                ),

                // Logs List
                Expanded(
                  child: logs.isEmpty
                      ? Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.history,
                                size: 64,
                                color: Colors.grey[400],
                              ),
                              const SizedBox(height: 16),
                              Text(
                                'Tidak ada log',
                                style: TextStyle(color: Colors.grey[600]),
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: () => _loadLogs(resetPage: true),
                          child: ListView.builder(
                            itemCount: logs.length,
                            itemBuilder: (context, index) {
                              final log = logs[index];
                              final createdAt = log['created_at'] != null
                                  ? DateFormat(
                                      'dd/MM/yyyy HH:mm',
                                    ).format(DateTime.parse(log['created_at']))
                                  : '-';

                              return Card(
                                margin: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 8,
                                ),
                                child: ListTile(
                                  leading: Icon(
                                    _getActionIcon(log['action']),
                                    color: _getActionColor(log['action']),
                                  ),
                                  title: Text(
                                    log['description'] ?? '-',
                                    style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  subtitle: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const SizedBox(height: 4),
                                      if (log['action'] != null)
                                        Text('Action: ${log['action']}'),
                                      if (log['model_type'] != null)
                                        Text('Model: ${log['model_type']}'),
                                      if (log['user'] != null)
                                        Text(
                                          'User: ${log['user']['name'] ?? log['user']['email'] ?? '-'}',
                                        ),
                                      Text('Waktu: $createdAt'),
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
                                  _loadLogs();
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
                                  _loadLogs();
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

  IconData _getActionIcon(String? action) {
    switch (action) {
      case 'create':
        return Icons.add_circle;
      case 'update':
        return Icons.edit;
      case 'delete':
        return Icons.delete;
      case 'login':
        return Icons.login;
      case 'logout':
        return Icons.logout;
      default:
        return Icons.info;
    }
  }

  Color _getActionColor(String? action) {
    switch (action) {
      case 'create':
        return Colors.green;
      case 'update':
        return Colors.blue;
      case 'delete':
        return Colors.red;
      case 'login':
        return Colors.orange;
      case 'logout':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }
}
