import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';

class DosenDashboard extends StatefulWidget {
  const DosenDashboard({Key? key}) : super(key: key);

  @override
  State<DosenDashboard> createState() => _DosenDashboardState();
}

class _DosenDashboardState extends State<DosenDashboard> {
  Map<String, dynamic>? dashboardData;
  Map<String, dynamic>? userData;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    // Load user data
    final user = await StorageService.getUser();
    setState(() {
      userData = user;
    });

    // Load dashboard data
    final result = await ApiService.getDashboard();
    if (result['success'] == true) {
      setState(() {
        dashboardData = result['data'];
        isLoading = false;
      });
    } else {
      setState(() {
        isLoading = false;
      });
    }
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Apakah Anda yakin ingin logout?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      await ApiService.logout();
      if (context.mounted) {
        context.go('/login');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Dashboard Dosen'),
        ),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    final dosen = dashboardData?['dosen'] ?? {};
    final semesterAktif = dashboardData?['semester_aktif'];
    final jadwalHariIni = dashboardData?['jadwal_hari_ini'] ?? [];
    final jadwalKuliah = dashboardData?['jadwal_kuliah'] ?? [];
    final dosenName = dosen['nama'] ?? userData?['name'] ?? 'Dosen';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard Dosen'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadData,
            tooltip: 'Refresh',
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: _handleLogout,
            tooltip: 'Logout',
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: _loadData,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Welcome Card
              Card(
                color: Colors.green[50],
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    children: [
                      const Icon(Icons.person, size: 40, color: Colors.green),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Selamat Datang',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                            ),
                            Text(
                              dosenName,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            if (dosen['nidn'] != null)
                              Text(
                                'NIDN: ${dosen['nidn']}',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[600],
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

              // Semester Aktif
              if (semesterAktif != null)
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        const Icon(Icons.calendar_today, color: Colors.blue),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Semester Aktif',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey,
                                ),
                              ),
                              Text(
                                '${semesterAktif['nama']} ${semesterAktif['tahun_ajaran']}',
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
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

              // Jadwal Hari Ini
              if (jadwalHariIni.isNotEmpty) ...[
                Text(
                  'Jadwal Hari Ini',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 12),
                ...jadwalHariIni.map<Widget>((jadwal) => Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: ListTile(
                    leading: const Icon(Icons.schedule, color: Colors.blue),
                    title: Text(
                      jadwal['mata_kuliah'] ?? '-',
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (jadwal['jam_mulai'] != null && jadwal['jam_selesai'] != null)
                          Text('${jadwal['jam_mulai']} - ${jadwal['jam_selesai']}'),
                        if (jadwal['ruangan'] != null)
                          Text('Ruangan: ${jadwal['ruangan']}'),
                      ],
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                  ),
                )),
              ] else ...[
                Card(
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Row(
                      children: [
                        const Icon(Icons.event_busy, color: Colors.grey),
                        const SizedBox(width: 12),
                        const Text('Tidak ada jadwal hari ini'),
                      ],
                    ),
                  ),
                ),
              ],
              const SizedBox(height: 24),

              // Total Kelas
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      Column(
                        children: [
                          const Icon(Icons.class_, size: 40, color: Colors.orange),
                          const SizedBox(height: 8),
                          Text(
                            '${jadwalKuliah.length}',
                            style: const TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const Text('Total Kelas'),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

