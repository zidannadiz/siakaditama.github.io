class ApiConfig {
  // Development
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  
  // Production (update saat deploy)
  // static const String baseUrl = 'https://yourdomain.com/api';
  
  // Timeout
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  
  // Headers
  static Map<String, String> getHeaders(String? token) {
    final headers = <String, String>{
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    
    return headers;
  }
}

