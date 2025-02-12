<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $organizer = $_SESSION['username'];
    $date = $_POST['date'];
    $location = $_POST['location'];

    $query = "INSERT INTO projects (title, organizer, date, location) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $title, $organizer, $date, $location);

    if ($stmt->execute()) {
        $message = "Project created successfully.";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->
    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <h1>Create a New Project</h1>
        <form method="post" action="create_project.php">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter project title" required>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            <input type="text" id="location" name="location" placeholder="Enter project location" required>
            <button type="submit">Create Project</button>
        </form>
    </div>
</body>
</html>
