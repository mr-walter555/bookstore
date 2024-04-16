<?php
session_start();

// Define a variable to track upload status
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve book title, author, genre, and description from the form
    $title = $_POST["title"];
    $author = $_POST["author"];
    $genre = $_POST["genre"];
    $description = $_POST["description"];

    // Establish a database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bookstore";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve user ID from session
    $username = $_SESSION['username']; // Assuming user_id is stored in session
    $userSql = "SELECT * FROM users WHERE username = '$username'";
    $userresult = $conn->query($userSql);
    
    if ($userresult->num_rows > 0) {
        $row = $userresult->fetch_assoc();
        $user_id = $row["id"];

        // Prepare and bind SQL statement
        $stmt = $conn->prepare("INSERT INTO createbook (user_id, title, author, genre, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $author, $genre, $description);

        // Execute SQL statement
        if ($stmt->execute()) {
            $response["status"] = "success";
            $response["message"] = "Book created successfully!";
        } else {
            $response["status"] = "error";
            $response["message"] = "Error creating book: " . $conn->error;
        }

        // Close statement
        $stmt->close();
    } else {
        $response["status"] = "error";
        $response["message"] = "User not found.";
    }

    // Close connection
    $conn->close();
} else {
    $response["status"] = "error";
    $response["message"] = "Form submission failed. Please try again.";
}

// Send response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
