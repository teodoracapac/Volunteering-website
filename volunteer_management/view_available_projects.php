<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$query = "SELECT * FROM projects";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Available Projects</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Available Projects</h1>
        <ul class="project-list">
            <?php while ($project = $result->fetch_assoc()) : ?>
                <li>
                    <h2><?php echo htmlspecialchars($project['title']); ?></h2>
                    <p>Date: <?php echo htmlspecialchars($project['date']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($project['location']); ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>
