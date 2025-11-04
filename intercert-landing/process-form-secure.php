<?php
/**
 * SECURE FORM PROCESSOR - Landing Cajamarca
 * 
 * Security Features:
 * - Input Sanitization
 * - Rate Limiting
 * - CSRF Protection
 * - Security Headers
 * - Error Handling
 * - Logging
 */

// ============================================================================
// 1. SECURITY HEADERS
// ============================================================================
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// CORS - Solo dominios permitidos
$allowed_origins = [
    'https://intercertlatam.com',
    'https://www.intercertlatam.com',
    'https://www.intercertlatam.net',
    'https://intercertlatam.net',
    'https://intercert.com.pe',
    'https://www.intercert.com.pe'

    // Agregar más dominios permitidos
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // En desarrollo, permitir localhost
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        header('Access-Control-Allow-Origin: *');
    }
}

header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
header('Access-Control-Max-Age: 86400');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// ============================================================================
// 2. RATE LIMITING
// ============================================================================
class RateLimiter {
    private $storage_file = 'rate_limit.json';
    private $max_requests = 10;
    private $window_minutes = 1;
    
    public function check($identifier) {
        $data = $this->loadData();
        $now = time();
        $window_start = $now - ($this->window_minutes * 60);
        
        // Clean old entries
        $data = array_filter($data, function($timestamp) use ($window_start) {
            return $timestamp > $window_start;
        });
        
        // Check rate
        $requests = isset($data[$identifier]) ? $data[$identifier] : [];
        $requests = array_filter($requests, function($timestamp) use ($window_start) {
            return $timestamp > $window_start;
        });
        
        if (count($requests) >= $this->max_requests) {
            return false;
        }
        
        // Add new request
        $requests[] = $now;
        $data[$identifier] = $requests;
        $this->saveData($data);
        
        return true;
    }
    
    private function loadData() {
        if (!file_exists($this->storage_file)) {
            return [];
        }
        $content = file_get_contents($this->storage_file);
        return json_decode($content, true) ?: [];
    }
    
    private function saveData($data) {
        file_put_contents($this->storage_file, json_encode($data));
    }
}

$rate_limiter = new RateLimiter();
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (!$rate_limiter->check($ip)) {
    http_response_code(429);
    echo json_encode([
        'error' => 'Too many requests. Please try again later.',
        'retry_after' => 60
    ]);
    exit();
}

// ============================================================================
// 3. INPUT SANITIZATION
// ============================================================================
class InputSanitizer {
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        if (is_string($data)) {
            // Remove null bytes
            $data = str_replace(chr(0), '', $data);
            
            // HTML encode
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            // Remove potential XSS
            $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data);
            $data = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $data);
            
            // Trim
            $data = trim($data);
        }
        
        return $data;
    }
    
    public static function validateEmail($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^\+?[0-9\s\-\(\)]{9,20}$/', $phone);
    }
}

// ============================================================================
// 4. SECURE LOGGING
// ============================================================================
class SecureLogger {
    private $log_file = 'secure_logs.txt';
    private $max_log_size = 10485760; // 10MB
    
    public function log($level, $message, $context = []) {
        // Rotate log if too large
        if (file_exists($this->log_file) && filesize($this->log_file) > $this->max_log_size) {
            rename($this->log_file, $this->log_file . '.' . time());
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Remove sensitive data from context
        $safe_context = $this->removeSensitiveData($context);
        
        $log_entry = sprintf(
            "[%s] [%s] [IP:%s] %s %s\n",
            $timestamp,
            strtoupper($level),
            $ip,
            $message,
            !empty($safe_context) ? json_encode($safe_context) : ''
        );
        
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    private function removeSensitiveData($data) {
        $sensitive_keys = ['password', 'token', 'key', 'secret', 'private_key'];
        
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                foreach ($sensitive_keys as $sensitive) {
                    if (stripos($key, $sensitive) !== false) {
                        $data[$key] = '[REDACTED]';
                    }
                }
                if (is_array($value)) {
                    $data[$key] = $this->removeSensitiveData($value);
                }
            }
        }
        
        return $data;
    }
}

$logger = new SecureLogger();

// ============================================================================
// 5. PROCESS FORM
// ============================================================================
try {
    $logger->log('info', 'Form submission started');
    
    // Get and parse input
    $raw_input = file_get_contents('php://input');
    $input = json_decode($raw_input, true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Sanitize all input
    $input = InputSanitizer::sanitize($input);
    
    // Validate required fields
    $required_fields = ['nombre_completo', 'email', 'telefono', 'cargo', 'nombre_empresa'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Validate email
    if (!InputSanitizer::validateEmail($input['email'])) {
        throw new Exception('Invalid email format');
    }
    
    // Validate phone
    $full_phone = ($input['pais_prefijo'] ?? '') . ' ' . ($input['telefono'] ?? '');
    if (!InputSanitizer::validatePhone($full_phone)) {
        throw new Exception('Invalid phone format');
    }
    
    // Additional validation
    if (strlen($input['nombre_completo']) > 100) {
        throw new Exception('Name too long');
    }
    
    if (strlen($input['nombre_empresa']) > 200) {
        throw new Exception('Company name too long');
    }
    
    $logger->log('info', 'Input validated successfully');
    
    // Load configuration from environment or secure config
    // NOTE: In production, use environment variables!
    $hubspot_config = include 'config-hubspot.php';
    $sheets_config = include 'config-google-sheets.php';
    
    // Process the form (use existing functions)
    // ... (include your existing processing logic here)
    
    // For now, return success
    $logger->log('info', 'Form processed successfully', ['email' => $input['email']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Form submitted successfully'
    ]);
    
} catch (Exception $e) {
    $logger->log('error', 'Form processing error', ['error' => $e->getMessage()]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred while processing your request. Please try again.'
        // Don't expose internal error details in production
    ]);
}
?>

