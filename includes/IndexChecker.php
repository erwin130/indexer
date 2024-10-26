<?php
class IndexChecker {
    private $userAgent;
    private $lastError;
    
    public function __construct() {
        $this->userAgent = 'Mozilla/5.0 (compatible; IndexCheckerBot/1.0; +http://example.com/bot)';
        $this->lastError = null;
    }
    
    /**
     * Check if domain is indexed in search engines
     * @param string $domain Domain to check
     * @return array Status for each search engine
     */
    public function checkDomain($domain) {
        $domain = $this->sanitizeDomain($domain);
        
        if (!$this->isValidDomain($domain)) {
            throw new Exception("Invalid domain format: $domain");
        }

        try {
            $googleStatus = $this->checkGoogle($domain);
        } catch (Exception $e) {
            $googleStatus = false;
            $this->lastError = "Google check failed: " . $e->getMessage();
        }

        try {
            $bingStatus = $this->checkBing($domain);
        } catch (Exception $e) {
            $bingStatus = false;
            $this->lastError = "Bing check failed: " . $e->getMessage();
        }
        
        return [
            'domain' => $domain,
            'google' => $googleStatus,
            'bing' => $bingStatus,
            'error' => $this->lastError
        ];
    }
    
    /**
     * Check Google indexing using scraping
     * @param string $domain Domain to check
     * @return bool Indexed status
     */
    private function checkGoogle($domain) {
        $query = "site:" . urlencode($domain);
        $url = "https://www.google.com/search?q={$query}&hl=en";
        
        $response = $this->makeRequest($url);
        
        // Cek berbagai indikator "tidak ada hasil"
        $noResultsIndicators = [
            'did not match any documents',
            'no results found',
            'không tìm thấy kết quả nào',
            'No results found'
        ];
        
        foreach ($noResultsIndicators as $indicator) {
            if (stripos($response, $indicator) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check Bing indexing using scraping
     * @param string $domain Domain to check
     * @return bool Indexed status
     */
    private function checkBing($domain) {
        $query = "site:" . urlencode($domain);
        $url = "https://www.bing.com/search?q={$query}&setlang=en";
        
        $response = $this->makeRequest($url);
        
        // Cek berbagai indikator "tidak ada hasil"
        $noResultsIndicators = [
            'no results found',
            'we did not find any results',
            'There are no results'
        ];
        
        foreach ($noResultsIndicators as $indicator) {
            if (stripos($response, $indicator) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Make HTTP request with proper headers
     * @param string $url URL to request
     * @return string Response content
     */
    private function makeRequest($url) {
        $ch = curl_init();
        
        $headers = [
            'Accept-Language: en-US,en;q=0.9',
            'Cache-Control: no-cache',
            'Pragma: no-cache'
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => REQUEST_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_ENCODING => '',
            CURLOPT_COOKIESESSION => true,
            CURLOPT_FRESH_CONNECT => true
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Request failed: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new Exception("HTTP request failed with code $httpCode");
        }
        
        curl_close($ch);
        return $response;
    }
    
    /**
     * Validate domain format
     * @param string $domain Domain to validate
     * @return bool Validation result
     */
    private function isValidDomain($domain) {
        // Validasi format domain yang lebih ketat
        $pattern = '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/';
        return (bool) preg_match($pattern, $domain);
    }
    
    /**
     * Sanitize domain input
     * @param string $domain Domain to sanitize
     * @return string Sanitized domain
     */
    private function sanitizeDomain($domain) {
        // Bersihkan domain dari whitespace dan protokol
        $domain = trim($domain);
        $domain = strtolower($domain);
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = rtrim($domain, '/');
        return $domain;
    }
}