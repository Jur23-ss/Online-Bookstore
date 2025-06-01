<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'Admin' && $role !== 'Librarian') {
    die("Access denied. Admins or Librarians only.");
}
?>
