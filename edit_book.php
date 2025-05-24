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
    $coverImage = $_POST['cover_image'];
    $desc = $_POST['description'];
    $section = $_POST['section'];

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, book_genre=?, cover_image=?, description=?, section=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $author, $book_genre, $coverImage, $desc, $section, $id);
    $stmt->execute();
    header("Location: manage_books.php");
    exit;
}

// Load book
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
            background: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #1c1c1c;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px #ff2c1f44;
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
            border: none;
            border-radius: 5px;
            background: #2a2a2a;
            color: white;
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
            background: #0c4090;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Book</h2>
        <div style="margin-bottom: 1rem;">
            <a href="admin.php" class="btn" style="background: #292929;">⬅ Back to Admin Dashboard</a>
        </div>
        <form method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
            <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
            <input type="text" name="book_genre" value="<?= htmlspecialchars($book['book_genre']) ?>" required>
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
