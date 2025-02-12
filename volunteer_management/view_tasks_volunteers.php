<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit();
}

// Initialize sort order to ascending by default
$sortOrder = 'ASC';

// Check if a sort order is specified in the request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sort_order'])) {
    // Validate and sanitize the sort order value
    $sortOrder = ($_GET['sort_order'] === 'desc') ? 'DESC' : 'ASC';
}

$query = "SELECT id, id_project, description, type, status, deadline FROM tasks WHERE status = 'Pending' ORDER BY deadline $sortOrder";
$result = $conn->query($query);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']);
    $username = $_SESSION['username'];

    // Find the volunteer ID
    $volunteer_query = "SELECT id FROM volunteers WHERE username = ?";
    $stmt = $conn->prepare($volunteer_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $volunteer_result = $stmt->get_result();
    if ($volunteer_result->num_rows > 0) {
        $volunteer = $volunteer_result->fetch_assoc();
        $volunteer_id = $volunteer['id'];

        // Assign the task to the volunteer
        $assign_query = "INSERT INTO volunteer_tasks (id_volunteer, id_task) VALUES (?, ?)";
        $stmt = $conn->prepare($assign_query);
        $stmt->bind_param("ii", $volunteer_id, $task_id);
        if ($stmt->execute()) {
            // Update the task status to 'Assigned'
            $update_task_query = "UPDATE tasks SET status = 'Assigned' WHERE id = ?";
            $stmt = $conn->prepare($update_task_query);
            $stmt->bind_param("i", $task_id);
            if ($stmt->execute()) {
                $message = "Task successfully assigned.";
            } else {
                $message = "Failed to update task status.";
            }
        } else {
            $message = "Failed to assign task.";
        }
    } else {
        $message = "Volunteer not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Tasks</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showConfirmation() {
            const radios = document.querySelectorAll('input[name="task_id"]');
            const confirmDiv = document.getElementById('confirm-assignment');
            let isChecked = false;

            radios.forEach(radio => {
                if (radio.checked) {
                    isChecked = true;
                }
            });

            confirmDiv.style.display = isChecked ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <?php include 'nav_bar_volunteers.php'; ?> <!-- Include the navigation bar -->

    <div class="container">
        <div class="sort-button">
            <form method="get" action="view_tasks_volunteers.php">
                <label for="sort_order">Sort by Deadline:</label>
                <select id="sort_order" name="sort_order" class="sort-order-select">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
                <button type="submit">Sort</button>
            </form>
        </div>
        
        <h2>All Tasks</h2>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($result->num_rows > 0) : ?>
            <form method="post" action="view_tasks_volunteers.php">
                <table>
                    <tr>
                        <th>Assign</th>
                        <th>Project ID</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Deadline</th>
                    </tr>
                    <?php while ($task = $result->fetch_assoc()) : ?>
                        <tr>
                            <td>
                                <input type="radio" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>" onclick="showConfirmation()">
                            </td>
                            <td><?php echo htmlspecialchars($task['id_project']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td><?php echo htmlspecialchars($task['type']); ?></td>
                            <td><?php echo htmlspecialchars($task['status']); ?></td>
                            <td><?php echo htmlspecialchars($task['deadline']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <div id="confirm-assignment" style="display: none;">
                    <p>Do you want to pick up this task?</p>
                    <button type="submit" name="confirm" value="yes">Yes</button>
                    <button type="button" class="no-button" onclick="document.querySelectorAll('input[name=\'task_id\']:checked')[0].checked = false; showConfirmation();">No</button>
                </div>
            </form>
        <?php else : ?>
            <p>No tasks found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
