<?php
session_start();
include 'includes/db_connect.php';

// Verify if user is Librarian or Admin
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

if (strtolower($role) !== 'librarian' && strtolower($role) !== 'admin') {
    die("Access denied. Librarians only.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #2e3b2e;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        h2 {
            color: #90ee90;
            text-align: center;
            margin-bottom: 2rem;
        }
        .dashboard {
            max-width: 600px;
            margin: auto;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }
        .dashboard a {
            display: block;
            text-align: center;
            background: #4a7c59;
            padding: 1rem;
            border-radius: 10px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        .dashboard a:hover {
            background: #385b44;
        }
    </style>
</head>
<body>

<h2>📚 Librarian Dashboard</h2>

<div class="dashboard">
    <a href="add_book.php">➕ Add Book</a>
    <a href="manage_books.php">📘 Manage Books</a>
    <a href="view_orders.php">📦 View Orders</a>
    <a href="index.php">🏠 Back to Homepage</a>
    <a href="logout.php">🚪 Logout</a>
</div>

</body>
</html>
