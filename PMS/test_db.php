<?php
// Simple database test file
echo "<h2>Database Connection Test</h2>";

// Test 1: Basic connection
echo "<h3>1. Testing Database Connection</h3>";
try {
    require_once 'db_connection.php';
    echo "✓ Database connection successful<br>";
    echo "Server: " . $servername . "<br>";
    echo "Database: " . $dbname . "<br>";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>";
    exit();
}

// Test 2: Check if users table exists
echo "<h3>2. Testing Users Table</h3>";
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result && $result->num_rows > 0) {
    echo "✓ Users table exists<br>";
} else {
    echo "✗ Users table does not exist<br>";
    exit();
}

// Test 3: Check table structure
echo "<h3>3. Testing Table Structure</h3>";
$result = $conn->query("DESCRIBE users");
if ($result) {
    echo "✓ Users table structure:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }
} else {
    echo "✗ Could not describe users table<br>";
}

// Test 4: Test simple query
echo "<h3>4. Testing Simple Query</h3>";
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "✓ Query successful. Total users: " . $row['count'] . "<br>";
} else {
    echo "✗ Query failed: " . $conn->error . "<br>";
}

// Test 5: Test the specific login query
echo "<h3>5. Testing Login Query</h3>";
$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = 'user'");
if ($stmt) {
    echo "✓ Login query preparation successful<br>";
    
    // Test with a sample username
    $test_username = "user";
    $stmt->bind_param("s", $test_username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "✓ Login query execution successful<br>";
        $user = $result->fetch_assoc();
        echo "Found user: " . $user['username'] ?? 'N/A' . " (ID: " . $user['id'] . ")<br>";
    } else {
        echo "✓ Login query executed but no user found with username 'user'<br>";
    }
} else {
    echo "✗ Login query preparation failed: " . $conn->error . "<br>";
}

// Test 6: Check for specific users
echo "<h3>6. Checking for Default Users</h3>";
$result = $conn->query("SELECT username, role FROM users");
if ($result) {
    echo "✓ Users in database:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['username'] . " (" . $row['role'] . ")<br>";
    }
} else {
    echo "✗ Could not fetch users<br>";
}

echo "<hr>";
echo "<p><a href='index.php'>Go to Login Page</a></p>";
?> 