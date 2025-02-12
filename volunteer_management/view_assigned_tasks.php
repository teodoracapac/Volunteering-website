<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

$username = $_SESSION['username'];

$query = "SELECT vt.id, t.id AS task_id, t.description, t.type, t.status, t.deadline
          FROM volunteer_tasks vt
          JOIN tasks t ON vt.id_task = t.id
          JOIN volunteers v ON vt.id_volunteer = v.id
          WHERE v.username = ? AND t.status IN ('Pending', 'Assigned')";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View My Tasks</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'nav_bar_volunteers.php'; ?> <!-- Include the navigation bar -->
    <div class="container">
        <h1>My Tasks</h1>
        <?php if ($result->num_rows > 0) : ?>
            <ul class="task-list">
                <?php while ($task = $result->fetch_assoc()) : ?>
                    <li>
                        <h2><?php echo htmlspecialchars($task['description']); ?></h2>
                        <p>Type: <?php echo htmlspecialchars($task['type']); ?></p>
                        <p>Status: <?php echo htmlspecialchars($task['status']); ?></p>
                        <p>Deadline: <?php echo htmlspecialchars($task['deadline']); ?></p>
                        <?php if ($task['status'] === 'Pending') : ?>
                            <form action="mark_task_completed.php" method="post">
                                <input type="hidden" name="id_task" value="<?php echo htmlspecialchars($task['task_id']); ?>">
                                <label for="completion_date">Completion Date:</label>
                                <input type="date" id="completion_date" name="completion_date" required>
                                <button type="submit">Mark as completed</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>You currently have no tasks, get to work!</p>
        <?php endif; ?>
    </div>
</body>
</html>
