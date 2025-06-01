<?php
session_start();
include 'includes/db_connect.php';
include 'admin_or_librarian_check.php';

// Handle new restock request from form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id']) && isset($_POST['requested_stock'])) {
    $bookId = (int)$_POST['book_id'];
    $requestedStock = (int)$_POST['requested_stock'];
    $librarianId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO restock_requests (book_id, requested_stock, librarian_id, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())");
    $stmt->bind_param("iii", $bookId, $requestedStock, $librarianId);
    $stmt->execute();
}

$result = $conn->query("SELECT r.id, b.title, u.name AS librarian, r.requested_stock, r.status, r.created_at, b.id AS book_id
                        FROM restock_requests r
                        JOIN books b ON r.book_id = b.id
                        JOIN users u ON r.librarian_id = u.id
                        ORDER BY r.created_at DESC");

$books = $conn->query("SELECT id, title FROM books ORDER BY title ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restock Requests</title>
    <link rel="stylesheet" href="">
    <style>
        body {
            background-color: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .container {
            max-width: 1100px;
            margin: auto;
        }
        h2 {
            color: #4a7c59;
            text-align: left;
            margin-bottom: 1.5rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #1c1c1c;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 2rem;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background: #222;
            color: white;
        }
        .btn {
            display: inline-block;
            background: #4a7c59;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .btn:hover {
            background-color: #3b614d;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
        }
        .form-box {
            background: #1c1c1c;
            padding: 1rem 1.5rem;
            margin-top: 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        select, input[type="number"] {
            padding: 0.6rem;
            border-radius: 5px;
            border: none;
            background: #2a2a2a;
            color: white;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📥 Restock Requests</h2>
    <a href="<?= $role === 'Admin' ? 'admin.php' : 'librarianDashboard.php' ?>" class="btn">⬅ Back to Dashboard</a>

    <div class="form-box">
        <form method="POST">
            <label for="book_id">Select Book:</label>
            <select name="book_id" id="book_id" required>
                <?php while ($book = $books->fetch_assoc()): ?>
                    <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="number" name="requested_stock" placeholder="Quantity" min="1" required>
            <button type="submit" class="btn-sm">Request Restock</button>
        </form>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Book Title</th>
            <th>Librarian</th>
            <th>Requested Qty</th>
            <th>Status</th>
            <th>Requested At</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['librarian']) ?></td>
                <td><?= htmlspecialchars($row['requested_stock']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <a href="request_restock.php?book_id=<?= $row['book_id'] ?>" class="btn btn-sm">Request Again</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
