<?php
session_start();
include('db_connect.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

// Retrieve list of projects for dropdown menu
$query = "SELECT id, title FROM projects";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $project_id = $_POST['project_id']; // Project ID selected from the dropdown
    $description = $_POST['description'];
    $type = $_POST['type'];
    $deadline = $_POST['deadline'];
    $status = 'Pending'; // Assuming new tasks are initially set as 'Pending'

    // Prepare and execute SQL query to insert new task
    $query = "INSERT INTO tasks (id_project, description, type, status, deadline) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $project_id, $description, $type, $status, $deadline);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Task added successfully.";
        header('Location: new_task.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Task</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->

<div class="container">
    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="message success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>
    <h1>Add a New Task</h1>
    <form method="post" action="new_task.php">
        <!-- Dropdown menu for selecting project -->
        <div>
            <label for="project_id">Select Project:</label>
            <select id="project_id" name="project_id" required>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div>
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>

        <div>
            <label for="type">Type:</label>
            <input type="text" id="type" name="type" required>
        </div>
        
        <div>
            <label for="deadline">Deadline:</label>
            <input type="date" id="deadline" name="deadline" required>
        </div>
        
        <button type="submit">Add Task</button>
    </form>
</div>
</body>
</html>
