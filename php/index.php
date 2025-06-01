<?php
session_start();
include 'includes/db_connect.php';

$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($cartCount);
    $stmt->fetch();
    $stmt->close();
}

$sliders = $conn->query("SELECT * FROM sliders ORDER BY id ASC");
$featuredBooks = $conn->query("SELECT * FROM books WHERE section = 'featured' ORDER BY id DESC LIMIT 10");
$comingSoon = $conn->query("SELECT * FROM books WHERE section = 'coming' ORDER BY id DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookstore Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
</head>
<body>

<header class="header">
    <a href="#" class="logo"><i class="ri-book-line"></i> Books</a>
    <ul class="navbar">
        <li><a href="#home">Home</a></li>
        <li><a href="#books">Featured</a></li>
        <li><a href="#coming">Coming</a></li>
        <li><a href="cart.php">🛒 View Cart (<?= $cartCount ?>)</a></li>
        <?php if (isset($_SESSION['user_id'])):
            $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($is_admin);
            $stmt->fetch();
            $stmt->close();
            if ($is_admin): ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn">Sign in</a>
    <?php endif; ?>
</header>

<!-- Featured Books -->
<section class="books-section" id="books">
    <h2 class="heading">Featured Books</h2>
    <div class="books-container">
        <?php while ($row = $featuredBooks->fetch_assoc()): ?>
            <a href="book_details.php?id=<?= $row['id'] ?>" class="box">
                <div class="box-img">
                    <img src="images/<?= htmlspecialchars($row['cover_image'] ?? 'default_cover.jpg') ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                </div>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <span><?= htmlspecialchars($row['book_genre'] ?? '') ?> | <?= htmlspecialchars($row['author']) ?></span><br>
                <span>💰 $<?= number_format($row['price'], 2) ?> | 📦 Stock: <?= (int)$row['stock'] ?></span>
            </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- Coming Soon -->
<section class="books-section" id="coming">
    <h2 class="heading">Coming Soon</h2>
    <div class="books-container">
        <?php while ($row = $comingSoon->fetch_assoc()): ?>
            <a href="book_details.php?id=<?= $row['id'] ?>" class="box">
                <div class="box-img">
                    <img src="images/<?= htmlspecialchars($row['cover_image'] ?? 'default_cover.jpg') ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                </div>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <span><?= htmlspecialchars($row['book_genre'] ?? '') ?> | <?= htmlspecialchars($row['author']) ?></span><br>
                <span>💰 $<?= number_format($row['price'], 2) ?> | 📦 Stock: <?= (int)$row['stock'] ?></span>
            </a>
        <?php endwhile; ?>
    </div>
</section>

</body>
</html>