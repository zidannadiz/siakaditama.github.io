import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'screens/auth/login_screen.dart';
import 'screens/dashboard/admin_dashboard.dart';
import 'screens/dashboard/dosen_dashboard.dart';
import 'screens/dashboard/mahasiswa_dashboard.dart';
import 'screens/profile/profile_screen.dart';
import 'screens/mahasiswa/krs_list_screen.dart';
import 'screens/mahasiswa/krs_add_screen.dart';
import 'screens/mahasiswa/khs_screen.dart';
import 'screens/dosen/nilai_list_screen.dart';
import 'screens/dosen/nilai_input_screen.dart';
import 'screens/dosen/presensi_list_screen.dart' as dosen_presensi;
import 'screens/dosen/presensi_input_screen.dart';
import 'screens/notifikasi/notifikasi_screen.dart';
import 'screens/pengumuman/pengumuman_list_screen.dart';
import 'screens/pengumuman/pengumuman_detail_screen.dart';
import 'screens/chat/conversation_list_screen.dart';
import 'screens/chat/chat_detail_screen.dart';
import 'screens/chat/create_conversation_screen.dart';
import 'screens/payment/payment_list_screen.dart';
import 'screens/payment/payment_detail_screen.dart';
import 'screens/payment/payment_create_screen.dart';
import 'screens/mahasiswa/presensi_list_screen.dart' as mahasiswa_presensi;
import 'screens/mahasiswa/presensi_detail_screen.dart';
import 'screens/mahasiswa/assignment_list_screen.dart';
import 'screens/mahasiswa/assignment_detail_screen.dart';
import 'screens/mahasiswa/exam_list_screen.dart';
import 'screens/mahasiswa/exam_detail_screen.dart';
import 'screens/mahasiswa/exam_take_screen.dart';
import 'screens/mahasiswa/exam_result_screen.dart';
import 'screens/dosen/assignment_list_screen.dart' as dosen_assignment;
import 'screens/dosen/assignment_create_screen.dart' as dosen_assignment_create;
import 'screens/dosen/assignment_detail_screen.dart' as dosen_assignment_detail;
import 'screens/dosen/assignment_grade_screen.dart' as dosen_assignment_grade;
import 'screens/dosen/exam_list_screen.dart' as dosen_exam;
import 'screens/dosen/exam_create_screen.dart' as dosen_exam_create;
import 'screens/dosen/exam_detail_screen.dart' as dosen_exam_detail;
import 'screens/dosen/exam_question_screen.dart' as dosen_exam_question;
import 'screens/dosen/exam_results_screen.dart' as dosen_exam_results;
import 'screens/dosen/exam_grade_screen.dart' as dosen_exam_grade;
import 'screens/admin/krs_approval_list_screen.dart';
import 'screens/admin/krs_approval_detail_screen.dart';
import 'screens/admin/mahasiswa_list_screen.dart';
import 'screens/admin/mahasiswa_form_screen.dart';
import 'screens/admin/mahasiswa_detail_screen.dart';
import 'screens/forum/forum_list_screen.dart';
import 'screens/forum/forum_detail_screen.dart';
import 'screens/forum/forum_create_screen.dart';
import 'screens/qna/qna_list_screen.dart';
import 'screens/qna/qna_detail_screen.dart';
import 'screens/qna/qna_create_screen.dart';
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
      theme: ThemeData(primarySwatch: Colors.blue, useMaterial3: true),
      routerConfig: _router,
    );
  }
}

