<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_task']) && isset($_POST['completion_date'])) {
        $id_task = $_POST['id_task'];
        $completion_date = $_POST['completion_date'];
        $status = 'Complete';

        // Update the volunteer_tasks table
        $query = "UPDATE volunteer_tasks SET completion_date = ? WHERE id_task = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $completion_date, $id_task);

        if ($stmt->execute()) {
            // Update the tasks table
            $query = "UPDATE tasks SET status = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $status, $id_task);

            if ($stmt->execute()) {
                $message = "Task marked as completed.";
            } else {
                $message = "Error updating task status: " . $stmt->error;
            }
        } else {
            $message = "Error marking task as completed: " . $stmt->error;
        }
    } else {
        $message = "Task ID or completion date not set.";
    }
}

// Fetch the tasks assigned to the logged-in volunteer
$username = $_SESSION['username'];
$query = "SELECT vt.id_task, t.description
          FROM volunteer_tasks vt
          JOIN tasks t ON vt.id_task = t.id
          JOIN volunteers v ON vt.id_volunteer = v.id
          WHERE v.username = ? AND vt.completion_date IS NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Task Completed</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->
    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'completed') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <h1>Mark Task as Completed</h1>
        <form method="post" action="mark_task_completed.php">
            <label for="id_task">Task:</label>
            <select id="id_task" name="id_task" required>
                <?php while ($task = $result->fetch_assoc()) : ?>
                    <option value="<?php echo htmlspecialchars($task['id_task']); ?>">
                        <?php echo htmlspecialchars($task['description']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label for="completion_date">Completion Date:</label>
            <input type="date" id="completion_date" name="completion_date" required>
            <button type="submit">Mark as Completed</button>
        </form>
    </div>
</body>
</html>
