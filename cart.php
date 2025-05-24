<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$books = [];

$stmt = $conn->prepare("
    SELECT b.*
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #0b0b0b;
            font-family: 'Poppins', sans-serif;
            color: white;
            padding: 2rem;
        }
        .cart-container {
            max-width: 900px;
            margin: auto;
        }
        .cart-item {
            background: #1c1c1c;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            padding: 1.5rem;
            display: flex;
            gap: 1.5rem;
        }
        .cart-item img {
            width: 120px;
            height: 170px;
            object-fit: cover;
            border-radius: 6px;
        }
        .cart-details {
            flex: 1;
        }
        .cart-details h3 {
            color: #4a7c59;
            margin: 0 0 0.5rem;
        }
        .cart-details p {
            margin: 0.4rem 0;
        }
        .remove-link {
            color: #ff4d4d;
            font-weight: bold;
            text-decoration: none;
        }
        .remove-link:hover {
            text-decoration: underline;
        }
        .buttons {
            text-align: center;
            margin-top: 2rem;
        }
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            margin: 0.5rem;
            text-decoration: none;
            display: inline-block;
        }
        .btn-continue {
            background: #4a7c59;
            color: white;
        }
        .btn-checkout {
            background: #292929;
            color: white;
        }
        .btn-continue:hover {
            background: #0c4090;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h2 style="text-align: center; color: #4a7c59;">Your Cart</h2>

    <?php if (count($books) > 0): ?>
        <?php foreach ($books as $book): ?>
            <div class="cart-item">
                <div>
                    <img src="images/<?= htmlspecialchars($book['cover_image'] ?? 'default_cover.jpg') ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                </div>
                <div class="cart-details">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p><strong>Genre:</strong> <?= htmlspecialchars($book['book_genre'] ?? 'Unknown') ?></p>
                    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <a class="remove-link" href="remove_from_cart.php?id=<?= $book['id'] ?>">Remove</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="buttons">
            <a class="btn btn-continue" href="index.php">⬅ Continue Shopping</a>
            <a class="btn btn-checkout" href="checkout.php">Proceed to Checkout</a>
        </div>
    <?php else: ?>
        <p style="text-align: center;">Your cart is empty.</p>
        <div class="buttons">
            <a class="btn btn-continue" href="index.php">⬅ Back to Home</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
