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

$query = "SELECT id, id_project, description, type, status, deadline FROM view_tasks ORDER BY deadline $sortOrder";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task_id'])) {
    // Delete task code remains unchanged
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Tasks</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->

    <div class="container">
        <div class="sort-button">
            <form method="get" action="view_tasks.php">
                <label for="sort_order">Sort by Deadline:</label>
                <select id="sort_order" name="sort_order" class="sort-order-select">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
                <button type="submit">Sort</button>
            </form>
        </div>
        
        <h2>All Tasks</h2>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>Project ID</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Action</th>
                </tr>
                <?php while ($task = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['id_project']); ?></td>
                        <td><?php echo htmlspecialchars($task['description']); ?></td>
                        <td><?php echo htmlspecialchars($task['type']); ?></td>
                        <td><?php echo htmlspecialchars($task['status']); ?></td>
                        <td><?php echo htmlspecialchars($task['deadline']); ?></td>
                        <td>
                            <form method="post" action="view_tasks.php" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                <input type="hidden" name="delete_task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No tasks found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
