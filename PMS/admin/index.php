<?php
session_start();
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required!';
    } else {
        // Check database connection first
        if (!$conn) {
            $error = 'Database connection failed!';
        } else {
            $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = 'admin'");
            
            if (!$stmt) {
                $error = 'Database error: ' . $conn->error;
            } else {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $admin = $result->fetch_assoc();
                    
                    // Check if password is correct - handle both old MD5 and new password_hash
                    $password_correct = false;
                    
                    // First try password_verify for new hashed passwords
                    if (password_verify($password, $admin['password'])) {
                        $password_correct = true;
                    }
                    // If that fails, try MD5 for legacy passwords
                    elseif (md5($password) === $admin['password']) {
                        $password_correct = true;
                    }
                    
                    if ($password_correct) {
                        $_SESSION['user_id'] = $admin['id'];
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $admin['role'];
                        header('Location: admin_dashboard.php');
                        exit();
                    } else {
                        $error = 'Invalid username or password!';
                    }
                } else {
                    $error = 'Admin not found or incorrect role!';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS - Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS1/adminlogin.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4 login-container">
                <h3 class="text-center ">Parking Management System</h3>
                <h4 class="text-center mb-4">Login Here</h4>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="username" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>