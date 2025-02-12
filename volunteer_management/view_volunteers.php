<?php
session_start();
include('db_connect.php');

// Initialize search query to an empty string
$searchQuery = "";

// Check if a search query is specified in the request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

// Modify the query to include the search functionality
$sql = "SELECT username, fullname, email, phonenumber FROM view_volunteers 
        WHERE username LIKE '%$searchQuery%' 
        OR fullname LIKE '%$searchQuery%' 
        OR email LIKE '%$searchQuery%' 
        OR phonenumber LIKE '%$searchQuery%'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Volunteers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'nav_bar_organizers.php'; ?> <!-- Include the navigation bar -->

<div class="container">
    <div class="search-bar">
        <form method="get" action="view_volunteers.php">
            <label for="search">Search volunteers:</label>
            <input type="text" name="search" placeholder="Search by username, full name, email or phone number" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    
    <h2>All Volunteers</h2>
    <?php if ($result->num_rows > 0) : ?>
        <table>
            <tr>
                <th>Username</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phonenumber']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else : ?>
        <p>No volunteers found.</p>
    <?php endif; ?>
</div>
</body>
</html>
<?php
$conn->close();
?>
