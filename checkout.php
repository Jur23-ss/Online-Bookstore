<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$books = [];

// Load cart items
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

// Handle order submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($books) > 0) {
    $stmt = $conn->prepare("INSERT INTO orders (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id) VALUES (?, ?)");
    foreach ($books as $book) {
        $stmt->bind_param("ii", $order_id, $book['id']);
        $stmt->execute();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $success = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #0b0b0b;
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 2rem;
        }
        .checkout-container {
            max-width: 800px;
            margin: auto;
            background: #1c1c1c;
            padding: 2rem;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            color: #4a7c59;
            margin-bottom: 2rem;
        }
        .checkout-item {
            display: flex;
            gap: 1rem;
            background: #111;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            align-items: center;
        }
        .checkout-item img {
            width: 100px;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
        }
        .checkout-item h3 {
            margin: 0 0 0.3rem;
            color: #4a7c59;
        }
        .checkout-item p {
            margin: 0.2rem 0;
            color: #bbb;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 0.9rem;
            margin-top: 2rem;
            background: #4a7c59;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .btn:hover {
            background: #0c4090;
        }
        .success {
            color: #90ee90;
            text-align: center;
            font-size: 1.2rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <?php if ($success): ?>
        <div class="success">✅ Your order has been placed successfully!</div>
        <a class="btn" href="index.php">⬅ Back to Home</a>
    <?php elseif (count($books) > 0): ?>
        <h2>Confirm Your Order</h2>
        <?php foreach ($books as $book): ?>
            <div class="checkout-item">
                <img src="images/<?= htmlspecialchars($book['cover_image'] ?? 'default_cover.jpg') ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                <div>
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p><strong>Genre:</strong> <?= htmlspecialchars($book['book_genre'] ?? 'Unknown') ?></p>
                    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        <form method="POST">
            <button type="submit" class="btn">Confirm Order</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a class="btn" href="index.php">⬅ Back to Home</a>
    <?php endif; ?>
</div>

</body>
</html>
