<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harayo - Lost and Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/harayo/assets/css/style.css">
    <script src="/harayo/assets/js/script.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/harayo/" class="brand">HARAYO</a>
            <div class="nav-links">
                <a href="/harayo/">Home</a>
                <a href="/harayo/search.php">Search</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/harayo/post_item.php">Report Item</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="/harayo/admin/">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="/harayo/dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <a href="/harayo/logout.php" class="btn btn-outline">Logout</a>
                <?php else: ?>
                    <a href="/harayo/login.php" class="btn btn-primary">Sign In</a>
                    <a href="/harayo/register.php" class="btn btn-outline">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="main-content">
