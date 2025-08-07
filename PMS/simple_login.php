<?php
session_start();
require_once 'db_connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $user_type = $_POST['user_type']; // 'admin' or 'user'

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required!';
    } else {
        // Simple query without prepare to test
        $sql = "SELECT * FROM users WHERE username = '$username' AND role = '$user_type'";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check password
            if (md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];
                
                if ($user_type === 'admin') {
                    header('Location: admin/admin_dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                $error = 'Invalid password!';
            }
        } else {
            $error = 'User not found!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Login Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Simple Login Test</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="user_type" class="form-label">Login Type</label>
                                <select name="user_type" id="user_type" class="form-select" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <h4>Test Credentials:</h4>
                    <ul>
                        <li><strong>Admin:</strong> username: admin, password: admin123</li>
                        <li><strong>User:</strong> username: user, password: user123</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <h4>Database Status:</h4>
                    <?php
                    // Check database connection
                    if ($conn) {
                        echo "✓ Database connected<br>";
                        
                        // Check users table
                        $result = $conn->query("SHOW TABLES LIKE 'users'");
                        if ($result && $result->num_rows > 0) {
                            echo "✓ Users table exists<br>";
                            
                            // Count users
                            $result = $conn->query("SELECT COUNT(*) as count FROM users");
                            if ($result) {
                                $row = $result->fetch_assoc();
                                echo "✓ Total users: " . $row['count'] . "<br>";
                                
                                // List users
                                $result = $conn->query("SELECT username, role FROM users");
                                if ($result) {
                                    echo "✓ Users in database:<br>";
                                    while ($row = $result->fetch_assoc()) {
                                        echo "- " . $row['username'] . " (" . $row['role'] . ")<br>";
                                    }
                                }
                            }
                        } else {
                            echo "✗ Users table does not exist<br>";
                        }
                    } else {
                        echo "✗ Database connection failed<br>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 