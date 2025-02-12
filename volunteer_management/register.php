<?php
session_start(); // Start the session
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $role = $_POST['role'];

    $table = ($role === 'organizer') ? 'organizers' : 'volunteers';

    // Check if the username already exists
    $checkQuery = "SELECT * FROM $table WHERE username = ?";
    $stmt = $conn->prepare($checkQuery);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Username already exists. Please choose a different username.";
    } else {
        // Insert the new user into the database
        $query = "INSERT INTO $table (fullname, username, password, email, phonenumber) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("sssss", $fullname, $username, $password, $email, $phonenumber);

        if ($stmt->execute()) {
            // Set session variables to log the user in
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // Redirect to the appropriate dashboard
            if ($role === 'organizer') {
                header('Location: dashboard_login.php');
            } else {
                header('Location: volunteer_dashboard.php');
            }
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
