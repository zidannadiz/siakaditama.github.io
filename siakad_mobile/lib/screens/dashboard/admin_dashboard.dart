import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../services/api_service.dart';
import '../../services/storage_service.dart';
import '../../widgets/notifikasi_badge.dart';

class AdminDashboard extends StatefulWidget {
  const AdminDashboard({Key? key}) : super(key: key);

  @override
  State<AdminDashboard> createState() => _AdminDashboardState();
}

class _AdminDashboardState extends State<AdminDashboard> {
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
        appBar: AppBar(title: const Text('Dashboard Admin')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    final stats = dashboardData?['statistics'] ?? {};
    final userName = userData?['name'] ?? 'Admin';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard Admin'),
        actions: [
          const NotifikasiBadge(),
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
                color: Colors.blue[50],
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    children: [
                      const Icon(Icons.person, size: 40, color: Colors.blue),
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
                              userName,
                              style: const TextStyle(
                                fontSize: 20,
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

              // Statistics Title
              Text(
                'Statistik',
                style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 16),

              // Quick Access Menu
              Text(
                'Menu Utama',
                style: Theme.of(
                  context,
                ).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 12),
              GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisSpacing: 12,
                mainAxisSpacing: 12,
                childAspectRatio: 1.5,
                children: [
                  _MenuCard(
                    title: 'Persetujuan KRS',
                    icon: Icons.approval,
                    color: Colors.orange,
                    onTap: () => context.push('/admin/krs'),
                  ),
                  _MenuCard(
                    title: 'Mahasiswa',
                    icon: Icons.people,
                    color: Colors.blue,
                    onTap: () => context.push('/admin/mahasiswa'),
                  ),
                  _MenuCard(
                    title: 'Profil',
                    icon: Icons.person,
                    color: Colors.purple,
                    onTap: () => context.push('/profile'),
                  ),
                  _MenuCard(
                    title: 'Notifikasi',
                    icon: Icons.notifications,
                    color: Colors.red,
                    onTap: () => context.push('/notifikasi'),
                  ),
                  _MenuCard(
                    title: 'Pengumuman',
                    icon: Icons.announcement,
                    color: Colors.orange,
                    onTap: () => context.push('/pengumuman'),
                  ),
                  _MenuCard(
                    title: 'Chat',
                    icon: Icons.chat,
                    color: Colors.green,
                    onTap: () => context.push('/chat'),
                  ),
                  _MenuCard(
                    title: 'Pembayaran',
                    icon: Icons.payment,
                    color: Colors.purple,
                    onTap: () => context.push('/payment'),
                  ),
                  _MenuCard(
                    title: 'Forum',
                    icon: Icons.forum,
                    color: Colors.indigo,
                    onTap: () => context.push('/forum'),
                  ),
                  _MenuCard(
                    title: 'Q&A',
                    icon: Icons.help_outline,
                    color: Colors.cyan,
                    onTap: () => context.push('/qna'),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Statistics Grid
              GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                childAspectRatio: 1.1,
                children: [
                  _StatCard(
                    title: 'Mahasiswa',
                    value: stats['total_mahasiswa']?.toString() ?? '0',
                    icon: Icons.people,
                    color: Colors.blue,
                  ),
                  _StatCard(
                    title: 'Dosen',
                    value: stats['total_dosen']?.toString() ?? '0',
                    icon: Icons.person,
                    color: Colors.green,
                  ),
                  _StatCard(
                    title: 'Prodi',
                    value: stats['total_prodi']?.toString() ?? '0',
                    icon: Icons.school,
                    color: Colors.orange,
                  ),
                  _StatCard(
                    title: 'Mata Kuliah',
                    value: stats['total_mata_kuliah']?.toString() ?? '0',
                    icon: Icons.book,
                    color: Colors.purple,
                  ),
                  _StatCard(
                    title: 'KRS Pending',
                    value: stats['krs_pending']?.toString() ?? '0',
                    icon: Icons.pending,
                    color: Colors.red,
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color color;

  const _StatCard({
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 40, color: color),
            const SizedBox(height: 12),
            Text(
              value,
              style: TextStyle(
                fontSize: 28,
                fontWeight: FontWeight.bold,
                color: color,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              title,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}

class _MenuCard extends StatelessWidget {
  final String title;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _MenuCard({
    required this.title,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 2,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 32, color: color),
              const SizedBox(height: 8),
              Text(
                title,
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: color,
                ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
