<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prevent deleting yourself
    if ($_SESSION['user_id'] == $id) {
        die("❌ You cannot delete your own account.");
    }

    // Optional: Protect super admin (ID = 1)
    if ($id === 1) {
        die("❌ You cannot delete the primary Admin.");
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: manage_users.php");
exit;
