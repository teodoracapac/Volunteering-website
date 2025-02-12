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
$sql = "SELECT fullname, email, phonenumber FROM view_organizers 
        WHERE fullname LIKE '%$searchQuery%' 
        OR email LIKE '%$searchQuery%' 
        OR phonenumber LIKE '%$searchQuery%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Organizers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'nav_bar_volunteers.php'; ?> <!-- Include the navigation bar -->
    
    <div class="container">
        <div class="search-bar">
            <form method="get" action="view_organizers.php">
                <label for="search">Search organziers:</label>
                <input type="text" name="search" placeholder="Search by full name, email or phone number" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <h2>Organizers List</h2>
        <?php
        // Check if there are any organizers
        if ($result->num_rows > 0) {
            // Output data of each row in a table
            echo "<table>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["fullname"]) . "</td>
                        <td>" . htmlspecialchars($row["email"]) . "</td>
                        <td>" . htmlspecialchars($row["phonenumber"]) . "</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No organizers found.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
