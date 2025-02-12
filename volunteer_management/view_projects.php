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

// Initialize search query to an empty string
$searchQuery = "";

// Check if a search query is specified in the request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Modify the query to include the search functionality
$query = "SELECT title, organizer, date, location FROM projects 
          WHERE title LIKE '%$searchQuery%' 
          OR organizer LIKE '%$searchQuery%' 
          OR location LIKE '%$searchQuery%'
          ORDER BY date $sortOrder";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Projects</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->
    <div class="container">
        <div class="sort-button">
            <form method="get" action="view_projects.php">
                <label for="sort_order">Sort by Date:</label>
                <select id="sort_order" name="sort_order" class="sort-order-select">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
                <button type="submit">Sort</button>
            </form>
        </div>
        
        <div class="search-bar">
            <form method="get" action="view_projects.php">
                <label for="search">Search projects:</label>
                <input type="text" name="search" placeholder="Search projects by name, location, or organizer" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <h2>All Projects</h2>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Organizer</th>
                    <th>Date</th>
                    <th>Location</th>
                </tr>
                <?php while ($project = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><?php echo htmlspecialchars($project['organizer']); ?></td>
                        <td><?php echo htmlspecialchars($project['date']); ?></td>
                        <td><?php echo htmlspecialchars($project['location']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No projects found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>
