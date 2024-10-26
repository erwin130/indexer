<?php
// includes/config.php

// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi dasar
define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('UPLOAD_PATH', BASE_PATH . '/uploads/');

// Konfigurasi keamanan
define('MAX_DOMAINS_PER_REQUEST', 50); // Maksimum domain yang bisa dicek sekaligus
define('REQUEST_TIMEOUT', 10); // Timeout dalam detik

// Headers untuk CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Fungsi helper
function sanitize_output($buffer) {
    return htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk validasi input
function validate_domains($domains) {
    if (!is_array($domains)) {
        return false;
    }
    
    if (count($domains) > MAX_DOMAINS_PER_REQUEST) {
        return false;
    }
    
    return true;
}

// Fungsi untuk logging
function log_check($domain, $result) {
    $log_file = BASE_PATH . '/logs/checks.log';
    $log_entry = date('Y-m-d H:i:s') . " | Domain: $domain | Result: " . json_encode($result) . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}
?>