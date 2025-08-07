<?php
// Comprehensive test script for Parking Management System
echo "<h1>Parking Management System - System Test</h1>";
echo "<hr>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    require_once 'db_connection.php';
    echo "✓ Database connection successful<br>";
    echo "✓ Database: " . $dbname . "<br>";
    echo "✓ Server: " . $servername . "<br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 2: Check Required Tables
echo "<h2>2. Database Tables Check</h2>";
$required_tables = ['users', 'vehicles', 'parking_spots', 'usage'];
foreach ($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' missing<br>";
    }
}
echo "<hr>";

// Test 3: Test User Authentication
echo "<h2>3. User Authentication Test</h2>";
$test_users = [
    ['username' => 'admin', 'password' => 'admin123', 'role' => 'admin'],
    ['username' => 'user', 'password' => 'user123', 'role' => 'user']
];

foreach ($test_users as $test_user) {
    echo "<h3>Testing: {$test_user['username']} ({$test_user['role']})</h3>";
    
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $test_user['username'], $test_user['role']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        echo "✓ User found in database<br>";
        
        // Test password verification
        $password_correct = false;
        
        // Try password_verify first
        if (password_verify($test_user['password'], $user['password'])) {
            $password_correct = true;
            echo "✓ Password verified using password_verify()<br>";
        }
        // Try MD5 if password_verify fails
        elseif (md5($test_user['password']) === $user['password']) {
            $password_correct = true;
            echo "✓ Password verified using MD5<br>";
        }
        
        if ($password_correct) {
            echo "<strong style='color: green;'>✓ LOGIN SUCCESSFUL</strong><br>";
        } else {
            echo "<strong style='color: red;'>✗ LOGIN FAILED - Wrong password</strong><br>";
        }
    } else {
        echo "<strong style='color: red;'>✗ User not found or wrong role</strong><br>";
    }
}
echo "<hr>";

// Test 4: Check File Structure
echo "<h2>4. File Structure Check</h2>";
$required_files = [
    'index.php',
    'dashboard.php',
    'register.php',
    'logout.php',
    'db_connection.php',
    'admin/index.php',
    'admin/admin_dashboard.php',
    'admin/logout.php',
    'CSS/loginform.css',
    'CSS/dashboard.css',
    'admin/CSS1/adminlogin.css',
    'admin/CSS1/dashboard.css'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ File '$file' exists<br>";
    } else {
        echo "✗ File '$file' missing<br>";
    }
}
echo "<hr>";

// Test 5: Session Test
echo "<h2>5. Session Test</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✓ Sessions are working<br>";
} else {
    echo "✗ Sessions are not working<br>";
}
echo "<hr>";

// Test 6: PHP Version and Extensions
echo "<h2>6. PHP Environment Check</h2>";
echo "✓ PHP Version: " . phpversion() . "<br>";
echo "✓ MySQL Extension: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "✓ Session Extension: " . (extension_loaded('session') ? 'Loaded' : 'Not Loaded') . "<br>";
echo "<hr>";

echo "<h2>Test Results Summary</h2>";
echo "<p><strong>If all tests show ✓ marks, your system is ready to use!</strong></p>";
echo "<p><a href='index.php' class='btn btn-primary'>Go to User Login</a> | <a href='admin/index.php' class='btn btn-secondary'>Go to Admin Login</a></p>";

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.btn { display: inline-block; padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; }
.btn-primary { background-color: #007bff; color: white; }
.btn-secondary { background-color: #6c757d; color: white; }
</style>";
?> 