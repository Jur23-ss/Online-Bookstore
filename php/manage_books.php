<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

// Build dynamic query
$query = "SELECT * FROM books WHERE 1=1";

// Search
if (!empty($_GET['search'])) {
    $search = "%" . $conn->real_escape_string($_GET['search']) . "%";
    $query .= " AND (title LIKE '$search' OR author LIKE '$search')";
}

// Sort
if (!empty($_GET['sort']) && $_GET['sort'] === 'oldest') {
    $query .= " ORDER BY id ASC";
} else {
    $query .= " ORDER BY id DESC";
}

$books = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .manage-container {
            max-width: 1000px;
            margin: auto;
        }
        h2 {
            color: #ff2c1f;
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .filter-bar input,
        .filter-bar select,
        .filter-bar button {
            padding: 0.6rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
        }
        .filter-bar input {
            flex: 1;
            background: #2a2a2a;
            color: white;
        }
        .filter-bar select,
        .filter-bar button {
            background: #292929;
            color: white;
            cursor: pointer;
        }
        .filter-bar button {
            background: #ff2c1f;
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
            color: white;
        }
        td a {
            color: #00ccff;
            margin-right: 0.5rem;
        }
        td a.delete {
            color: #ff4d4d;
        }
    </style>
</head>
<body>

<div class="manage-container">
    <h2>Manage Books</h2>
<div style="margin-bottom: 1rem;">
    <a href="admin.php" class="btn" style="background: #292929; color: white; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: bold;">⬅ Back to Admin Dashboard</a>
</div>


    <form class="filter-bar" method="GET" action="manage_books.php">
        <input type="text" name="search" placeholder="Search by title or author" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <select name="sort">
            <option value="newest" <?= (($_GET['sort'] ?? '') === 'newest' ? 'selected' : '') ?>>Newest</option>
            <option value="oldest" <?= (($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '') ?>>Oldest</option>
        </select>
        <button type="submit">Apply</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($book = $books->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($book['id']) ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= htmlspecialchars($book['author']) ?></td>
                <td><?= htmlspecialchars($book['book_genre'] ?? 'Unknown') ?></td>
                <td>
                    <a href="edit_book.php?id=<?= $book['id'] ?>">Edit</a>
                    <a class="delete" href="delete_book.php?id=<?= $book['id'] ?>" onclick="return confirm('Delete this book?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
