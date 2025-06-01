<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Librarian') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: #0b0b0b;
            color: white;
        }
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 100px;
            z-index: 10;
        }
        .logo {
            font-size: 1.3rem;
            color: white;
            font-weight: bold;
        }
        .navbar {
            display: flex;
            gap: 2rem;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            position: relative;
        }
        .navbar a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background: #4a7c59;
            transition: 0.3s;
        }
        .navbar a:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">Librarian Panel</div>
    <nav class="navbar">
        <a class="btn" href="manage_books.php">Manage Books</a>
        <a class="btn" href="track_inventory.php">Track Inventory</a>
        <a class="btn" href="request_restock.php">Request Restock</a>
        <a class="btn" href="my_restock_requests.php">My Restock Requests</a>
        <a class="btn" href="index.php">Homepage</a>
        <a class="btn" href="logout.php">Logout</a>
    </nav>
</header>

</body>
</html>
