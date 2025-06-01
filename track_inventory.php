<?php
session_start();
include 'includes/db_connect.php';
include 'librarian_check.php';

$lowStockThreshold = 5;
$showLowOnly = isset($_GET['low']) && $_GET['low'] == '1';

$query = "SELECT id, title, author, book_genre, stock FROM books";
if ($showLowOnly) {
    $query .= " WHERE stock < ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lowStockThreshold);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Stock Tracker</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        h2 {
            color: #4a7c59;
            margin-bottom: 1rem;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        a.btn {
            background: #4a7c59;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1c1c1c;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background: #222;
        }
        tr.low-stock {
            background-color: #442222;
        }
    </style>
</head>
<body>

<h2>Book Stock Tracker</h2>

<div class="top-bar">
    <a href="librarianDashboard.php" class="btn">⬅ Back to Librarian Dashboard</a>
    <a href="track_inventory.php?low=1" class="btn">🔍 Show Low Stock Only</a>
</div>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="<?= $row['stock'] < $lowStockThreshold ? 'low-stock' : '' ?>">
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td><?= htmlspecialchars($row['book_genre']) ?></td>
                <td><?= htmlspecialchars($row['stock']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
