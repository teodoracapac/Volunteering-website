<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Dashboard</title>
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
            bottom: -100px; /* Initial position (adjust for higher slide) */
        }
        .message {
            font-size: 50px;
            color: #2d7a2b;
            margin-top: 20px;
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
    <?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->
    <div class="container">
        <h1 class="welcome-slide">Welcome, Organizer!</h1>
        <p class="message" style="font-size: 30px;">Use this efficient volunteering management website to organize helping the world!</p>
        <img src="picture.png" alt="Organizer Image" class="image-slide">
    </div>
</body>
</html>
