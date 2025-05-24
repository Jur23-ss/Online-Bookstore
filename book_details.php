<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_GET['id'])) {
    echo "No book selected.";
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    echo "Book not found.";
    exit;
}

$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> - Book Details</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .details-container {
            max-width: 900px;
            margin: auto;
            display: flex;
            flex-wrap: wrap;
            background: #1c1c1c;
            border-radius: 12px;
            padding: 2rem;
            gap: 2rem;
            align-items: flex-start;
        }
        .details-container img {
            max-width: 300px;
            border-radius: 10px;
            object-fit: cover;
        }
        .book-info {
            flex: 1;
        }
        .book-info h1 {
            color: #4a7c59;
            margin-bottom: 0.5rem;
        }
        .book-info p {
            margin: 0.4rem 0;
        }
        .buttons {
            margin-top: 2rem;
        }
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            margin-right: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cart {
            background: #4a7c59;
            color: white;
        }
        .btn-home {
            background: #333;
            color: white;
        }
        .btn-cart:hover {
            background: #0c4090;
        }
        .btn-home:hover {
            background: #555;
        }
    </style>
</head>
<body>

<div class="details-container">
    <div>
        <img src="images/<?= htmlspecialchars($book['cover_image'] ?? 'default_cover.jpg') ?>" alt="<?= htmlspecialchars($book['title']) ?>">
    </div>
    <div class="book-info">
        <h1><?= htmlspecialchars($book['title']) ?></h1>
        <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><strong>Genre:</strong> <?= htmlspecialchars($book['book_genre'] ?? 'Not specified') ?></p>
        <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>

        <div class="buttons">
            <a class="btn btn-cart" href="add_to_cart.php?id=<?= $book['id'] ?>">🛒 Add to Cart</a>
            <a class="btn btn-home" href="index.php">⬅ Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>
