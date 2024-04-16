<?php
// Database configuration
$servername = "localhost"; // Change this to your database server
$username = "root"; // Change this to your database username
$password = ''; // Change this to your database password
$dbname = "bookstore"; // Change this to your database name

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert data into the table
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        // Close connection
        $conn->close();

        // Redirect to login page after a delay
        // echo "<script>
        //         setTimeout(function() {
        //             window.location.href = 'login.php';
        //         }, 1000); // 2000 milliseconds (2 seconds) delay
        //       </script>";
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>

    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include Sweet Alert library -->

    <script>
        // Function to display Sweet Alert success message
        function showSuccessMessage() {
            Swal.fire({
                title: 'Registration Successful!',
                text: 'You have successfully registered.',
                icon: 'success',
                showConfirmButton: true,
                timer: 1500

            });
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Create Your Account</h1>
        <form action="registration.php" method="POST" onsubmit="showSuccessMessage()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>
