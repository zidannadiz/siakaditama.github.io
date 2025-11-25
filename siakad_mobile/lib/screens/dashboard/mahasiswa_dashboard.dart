import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';

class MahasiswaDashboard extends StatefulWidget {
  const MahasiswaDashboard({Key? key}) : super(key: key);

  @override
  State<MahasiswaDashboard> createState() => _MahasiswaDashboardState();
}

class _MahasiswaDashboardState extends State<MahasiswaDashboard> {
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
          title: const Text('Dashboard Mahasiswa'),
        ),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    final mahasiswa = dashboardData?['mahasiswa'] ?? {};
    final semesterAktif = dashboardData?['semester_aktif'];
    final krsSemesterIni = dashboardData?['krs_semester_ini'] ?? [];
    final jadwalHariIni = dashboardData?['jadwal_hari_ini'] ?? [];
    final totalSks = dashboardData?['total_sks'] ?? 0;
    final mahasiswaName = mahasiswa['nama'] ?? userData?['name'] ?? 'Mahasiswa';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard Mahasiswa'),
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
                color: Colors.purple[50],
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    children: [
                      const Icon(Icons.person, size: 40, color: Colors.purple),
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
                              mahasiswaName,
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            if (mahasiswa['nim'] != null)
                              Text(
                                'NIM: ${mahasiswa['nim']}',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey[600],
                                ),
                              ),
                            if (mahasiswa['prodi'] != null)
                              Text(
                                'Prodi: ${mahasiswa['prodi']}',
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

              // Semester Aktif & Total SKS
              Row(
                children: [
                  Expanded(
                    child: Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          children: [
                            const Icon(Icons.calendar_today, color: Colors.blue),
                            const SizedBox(height: 8),
                            if (semesterAktif != null)
                              Text(
                                '${semesterAktif['nama']}',
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                ),
                                textAlign: TextAlign.center,
                              )
                            else
                              const Text(
                                'Tidak ada',
                                style: TextStyle(fontSize: 12),
                              ),
                            const Text(
                              'Semester',
                              style: TextStyle(fontSize: 10),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Card(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          children: [
                            const Icon(Icons.credit_card, color: Colors.orange),
                            const SizedBox(height: 8),
                            Text(
                              '$totalSks',
                              style: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const Text(
                              'Total SKS',
                              style: TextStyle(fontSize: 10),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
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
                        if (jadwal['dosen'] != null)
                          Text('Dosen: ${jadwal['dosen']}'),
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

              // KRS Semester Ini
              if (krsSemesterIni.isNotEmpty) ...[
                Text(
                  'KRS Semester Ini',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 12),
                ...krsSemesterIni.take(5).map<Widget>((krs) => Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  child: ListTile(
                    leading: const Icon(Icons.book, color: Colors.green),
                    title: Text(
                      krs['mata_kuliah'] ?? '-',
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (krs['dosen'] != null)
                          Text('Dosen: ${krs['dosen']}'),
                        if (krs['hari'] != null)
                          Text('Hari: ${krs['hari']}'),
                        if (krs['sks'] != null)
                          Text('SKS: ${krs['sks']}'),
                      ],
                    ),
                    trailing: const Icon(Icons.arrow_forward_ios, size: 16),
                  ),
                )),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

