<?php
session_start();
include 'includes/db_connect.php';
include 'librarian_check.php';

$userId = $_SESSION['user_id'];

$requests = $conn->prepare("SELECT rr.id, b.title, rr.requested_stock, rr.status, rr.requested_at
                            FROM restock_requests rr
                            JOIN books b ON rr.book_id = b.id
                            WHERE rr.librarian_id = ?
                            ORDER BY rr.requested_at DESC");
$requests->bind_param("i", $userId);
$requests->execute();
$result = $requests->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Restock Requests</title>
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
            margin-bottom: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1c1c1c;
        }
        th, td {
            padding: 1rem;
            border-bottom: 1px solid #333;
        }
        th {
            background: #292929;
        }
        .btn {
            background: #4a7c59;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 1rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h2>My Restock Requests</h2>
    <a href="librarianDashboard.php" class="btn">⬅ Back to Dashboard</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Book</th>
                <th>Requested Qty</th>
                <th>Status</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['requested_stock'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['requested_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
