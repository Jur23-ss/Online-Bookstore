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

$role = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($role);
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
    <style>
        /* Add basic slider styling */
        .slider-container {
            width: 100%;
            max-width: 1000px;
            margin: 2rem auto;
            position: relative;
            overflow: hidden;
        }
        .slider-slide {
            width: 100%;
            height: 300px;
            position: relative;
            margin-bottom: 2rem;
            border-radius: 10px;
            overflow: hidden;
            background: #222;
        }
        .slider-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .slider-caption {
            position: absolute;
            bottom: 15px;
            left: 20px;
            color: white;
            background: rgba(0,0,0,0.6);
            padding: 10px 15px;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<header class="header">
    <a href="#" class="logo"><i class="ri-book-line"></i> Books</a>
    <ul class="navbar">
        <li><a href="#home">Home</a></li>
        <li><a href="#books">Featured</a></li>
        <li><a href="#coming">Coming</a></li>
        <li><a href="cart.php">🛒 View Cart (<?= $cartCount ?>)</a></li>

        <?php if ($role === 'Admin'): ?>
            <li><a href="admin.php">Admin Panel</a></li>
        <?php elseif ($role === 'Librarian'): ?>
            <li><a href="librarianDashboard.php">Librarian Panel</a></li>
        <?php endif; ?>
    </ul>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="btn">Sign in</a>
    <?php endif; ?>
</header>

<?php
$sliders = $conn->query("SELECT id, image, headline, subheadline FROM sliders ORDER BY id ASC");
?>

<!-- Slider Section -->
<div class="slider-container">
    <?php while ($slider = $sliders->fetch_assoc()): ?>
        <div class="slider-slide">
            <img src="images/<?= htmlspecialchars($slider['image']) ?>" alt="<?= htmlspecialchars($slider['headline']) ?>">
            <div class="slider-text">
                <h3><?= htmlspecialchars($slider['headline']) ?></h3>
                <p><?= htmlspecialchars($slider['subheadline']) ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>


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
