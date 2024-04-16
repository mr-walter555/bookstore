<?php
session_start();

// Database configuration
$servername = "localhost"; // Change this to your database server
$username = "root"; // Change this to your database username
$password = ''; // Change this to your database password
$dbname = "bookstore"; // Change this to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variable to control form visibility
$formVisible = true;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to fetch user data
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $row['password'])) {
            // Password is correct, redirect to dashboard after a delay
            $_SESSION['username'] = $username; // Store username in session for future use
            $_SESSION['username'] = $row['username']; // Assuming 'id' is the column name storing user IDs
            echo "<script>
            window.location.href = 'dashboard.php';
        </script>";
        
            exit();
        } else {
            // Password is incorrect
            $error_message = "Invalid username or password.";
        }
    } else {
        // User does not exist
        $error_message = "Invalid username or password.";
    }

    // Hide the form after submission
    $formVisible = false;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>

    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include Sweet Alert library -->

    <!-- <script>
        // Function to display Sweet Alert success message and hide the form
        function showSuccessMessage() {
            // Hide the form using jQuery
            $('#loginForm').hide();
            
            // Display the success message
            Swal.fire({
                title: 'Login Successful!',
                text: 'You have successfully logged in.',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            });
        }
    </script> -->

</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <p>access your dashboard</p>
        <form id="loginForm" action="login.php" method="POST" onsubmit="showSuccessMessage()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </div>


</body>

</html>
