<?php
require_once '../includes/config.php';
require_once '../includes/IndexChecker.php';

// Set header JSON
header('Content-Type: application/json');

try {
    // Baca raw input
    $rawInput = file_get_contents('php://input');
    if (empty($rawInput)) {
        throw new Exception('No input data received');
    }

    // Decode JSON input
    $input = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON format: ' . json_last_error_msg());
    }

    // Validasi input domains
    if (!isset($input['domains']) || !is_array($input['domains'])) {
        throw new Exception('Invalid input format: domains array required');
    }

    // Filter domain kosong
    $domains = array_filter($input['domains'], function($domain) {
        return !empty(trim($domain));
    });

    if (empty($domains)) {
        throw new Exception('No valid domains provided');
    }

    // Batasi jumlah domain
    if (count($domains) > MAX_DOMAINS_PER_REQUEST) {
        throw new Exception('Too many domains. Maximum ' . MAX_DOMAINS_PER_REQUEST . ' domains allowed');
    }

    // Inisialisasi checker
    $checker = new IndexChecker();
    
    // Proses setiap domain
    $results = [];
    foreach ($domains as $domain) {
        try {
            $result = $checker->checkDomain($domain);
            $results[] = $result;
            
            // Log hasil pengecekan
            log_check($domain, $result);
        } catch (Exception $e) {
            $results[] = [
                'domain' => $domain,
                'error' => $e->getMessage(),
                'google' => false,
                'bing' => false
            ];
        }
    }

    // Return hasil
    echo json_encode([
        'success' => true,
        'data' => $results
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}