<?php
session_start();
include 'includes/db_connect.php';
include 'admin_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $headline = $_POST['headline'];
    $subheadline = $_POST['subheadline'];

    $image = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    $uploadPath = "images/" . basename($image);

    if (!is_dir("images")) {
        mkdir("images", 0755, true);
    }

    if (move_uploaded_file($tmpName, $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO sliders (title, image, headline, subheadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $image, $headline, $subheadline);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    } else {
        $error = "Failed to upload image.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Slider</title>
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
        }
        h2 {
            text-align: center;
            color: #4a7c59;
        }
        input, textarea {
            width: 100%;
            padding: 0.8rem;
            margin: 1rem 0;
            background: #2a2a2a;
            border: none;
            border-radius: 6px;
            color: white;
        }
        .btn {
            width: 100%;
            padding: 0.8rem;
            background: #4a7c59;;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover {
            background: #0c4090;
        }
        .error {
            color: #ff4d4d;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Slider</h2>
    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Slider Title" required>
        <input type="file" name="image" accept="image/*" required>
        <input type="text" name="headline" placeholder="Headline Text" required>
        <textarea name="subheadline" placeholder="Subheadline Text" required></textarea>
        <button type="submit" class="btn">Add Slider</button>
    </form>
</div>

</body>
</html>
