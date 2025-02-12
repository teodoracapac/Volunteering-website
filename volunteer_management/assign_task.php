<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_task = $_POST['id_task'];
    $id_volunteer = $_POST['id_volunteer'];

    $query = "INSERT INTO volunteer_tasks (id_task, id_volunteer) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_task, $id_volunteer);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Task assigned successfully!";
        header('Location: assign_task.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch tasks and volunteers for the form
$tasks = $conn->query("SELECT * FROM tasks");
$volunteers = $conn->query("SELECT * FROM volunteers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Task</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->
     <div class="container scrollable-section">
        <h1>Assign Task to Volunteer</h1>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="message success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>
        <form method="post" action="assign_task.php">
            <label for="id_task">Task:</label>
            <select id="id_task" name="id_task" required>
                <?php while ($task = $tasks->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($task['id']); ?>">
                        <?php echo htmlspecialchars($task['description']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label for="id_volunteer">Volunteer:</label>
            <select id="id_volunteer" name="id_volunteer" required>
                <?php while ($volunteer = $volunteers->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($volunteer['id']); ?>">
                        <?php echo htmlspecialchars($volunteer['fullname']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Assign Task</button>
        </form>
    </div>
</body>
</html>
