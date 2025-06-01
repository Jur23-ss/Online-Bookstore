<?php
session_start();
require_once 'includes/db_connect.php';
include 'admin_check.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    if ($name && $email && $password && $role !== '') {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $is_admin     = ($role === 'admin') ? 1 : 0;
        $is_librarian = ($role === 'librarian') ? 1 : 0;

        $query = "INSERT INTO users (name, email, password, is_admin, is_librarian) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sssii", $name, $email, $hashed, $is_admin, $is_librarian);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors[] = "Email already exists or insertion failed.";
            }
            $stmt->close();
        } else {
            $errors[] = "Query preparation failed: " . $conn->error;
        }
    } else {
        $errors[] = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New User</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #2e3b2e;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .form-container {
            max-width: 500px;
            background: #1c1c1c;
            padding: 2rem;
            border-radius: 10px;
            margin: auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #90ee90;
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
            background-color: #4a7c59;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #385b44;
        }
        .error {
            color: #ff6b6b;
            margin-bottom: 1rem;
        }
        .success {
            color: #90ee90;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<h2>Manage Users</h2>

<form method="POST" class="form-container">
    <?php if ($success): ?>
        <div class="success">✅ User added successfully!</div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="role" required>
        <option value="">Select Role</option>
        <option value="user">User</option>
        <option value="librarian">Librarian</option>
        <option value="admin">Admin</option>
    </select>

    <a href="manage_users.php" class="btn" style="margin-bottom: 1.5rem;">← Back to Manage Users</a>
    <button type="submit" class="btn">Add User</button>
</form>

</body>
</html>
