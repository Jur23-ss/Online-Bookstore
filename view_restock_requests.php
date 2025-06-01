<?php
session_start();
include 'includes/db_connect.php';

// Only Admins
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'Admin') {
    die("Access denied.");
}

// Handle approval or deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $requestId = (int)$_POST['request_id'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE restock_requests SET status = 'Approved' WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM restock_requests WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
    }
}

$requests = $conn->query("SELECT rr.id, b.title AS book_title, rr.requested_stock, u.name AS librarian_name, rr.status
                          FROM restock_requests rr
                          JOIN books b ON rr.book_id = b.id
                          JOIN users u ON rr.librarian_id = u.id
                          ORDER BY rr.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Restock Requests</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        h2 {
            color: #4a7c59;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1c1c1c;
            margin-top: 1rem;
        }
        th, td {
            padding: 1rem;
            border-bottom: 1px solid #333;
        }
        th {
            background: #292929;
        }
        .actions form {
            display: inline;
        }
        .actions button {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .approve {
            background-color: #4a7c59;
            color: white;
        }
        .delete {
            background-color: #ff4d4d;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Restock Requests</h2>
    <a href="admin.php" style="color: #90ee90; text-decoration: none;">⬅ Back to Admin Panel</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Book</th>
                <th>Requested Qty</th>
                <th>Librarian</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $requests->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['book_title']) ?></td>
                <td><?= $row['requested_stock'] ?></td>
                <td><?= htmlspecialchars($row['librarian_name']) ?></td>
                <td><?= $row['status'] ?></td>
                <td class="actions">
                    <?php if ($row['status'] === 'Pending'): ?>
                        <form method="post">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="approve" class="approve">Approve</button>
                        </form>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="action" value="delete" class="delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
