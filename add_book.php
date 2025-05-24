<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $book_genre = $_POST['book_genre'];
    $desc = $_POST['description'];
    $section = $_POST['section'];

    $coverImage = $_FILES['cover_image']['name'];
    $tmpName = $_FILES['cover_image']['tmp_name'];
    $uploadPath = "images/" . basename($coverImage);

    if (!is_dir("images")) {
        mkdir("images", 0755, true);
    }

    if (move_uploaded_file($tmpName, $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, book_genre, cover_image, description, section) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $title, $author, $book_genre, $coverImage, $desc, $section);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    } else {
        $uploadError = "Failed to upload image. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
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
            color:  #4a7c59;
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
            background:  #4a7c59;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background: #0c4090;
        }
        .error {
            color:#5f9270;

            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Book</h2>
        <div style="margin-bottom: 1rem;">
            <a href="admin.php" class="btn" style="background: #292929;">⬅ Back to Admin Dashboard</a>
        </div>
        <?php if (!empty($uploadError)): ?>
            <div class="error"><?= $uploadError ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="book_genre" placeholder="Genre (e.g. Fiction, Biography)" required>
            <input type="file" name="cover_image" accept="image/*" required>
            <select name="section" required>
                <option value="featured">Featured Books</option>
                <option value="coming">Coming Soon</option>
            </select>
            <textarea name="description" placeholder="Book Description" required></textarea>
            <button class="btn" type="submit">Add Book</button>
        </form>
    </div>
</body>
</html>
