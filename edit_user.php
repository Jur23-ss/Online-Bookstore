<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $hashed, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $role, $id);
    }

    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        form {
            max-width: 500px;
            margin: auto;
            background: #1c1c1c;
            padding: 2rem;
            border-radius: 10px;
        }
        h2 {
            color: #4a7c59;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        input, select {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            background: #2a2a2a;
            color: white;
            border: none;
        }
        .btn {
            width: 100%;
            padding: 0.8rem;
            background: #4a7c59;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: rgb(37, 70, 47);
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Edit User</h2>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled>
        <input type="password" name="password" placeholder="New Password (leave blank to keep)">
        <select name="role" required>
            <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="Librarian" <?= $user['role'] === 'Librarian' ? 'selected' : '' ?>>Librarian</option>
            <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
        </select>
        <button class="btn" type="submit">Update User</button>
    </form>
</body>
</html>
