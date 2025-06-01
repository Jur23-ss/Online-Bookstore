<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

if (!isset($_GET['id'])) {
    header("Location: manage_books.php");
    exit;
}

$id = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $book_genre = $_POST['book_genre'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $coverImage = $_POST['cover_image'];
    $desc = $_POST['description'];
    $section = $_POST['section'];

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, book_genre=?, price=?, stock=?, cover_image=?, description=?, section=? WHERE id=?");
    $stmt->bind_param("sssdisssi", $title, $author, $book_genre, $price, $stock, $coverImage, $desc, $section, $id);
    $stmt->execute();
    header("Location: manage_books.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #f9f5ec;
            color: #2e4d2e;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fffdf6;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px #ddd8c4;
        }
        h2 {
            color: #4a7c59;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            margin: 0.6rem 0 1.2rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f0e9d8;
            color: #2e4d2e;
        }
        textarea {
            min-height: 100px;
        }
        .btn {
            width: 100%;
            padding: 0.8rem;
            background: #4a7c59;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background: #3e684a;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Book</h2>
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
            <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
            <input type="text" name="book_genre" value="<?= htmlspecialchars($book['book_genre']) ?>" required>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($book['price']) ?>" required>
            <input type="number" name="stock" value="<?= htmlspecialchars($book['stock']) ?>" required>
            <input type="text" name="cover_image" value="<?= htmlspecialchars($book['cover_image']) ?>" required>
            <select name="section" required>
                <option value="featured" <?= $book['section'] === 'featured' ? 'selected' : '' ?>>Featured Books</option>
                <option value="coming" <?= $book['section'] === 'coming' ? 'selected' : '' ?>>Coming Soon</option>
            </select>
            <textarea name="description" required><?= htmlspecialchars($book['description']) ?></textarea>
            <button class="btn" type="submit">Update Book</button>
        </form>
    </div>
</body>
</html>