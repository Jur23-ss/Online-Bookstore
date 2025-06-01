<?php
include 'includes/db_connect.php';

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']);
    $sql = "SELECT * FROM books WHERE id = $book_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        echo "Book not found.";
        exit;
    }
} else {
    echo "No book selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?> - Details</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            background-color: #f9f5ec;
            font-family: 'Poppins', sans-serif;
            color: #2e4d2e;
        }

        .book-container {
            max-width: 1000px;
            margin: 100px auto;
            background: #fffdf6;
            display: flex;
            gap: 2rem;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }

        .book-cover {
            flex: 1;
        }

        .book-cover img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        .book-info {
            flex: 1.2;
        }

        .book-info h1 {
            font-size: 2rem;
            color: #4a7c59;
            margin-bottom: 0.5rem;
        }

        .book-info p {
            margin: 0.3rem 0;
        }

        .book-info p span {
            font-weight: bold;
            color: #2e4d2e;
        }

        .description {
            margin-top: 1rem;
            font-style: italic;
            line-height: 1.6;
        }

        .actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
        }

        .btn-green {
            background: #4a7c59;
        }

        .btn-green:hover {
            background: #3b644a;
        }

        .btn-dark {
            background: #292929;
        }

        .btn-dark:hover {
            background: #1e1e1e;
        }
    </style>
</head>
<body>
    <div class="book-container">
        <div class="book-cover">
            <img src="images/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
        </div>
        <div class="book-info">
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            <p><span>Author:</span> <?= htmlspecialchars($book['author']) ?></p>
            <p><span>Genre:</span> <?= htmlspecialchars($book['book_genre']) ?></p>
            <p><span>Price:</span> 💰 $<?= number_format($book['price'], 2) ?></p>
            <p><span>Stock:</span> 📦 <?= (int)$book['stock'] ?></p>
            <div class="description">
                <?= nl2br(htmlspecialchars($book['description'])) ?>
            </div>

            <div class="actions">
                <?php if ($book['stock'] > 0): ?>
                    <a href="add_to_cart.php?id=<?= $book['id'] ?>" class="btn btn-green">🛒 Add to Cart</a>
                <?php else: ?>
                    <span style="color: red; font-weight: bold;">Out of Stock</span>
                <?php endif; ?>
                <a href="index.php" class="btn btn-dark">⬅ Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>