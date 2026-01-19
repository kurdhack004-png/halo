<?php
// test_ssrf.php - Test endpoint for SSRF/SSTI vulnerabilities
// WARNING: For authorized security testing only

// Allow from any origin for testing
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html");

// Check if a URL parameter is provided
if(isset($_GET['url'])) {
    $url = $_GET['url'];
    
    // Basic check (in real testing, you'd want more validation)
    if(filter_var($url, FILTER_VALIDATE_URL)) {
        try {
            $content = file_get_contents($url);
            echo htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        } catch(Exception $e) {
            echo "Error fetching URL: " . htmlspecialchars($e->getMessage());
        }
    } else {
        echo "Invalid URL format";
    }
} else {
    // Default behavior - show page info
    echo "<h1>SSRF Test Endpoint</h1>";
    echo "<p>Use ?url= parameter to test URL fetching</p>";
    echo "<p>Example: ?url=http://example.com</p>";
    echo "<p>Local test: ?url=http://127.0.0.1:5000/logs</p>";
}
?>
