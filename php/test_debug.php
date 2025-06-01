<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<p>✅ PHP is working</p>";

if (isset($_GET['id'])) {
    echo "<p>✅ Got book ID: " . $_GET['id'] . "</p>";
} else {
    echo "<p>❌ No book ID found</p>";
}
?>

