import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'screens/auth/login_screen.dart';
import 'screens/dashboard/admin_dashboard.dart';
import 'screens/dashboard/dosen_dashboard.dart';
import 'screens/dashboard/mahasiswa_dashboard.dart';
import 'services/storage_service.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'SIAKAD Mobile',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primarySwatch: Colors.blue,
        useMaterial3: true,
      ),
      routerConfig: _router,
    );
  }
}

final GoRouter _router = GoRouter(
  initialLocation: '/login',
  routes: [
    GoRoute(
      path: '/login',
      builder: (context, state) => const LoginScreen(),
    ),
    GoRoute(
      path: '/dashboard',
      builder: (context, state) => const DashboardRouter(),
    ),
    GoRoute(
      path: '/admin/dashboard',
      builder: (context, state) => const AdminDashboard(),
    ),
    GoRoute(
      path: '/dosen/dashboard',
      builder: (context, state) => const DosenDashboard(),
    ),
    GoRoute(
      path: '/mahasiswa/dashboard',
      builder: (context, state) => const MahasiswaDashboard(),
    ),
  ],
);

// Helper widget to route based on user role
class DashboardRouter extends StatelessWidget {
  const DashboardRouter({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<Map<String, dynamic>?>(
      future: StorageService.getUser(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Scaffold(
            body: Center(child: CircularProgressIndicator()),
          );
        }

        final user = snapshot.data;
        if (user == null) {
          return const LoginScreen();
        }

        final role = user['role'] as String?;
        
        switch (role) {
          case 'admin':
            return const AdminDashboard();
          case 'dosen':
            return const DosenDashboard();
          case 'mahasiswa':
            return const MahasiswaDashboard();
          default:
            return const LoginScreen();
        }
      },
    );
  }
}