final GoRouter _router = GoRouter(
  initialLocation: '/login',
  redirect: (context, state) async {
    // Check if user is already logged in
    final token = await StorageService.getToken();
    final user = await StorageService.getUser();

    final isLoggingIn = state.matchedLocation == '/login';

    // If user is already logged in and trying to access login, redirect to dashboard
    if (isLoggingIn && token != null && user != null) {
      final role = user['role'] as String?;
      switch (role) {
        case 'admin':
          return '/admin/dashboard';
        case 'dosen':
          return '/dosen/dashboard';
        case 'mahasiswa':
          return '/mahasiswa/dashboard';
        default:
          return '/dashboard';
      }
    }

    // If user is not logged in and trying to access protected route, redirect to login
    if (!isLoggingIn && token == null) {
      return '/login';
    }

    return null; // No redirect needed
  },
  routes: [
    GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
    GoRoute(
      path: '/dashboard',
      builder: (context, state) => const DashboardRouter(),
    ),
    GoRoute(
      path: '/admin/dashboard',
      builder: (context, state) => const AdminDashboard(),
    ),
    // Admin KRS Approval routes
    GoRoute(
      path: '/admin/krs',
      builder: (context, state) => const KRSApprovalListScreen(),
    ),
    GoRoute(
      path: '/admin/krs/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('KRS ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('KRS ID tidak valid')),
          );
        }
        return KRSApprovalDetailScreen(krsId: id);
      },
    ),
    // Admin Mahasiswa CRUD routes
    GoRoute(
      path: '/admin/mahasiswa',
      builder: (context, state) => const MahasiswaListScreen(),
    ),
    GoRoute(
      path: '/admin/mahasiswa/create',
      builder: (context, state) => const MahasiswaFormScreen(),
    ),
    GoRoute(
      path: '/admin/mahasiswa/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Mahasiswa ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Mahasiswa ID tidak valid')),
          );
        }
        return MahasiswaDetailScreen(mahasiswaId: id);
      },
    ),
    GoRoute(
      path: '/admin/mahasiswa/:id/edit',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Mahasiswa ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Mahasiswa ID tidak valid')),
          );
        }
        return MahasiswaFormScreen(mahasiswaId: id);
      },
    ),
    GoRoute(
      path: '/dosen/dashboard',
      builder: (context, state) => const DosenDashboard(),
    ),
    GoRoute(
      path: '/mahasiswa/dashboard',
      builder: (context, state) => const MahasiswaDashboard(),
    ),
    // Profile routes (for all roles)
    GoRoute(
      path: '/profile',
      builder: (context, state) => const ProfileScreen(),
    ),
    // Mahasiswa routes
    GoRoute(
      path: '/mahasiswa/krs',
      builder: (context, state) => const KRSListScreen(),
    ),
    GoRoute(
      path: '/mahasiswa/krs/add',
      builder: (context, state) => const KRSAddScreen(),
    ),
    GoRoute(
      path: '/mahasiswa/khs',
      builder: (context, state) => const KHSScreen(),
    ),
    // Dosen routes
    GoRoute(
      path: '/dosen/nilai',
      builder: (context, state) => const NilaiListScreen(),
    ),
    GoRoute(
      path: '/dosen/nilai/input/:jadwalId',
      builder: (context, state) {
        final jadwalIdStr = state.pathParameters['jadwalId'];
        if (jadwalIdStr == null) {
          return const Scaffold(
            body: Center(child: Text('Jadwal ID tidak valid')),
          );
        }
        final jadwalId = int.tryParse(jadwalIdStr);
        if (jadwalId == null) {
          return const Scaffold(
            body: Center(child: Text('Jadwal ID tidak valid')),
          );
        }
        return NilaiInputScreen(jadwalId: jadwalId);
      },
    ),
    GoRoute(
      path: '/dosen/presensi',
      builder: (context, state) => const dosen_presensi.PresensiListScreen(),
    ),
    // Assignment Dosen routes
    GoRoute(
      path: '/dosen/assignment',
      builder: (context, state) =>
          const dosen_assignment.AssignmentListScreen(),
    ),
    GoRoute(
      path: '/dosen/assignment/create',
      builder: (context, state) {
        final jadwalIdStr = state.uri.queryParameters['jadwal_id'];
        final jadwalId = jadwalIdStr != null ? int.tryParse(jadwalIdStr) : null;
        return dosen_assignment_create.AssignmentCreateScreen(
          jadwalId: jadwalId,
        );
      },
    ),
    GoRoute(
      path: '/dosen/assignment/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Assignment ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Assignment ID tidak valid')),
          );
        }
        return dosen_assignment_detail.AssignmentDetailScreen(assignmentId: id);
      },
    ),
    GoRoute(
      path: '/dosen/assignment/:id/edit',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Assignment ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Assignment ID tidak valid')),
          );
        }
        return dosen_assignment_create.AssignmentCreateScreen(assignmentId: id);
      },
    ),
    GoRoute(
      path: '/dosen/assignment/:assignmentId/grade/:submissionId',
      builder: (context, state) {
        final assignmentIdStr = state.pathParameters['assignmentId'];
        final submissionIdStr = state.pathParameters['submissionId'];
        if (assignmentIdStr == null || submissionIdStr == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        final assignmentId = int.tryParse(assignmentIdStr);
        final submissionId = int.tryParse(submissionIdStr);
        if (assignmentId == null || submissionId == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        return dosen_assignment_grade.AssignmentGradeScreen(
          assignmentId: assignmentId,
          submissionId: submissionId,
        );
      },
    ),
    // Exam Dosen routes
    GoRoute(
      path: '/dosen/exam',
      builder: (context, state) => const dosen_exam.ExamListScreen(),
    ),
    GoRoute(
      path: '/dosen/exam/create',
      builder: (context, state) {
        final jadwalIdStr = state.uri.queryParameters['jadwal_id'];
        final jadwalId = jadwalIdStr != null ? int.tryParse(jadwalIdStr) : null;
        return dosen_exam_create.ExamCreateScreen(jadwalId: jadwalId);
      },
    ),
    GoRoute(
      path: '/dosen/exam/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        return dosen_exam_detail.ExamDetailScreen(examId: id);
      },
    ),
    GoRoute(
      path: '/dosen/exam/:id/edit',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        return dosen_exam_create.ExamCreateScreen(examId: id);
      },
    ),
    GoRoute(
      path: '/dosen/exam/:examId/question/add',
      builder: (context, state) {
        final examIdStr = state.pathParameters['examId'];
        if (examIdStr == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        final examId = int.tryParse(examIdStr);
        if (examId == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        return dosen_exam_question.ExamQuestionScreen(examId: examId);
      },
    ),
    GoRoute(
      path: '/dosen/exam/:examId/question/:questionId',
      builder: (context, state) {
        final examIdStr = state.pathParameters['examId'];
        final questionIdStr = state.pathParameters['questionId'];
        if (examIdStr == null || questionIdStr == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        final examId = int.tryParse(examIdStr);
        final questionId = int.tryParse(questionIdStr);
        if (examId == null || questionId == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        return dosen_exam_question.ExamQuestionScreen(
          examId: examId,
          questionId: questionId,
        );
      },
    ),
    GoRoute(
      path: '/dosen/exam/:examId/results',
      builder: (context, state) {
        final examIdStr = state.pathParameters['examId'];
        if (examIdStr == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        final examId = int.tryParse(examIdStr);
        if (examId == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        return dosen_exam_results.ExamResultsScreen(examId: examId);
      },
    ),
    GoRoute(
      path: '/dosen/exam/:examId/grade/:sessionId',
      builder: (context, state) {
        final examIdStr = state.pathParameters['examId'];
        final sessionIdStr = state.pathParameters['sessionId'];
        if (examIdStr == null || sessionIdStr == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        final examId = int.tryParse(examIdStr);
        final sessionId = int.tryParse(sessionIdStr);
        if (examId == null || sessionId == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        return dosen_exam_grade.ExamGradeScreen(
          examId: examId,
          sessionId: sessionId,
        );
      },
    ),
    GoRoute(
      path: '/dosen/presensi/input/:jadwalId',
      builder: (context, state) {
        final jadwalIdStr = state.pathParameters['jadwalId'];
        if (jadwalIdStr == null) {
          return const Scaffold(
            body: Center(child: Text('Jadwal ID tidak valid')),
          );
        }
        final jadwalId = int.tryParse(jadwalIdStr);
        if (jadwalId == null) {
          return const Scaffold(
            body: Center(child: Text('Jadwal ID tidak valid')),
          );
        }
        return PresensiInputScreen(jadwalId: jadwalId);
      },
    ),
    // Notifikasi routes (for all roles)
    GoRoute(
      path: '/notifikasi',
      builder: (context, state) => const NotifikasiScreen(),
    ),
    // Pengumuman routes (for all roles)
    GoRoute(
      path: '/pengumuman',
      builder: (context, state) => const PengumumanListScreen(),
    ),
    GoRoute(
      path: '/pengumuman/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Pengumuman ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Pengumuman ID tidak valid')),
          );
        }
        return PengumumanDetailScreen(pengumumanId: id);
      },
    ),
    // Chat routes (for all roles)
    GoRoute(
      path: '/chat',
      builder: (context, state) => const ConversationListScreen(),
    ),
    GoRoute(
      path: '/chat/create',
      builder: (context, state) => const CreateConversationScreen(),
    ),
    GoRoute(
      path: '/chat/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Conversation ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Conversation ID tidak valid')),
          );
        }
        return ChatDetailScreen(conversationId: id);
      },
    ),
    // Payment routes (for all roles)
    GoRoute(
      path: '/payment',
      builder: (context, state) => const PaymentListScreen(),
    ),
    GoRoute(
      path: '/payment/create',
      builder: (context, state) => const PaymentCreateScreen(),
    ),
    GoRoute(
      path: '/payment/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Payment ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Payment ID tidak valid')),
          );
        }
        return PaymentDetailScreen(paymentId: id);
      },
    ),
    // Presensi Mahasiswa routes
    GoRoute(
      path: '/mahasiswa/presensi',
      builder: (context, state) =>
          const mahasiswa_presensi.PresensiListScreen(),
    ),
    GoRoute(
      path: '/mahasiswa/presensi/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Jadwal ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Jadwal ID tidak valid')),
          );
        }
        return PresensiDetailScreen(jadwalId: id);
      },
    ),
    // Assignment Mahasiswa routes
    GoRoute(
      path: '/mahasiswa/assignment',
      builder: (context, state) => const AssignmentListScreen(),
    ),
    GoRoute(
      path: '/mahasiswa/assignment/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Assignment ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Assignment ID tidak valid')),
          );
        }
        return AssignmentDetailScreen(assignmentId: id);
      },
    ),
    // Exam Mahasiswa routes
    GoRoute(
      path: '/mahasiswa/exam',
      builder: (context, state) => const ExamListScreen(),
    ),
    GoRoute(
      path: '/mahasiswa/exam/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Exam ID tidak valid')),
          );
        }
        return ExamDetailScreen(examId: id);
      },
    ),
    GoRoute(
      path: '/mahasiswa/exam/:examId/take/:sessionId',
      builder: (context, state) {
        final examIdStr = state.pathParameters['examId'];
        final sessionIdStr = state.pathParameters['sessionId'];
        if (examIdStr == null || sessionIdStr == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        final examId = int.tryParse(examIdStr);
        final sessionId = int.tryParse(sessionIdStr);
        if (examId == null || sessionId == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        return ExamTakeScreen(examId: examId, sessionId: sessionId);
      },
    ),
    GoRoute(
      path: '/mahasiswa/exam/:examId/result/:sessionId',
      builder: (context, state) {
        final examIdStr = state.pathParameters['examId'];
        final sessionIdStr = state.pathParameters['sessionId'];
        if (examIdStr == null || sessionIdStr == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        final examId = int.tryParse(examIdStr);
        final sessionId = int.tryParse(sessionIdStr);
        if (examId == null || sessionId == null) {
          return const Scaffold(body: Center(child: Text('ID tidak valid')));
        }
        return ExamResultScreen(examId: examId, sessionId: sessionId);
      },
    ),
    // Forum routes (for all roles)
    GoRoute(
      path: '/forum',
      builder: (context, state) => const ForumListScreen(),
    ),
    GoRoute(
      path: '/forum/create',
      builder: (context, state) => const ForumCreateScreen(),
    ),
    GoRoute(
      path: '/forum/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Forum Topic ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Forum Topic ID tidak valid')),
          );
        }
        return ForumDetailScreen(topicId: id);
      },
    ),
    // Q&A routes (for all roles)
    GoRoute(path: '/qna', builder: (context, state) => const QnAListScreen()),
    GoRoute(
      path: '/qna/create',
      builder: (context, state) => const QnACreateScreen(),
    ),
    GoRoute(
      path: '/qna/:id',
      builder: (context, state) {
        final idStr = state.pathParameters['id'];
        if (idStr == null) {
          return const Scaffold(
            body: Center(child: Text('Question ID tidak valid')),
          );
        }
        final id = int.tryParse(idStr);
        if (id == null) {
          return const Scaffold(
            body: Center(child: Text('Question ID tidak valid')),
          );
        }
        return QnADetailScreen(questionId: id);
      },
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
