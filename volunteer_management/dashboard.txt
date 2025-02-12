<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT * FROM organizers WHERE username='$username'";
$result = mysqli_query($conn, $query);
$is_organizer = mysqli_num_rows($result) > 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if ($is_organizer) : ?>
            <h1>Organizer Dashboard</h1>
            <a href='logout.php' class="logout">Logout</a>
            <div class="dashboard-links">
                <a href='create_project.php'>Create a New Project</a>
                <a href='view_projects.php'>View All Projects</a>
            </div>
        <?php else : ?>
            <h1>Volunteer Dashboard</h1>
            <a href='logout.php' class="logout">Logout</a>
            <div class="dashboard-links">
                <a href='view_available_projects.php'>View Available Projects</a>
                <a href='view_assigned_tasks.php'>View My Tasks</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
