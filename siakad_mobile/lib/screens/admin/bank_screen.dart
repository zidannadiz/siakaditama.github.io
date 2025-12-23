import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class BankScreen extends StatefulWidget {
  const BankScreen({Key? key}) : super(key: key);

  @override
  State<BankScreen> createState() => _BankScreenState();
}

class _BankScreenState extends State<BankScreen> {
  List<dynamic> banks = [];
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadBanks();
  }

  Future<void> _loadBanks() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    try {
      final result = await ApiService.get('/admin/bank');
      if (result['success'] == true) {
        setState(() {
          banks = result['data'] ?? [];
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
          errorMessage = result['message'] ?? 'Gagal memuat bank';
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
        errorMessage = 'Error: ${e.toString()}';
      });
    }
  }

  Future<void> _toggleStatus(int bankId, bool currentStatus) async {
    try {
      final result = await ApiService.post(
        '/admin/bank/$bankId/toggle-status',
        {},
      );
      if (result['success'] == true) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Status bank berhasil diubah'),
              backgroundColor: Colors.green,
            ),
          );
          _loadBanks();
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(result['message'] ?? 'Gagal mengubah status bank'),
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

  Future<void> _showEditDialog(dynamic bank) async {
    final nameController = TextEditingController(text: bank['name'] ?? '');
    final codeController = TextEditingController(text: bank['code'] ?? '');

    await showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Edit Bank'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: nameController,
                decoration: const InputDecoration(
                  labelText: 'Nama Bank *',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: codeController,
                decoration: const InputDecoration(
                  labelText: 'Kode Bank *',
                  border: OutlineInputBorder(),
                ),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (nameController.text.isEmpty || codeController.text.isEmpty) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Nama dan kode bank harus diisi'),
                    backgroundColor: Colors.red,
                  ),
                );
                return;
              }

              try {
                final result =
                    await ApiService.put('/admin/bank/${bank['id']}', {
                      'name': nameController.text.trim(),
                      'code': codeController.text.trim().toUpperCase(),
                      'is_active': bank['is_active'] ?? true,
                    });

                if (result['success'] == true) {
                  if (mounted) {
                    Navigator.pop(context);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Text('Bank berhasil diperbarui'),
                        backgroundColor: Colors.green,
                      ),
                    );
                    _loadBanks();
                  }
                } else {
                  if (mounted) {
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(
                        content: Text(
                          result['message'] ?? 'Gagal memperbarui bank',
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
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Manajemen Bank'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadBanks,
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
                    onPressed: _loadBanks,
                    child: const Text('Coba Lagi'),
                  ),
                ],
              ),
            )
          : banks.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.account_balance,
                    size: 64,
                    color: Colors.grey[400],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Belum ada bank',
                    style: TextStyle(color: Colors.grey[600]),
                  ),
                ],
              ),
            )
          : RefreshIndicator(
              onRefresh: _loadBanks,
              child: ListView.builder(
                itemCount: banks.length,
                itemBuilder: (context, index) {
                  final bank = banks[index];
                  final isActive = bank['is_active'] ?? false;

                  return Card(
                    margin: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 8,
                    ),
                    child: ListTile(
                      leading: CircleAvatar(
                        backgroundColor: isActive ? Colors.green : Colors.grey,
                        child: Text(
                          bank['code']?[0] ?? 'B',
                          style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      title: Text(
                        bank['name'] ?? '-',
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const SizedBox(height: 4),
                          Text('Kode: ${bank['code'] ?? '-'}'),
                          Row(
                            children: [
                              Icon(
                                isActive ? Icons.check_circle : Icons.cancel,
                                size: 16,
                                color: isActive ? Colors.green : Colors.grey,
                              ),
                              const SizedBox(width: 4),
                              Text(
                                isActive ? 'Aktif' : 'Tidak Aktif',
                                style: TextStyle(
                                  color: isActive ? Colors.green : Colors.grey,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                      trailing: PopupMenuButton(
                        itemBuilder: (context) => [
                          PopupMenuItem(
                            child: const Row(
                              children: [
                                Icon(Icons.edit, color: Colors.blue),
                                SizedBox(width: 8),
                                Text('Edit'),
                              ],
                            ),
                            onTap: () => Future.delayed(
                              const Duration(milliseconds: 100),
                              () => _showEditDialog(bank),
                            ),
                          ),
                          PopupMenuItem(
                            child: Row(
                              children: [
                                Icon(
                                  isActive ? Icons.block : Icons.check_circle,
                                  color: isActive
                                      ? Colors.orange
                                      : Colors.green,
                                ),
                                const SizedBox(width: 8),
                                Text(isActive ? 'Nonaktifkan' : 'Aktifkan'),
                              ],
                            ),
                            onTap: () => Future.delayed(
                              const Duration(milliseconds: 100),
                              () => _toggleStatus(bank['id'], isActive),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
    );
  }
}
