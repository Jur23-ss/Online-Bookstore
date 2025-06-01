
<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$books = [];

// Load cart items with quantity
$stmt = $conn->prepare("
    SELECT b.*, c.quantity
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
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $total_amount = 0;
    foreach ($books as $book) {
        $total_amount += $book['price'] * $book['quantity'];
    }
    $stmt->bind_param("id", $user_id, $total_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");

   $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
if (!$stmt) {
    die("❌ Prepare failed (orders insert): " . $conn->error);
}
$stmt->bind_param("id", $user_id, $total_amount);

    foreach ($books as $book) {
        $stmt_items->bind_param("iiid", $order_id, $book['id'], $book['quantity'], $book['price']);
        $stmt_items->execute();

        $stmt_stock->bind_param("ii", $book['quantity'], $book['id']);
        $stmt_stock->execute();
    }

    // Clear cart
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
        .total {
            text-align: right;
            color: #eee;
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 1rem;
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
        <?php 
            $total = 0;
            foreach ($books as $book): 
                $subtotal = $book['price'] * $book['quantity'];
                $total += $subtotal;
        ?>
            <div class="checkout-item">
                <img src="images/<?= htmlspecialchars($book['cover_image'] ?? 'default_cover.jpg') ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                <div>
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p><strong>Genre:</strong> <?= htmlspecialchars($book['book_genre'] ?? 'Unknown') ?></p>
                    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p><strong>Price:</strong> $<?= number_format($book['price'], 2) ?> × <?= $book['quantity'] ?> = $<?= number_format($subtotal, 2) ?></p>
                    <p><strong>In Stock:</strong> <?= $book['stock'] ?> left</p>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="total">Total: $<?= number_format($total, 2) ?></div>
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
