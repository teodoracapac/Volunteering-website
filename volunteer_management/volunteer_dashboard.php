<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'volunteer') {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .welcome-slide {
            animation: slideIn 1s ease forwards;
            opacity: 0;
        }
        .image-slide {
            animation: slideUp 1s ease forwards;
            opacity: 0;
            position: relative; /* Ensure the image's position is relative for animation */
            bottom: -100px; /* Initial position (increased for higher slide) */
        }
        @keyframes slideIn {
            0% { transform: translateY(-50px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        @keyframes slideUp {
            0% { transform: translateY(100px); opacity: 0; } /* Increased for higher slide */
            100% { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <?php include 'nav_bar_volunteers.php'; ?>
    <div class="container">
        <h1 class="welcome-slide">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p class="welcome-slide"style="font-size: 30px; color: green">We're glad you're here to volunteer!</p>
        <p class="message" style="font-size: 30px; color: dark-green">Use this efficient volunteering management website to take part in helping the world!</p>
        <img src="picture.png" alt="Volunteer Image" class="image-slide">
    </div>
</body>
</html>
