import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import 'storage_service.dart';

class ApiService {
  // Login
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/login'),
        headers: ApiConfig.getHeaders(null),
        body: jsonEncode({
          'email': email,
          'password': password,
        }),
      ).timeout(ApiConfig.connectTimeout);
      
      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['success'] == true) {
        // Save token
        await StorageService.saveToken(data['data']['token']);
        // Save user data
        if (data['data']['user'] != null) {
          await StorageService.saveUser(data['data']['user']);
        }
        
        return {
          'success': true,
          'data': data['data'],
        };
      } else {
        return {
          'success': false,
          'message': data['message'] ?? 'Login gagal',
        };
      }
    } catch (e) {
      return {
        'success': false,
        'message': 'Error: ${e.toString()}',
      };
    }
  }
  
  // Logout
  static Future<bool> logout() async {
    try {
      final token = await StorageService.getToken();
      if (token == null) return false;
      
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}/logout'),
        headers: ApiConfig.getHeaders(token),
      ).timeout(ApiConfig.connectTimeout);
      
      await StorageService.clearAll();
      
      return response.statusCode == 200;
    } catch (e) {
      await StorageService.clearAll();
      return false;
    }
  }
  
  // Get Dashboard
  static Future<Map<String, dynamic>> getDashboard() async {
    try {
      final token = await StorageService.getToken();
      if (token == null) {
        throw Exception('Not authenticated');
      }
      
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/dashboard'),
        headers: ApiConfig.getHeaders(token),
      ).timeout(ApiConfig.connectTimeout);
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'data': data['data'],
        };
      } else if (response.statusCode == 401) {
        // Token expired
        await StorageService.clearAll();
        throw Exception('Session expired');
      } else {
        throw Exception('Failed to load dashboard');
      }
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  // Get Current User
  static Future<Map<String, dynamic>> getCurrentUser() async {
    try {
      final token = await StorageService.getToken();
      if (token == null) {
        throw Exception('Not authenticated');
      }
      
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}/user'),
        headers: ApiConfig.getHeaders(token),
      ).timeout(ApiConfig.connectTimeout);
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'data': data['data'],
        };
      } else {
        throw Exception('Failed to get user');
      }
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  // Generic GET request
  static Future<Map<String, dynamic>> get(String endpoint) async {
    try {
      final token = await StorageService.getToken();
      if (token == null) {
        throw Exception('Not authenticated');
      }
      
      final response = await http.get(
        Uri.parse('${ApiConfig.baseUrl}$endpoint'),
        headers: ApiConfig.getHeaders(token),
      ).timeout(ApiConfig.connectTimeout);
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'data': data['data'] ?? data,
        };
      } else if (response.statusCode == 401) {
        await StorageService.clearAll();
        throw Exception('Session expired');
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Request failed');
      }
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  // Generic POST request
  static Future<Map<String, dynamic>> post(String endpoint, Map<String, dynamic> body) async {
    try {
      final token = await StorageService.getToken();
      if (token == null && endpoint != '/login') {
        throw Exception('Not authenticated');
      }
      
      final response = await http.post(
        Uri.parse('${ApiConfig.baseUrl}$endpoint'),
        headers: ApiConfig.getHeaders(token),
        body: jsonEncode(body),
      ).timeout(ApiConfig.connectTimeout);
      
      if (response.statusCode >= 200 && response.statusCode < 300) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'data': data['data'] ?? data,
        };
      } else if (response.statusCode == 401) {
        await StorageService.clearAll();
        throw Exception('Session expired');
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Request failed');
      }
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  // Generic PUT request
  static Future<Map<String, dynamic>> put(String endpoint, Map<String, dynamic> body) async {
    try {
      final token = await StorageService.getToken();
      if (token == null) {
        throw Exception('Not authenticated');
      }
      
      final response = await http.put(
        Uri.parse('${ApiConfig.baseUrl}$endpoint'),
        headers: ApiConfig.getHeaders(token),
        body: jsonEncode(body),
      ).timeout(ApiConfig.connectTimeout);
      
      if (response.statusCode >= 200 && response.statusCode < 300) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'data': data['data'] ?? data,
        };
      } else if (response.statusCode == 401) {
        await StorageService.clearAll();
        throw Exception('Session expired');
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Request failed');
      }
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
  
  // Generic DELETE request
  static Future<Map<String, dynamic>> delete(String endpoint) async {
    try {
      final token = await StorageService.getToken();
      if (token == null) {
        throw Exception('Not authenticated');
      }
      
      final response = await http.delete(
        Uri.parse('${ApiConfig.baseUrl}$endpoint'),
        headers: ApiConfig.getHeaders(token),
      ).timeout(ApiConfig.connectTimeout);
      
      if (response.statusCode >= 200 && response.statusCode < 300) {
        final data = jsonDecode(response.body);
        return {
          'success': true,
          'data': data['data'] ?? data,
        };
      } else if (response.statusCode == 401) {
        await StorageService.clearAll();
        throw Exception('Session expired');
      } else {
        final data = jsonDecode(response.body);
        throw Exception(data['message'] ?? 'Request failed');
      }
    } catch (e) {
      return {
        'success': false,
        'message': e.toString(),
      };
    }
  }
}

