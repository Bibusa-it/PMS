<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-success"><?php echo $_GET['message']; ?></div>
<?php endif; ?>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.html");
    exit();
}


require_once '../db_connection.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Get the user ID from the URL
    $delete_sql = "DELETE FROM users WHERE id = ?";
    
    // Prepare and execute the statement
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        // Redirect back to manage users page with a success message
        header("Location: manage_users.php?message=User deleted successfully");
        exit();
    } else {
        // Handle error if deletion fails
        echo "Error deleting user: " . $conn->error;
    }
} else {
    // Redirect back to manage users page if no user ID is provided
    header("Location: manage_users.php?message=No user ID provided");
    exit();
}
?>
