<?php
session_start();

// Define a variable to track upload status
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Define the target directory for uploads
    $target_dir = "uploads/";

    // Retrieve book title, author, genre, publisher, language, and publication year from the form
    $book_title = $_POST["book_title"];
    $book_author = $_POST["book_author"];
    $genre = $_POST["genre"]; // Assuming the genre is submitted via the form
    $publisher = $_POST["publisher"]; // Assuming the publisher is submitted via the form
    $language = $_POST["language"]; // Assuming the language is submitted via the form
    $publication_year = $_POST["publication_year"]; // Assuming the publication year is submitted via the form

    // Define the target file path
    $target_file = $target_dir . basename($_FILES["book_file"]["name"]);

    // Set uploadOk flag to 1
    $uploadOk = 1;

    // Get the file extension
    $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        $response["status"] = "error";
        $response["message"] = "Sorry, file already exists.";
        $uploadOk = 0;
    }
    
    // Check file size (5MB limit)
    if ($_FILES["book_file"]["size"] > 5000000) {
        $response["status"] = "error";
        $response["message"] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    // Allow only certain file formats (pdf, doc, docx)
    if ($file_extension != "pdf" && $file_extension != "doc" && $file_extension != "docx") {
        $response["status"] = "error";
        $response["message"] = "Sorry, only PDF, DOC, and DOCX files are allowed.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // No need to set $response["status"] here, it will remain empty indicating an error
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES["book_file"]["tmp_name"], $target_file)) {
            // Insert book details into the database
            $servername = "localhost";
            $username = "root"; // Replace with your MySQL username
            $password = ""; // Replace with your MySQL password
            $dbname = "bookstore"; // Replace with your database name

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $username = $_SESSION['username']; // Assuming user_id is stored in session
            $userSql = "SELECT * FROM users WHERE username = '$username'";
            $userresult = $conn->query($userSql);
            if ($userresult->num_rows > 0) {
                $row = $userresult->fetch_assoc();
                $user_id = $row["id"];
                // Prepare SQL statement to insert book details
                $sql = "INSERT INTO uploadbooks (book_title, book_author, publisher, language, publication_year, file_path, user_id, genre) 
                VALUES ('$book_title', '$book_author', '$publisher', '$language', '$publication_year', '$target_file', '$user_id', '$genre')";
        
                if ($conn->query($sql) === TRUE) {
                    // If book details are inserted successfully, set response status to success
                    $response["status"] = "success";
                } else {
                    // If there's an error, set response status to error and include the error message
                    $response["status"] = "error";
                    $response["message"] = "Error inserting book details into the database: " . $conn->error;
                }
            }
            // Close connection
            $conn->close();
        } else {
            $response["status"] = "error";
            $response["message"] = "Sorry, there was an error uploading your file.";
        }
    }
} else {
    $response["status"] = "error";
    $response["message"] = "Form submission failed. Please try again.";
}

// Send response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
