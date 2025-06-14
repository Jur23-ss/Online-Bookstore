<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #2e3b2e;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        h2 {
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            background: #4a7c59;
            color: white;
            padding: 0.6rem 1.2rem;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .btn:hover {
            background: #385b44;
        }
        .actions a {
            margin-right: 1rem;
            color: #66d9a6;
            text-decoration: none;
            font-weight: bold;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .actions a.delete {
            color: #ff4d4d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1e261e;
        }
        th, td {
            padding: 0.8rem;
            border: 1px solid #444;
            text-align: left;
        }
        thead {
            background-color: #3b4d3b;
        }
    </style>
</head>
<body>

<h2>Manage Users</h2>
<a class="btn" href="admin.php" style="margin-bottom: 1rem; display: inline-block; background-color: #4a7c59; color: white; font-weight: bold; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none;">⬅ Back to Admin Panel</a>
<a href="add_user.php" class="btn">➕ Add New User</a>

<table>
    <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['role'] ?></td>
                <td class="actions">
                    <a href="edit_user.php?id=<?= $user['id'] ?>">Edit</a>
                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
