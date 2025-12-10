import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../services/api_service.dart';

class NotifikasiBadge extends StatefulWidget {
  const NotifikasiBadge({Key? key}) : super(key: key);

  @override
  State<NotifikasiBadge> createState() => _NotifikasiBadgeState();
}

class _NotifikasiBadgeState extends State<NotifikasiBadge> {
  int unreadCount = 0;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadUnreadCount();
  }

  Future<void> _loadUnreadCount() async {
    try {
      final result = await ApiService.get('/notifikasi/unread-count');
      if (result['success'] == true) {
        setState(() {
          unreadCount = result['data']['count'] ?? 0;
          isLoading = false;
        });
      } else {
        setState(() {
          isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isLoading) {
      return IconButton(
        icon: const Icon(Icons.notifications),
        onPressed: () {
          context.push('/notifikasi').then((_) => _loadUnreadCount());
        },
        tooltip: 'Notifikasi',
      );
    }

    return Stack(
      children: [
        IconButton(
          icon: const Icon(Icons.notifications),
          onPressed: () {
            context.push('/notifikasi').then((_) => _loadUnreadCount());
          },
          tooltip: 'Notifikasi',
        ),
        if (unreadCount > 0)
          Positioned(
            right: 8,
            top: 8,
            child: Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: Colors.red,
                borderRadius: BorderRadius.circular(10),
              ),
              constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
              child: Text(
                unreadCount > 99 ? '99+' : '$unreadCount',
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                ),
                textAlign: TextAlign.center,
              ),
            ),
          ),
      ],
    );
  }
}
