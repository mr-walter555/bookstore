<?php
session_start();

// Check if the user clicked on the logout link
if (isset($_GET['logout'])) {
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit(); // Ensure script execution stops after redirecting
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Default value for $username
$username = "Guest";

$servername = "localhost";
$username_db = "root"; // Changed variable name to avoid conflict
$password = "";
$database = "bookstore";

$conn = new mysqli($servername, $username_db, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute query to retrieve username
$user_id = $_SESSION['username']; // Assuming user_id is stored in session
$sql = "SELECT username FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id); // Changed "i" to "s" for string
$stmt->execute();

// Check if query executed successfully
if ($result = $stmt->get_result()) {
    // Fetch the username
    if ($row = $result->fetch_assoc()) {
        $username = $row['username'];
    } else {
        echo "No username found in database for user ID: $user_id"; // Debug statement
    }
    $result->free();
} else {
    echo "Error executing SQL query: " . $conn->error; // Debug statement
}

$stmt->close();
$conn->close();
?>
<?php
// Establish a database connection
$servername = "localhost";
$username_db = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "bookstore"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username_db, $password, $dbname);

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

    // Fetch count of books uploaded
    $sql = "SELECT COUNT(*) AS book_count FROM uploadbooks WHERE user_id = $user_id";
    $result = $conn->query($sql);
}
// Initialize book count variable
$book_count = 0;


// Check if there are any rows returned
if ($result->num_rows > 0) {
    // Fetch the book count
    $row = $result->fetch_assoc();
    $book_count = $row['book_count'];
}

// Close connection
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard with Pages</title>
    <link rel="stylesheet" href="dashboard.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:400,700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Include CKEditor library -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>

</head>

<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <div class="profile dropdown">
            <box-icon type='solid' name='user-circle'></box-icon>
            <span><?= $username ?></span> <!-- Display the username here -->
            <div class="dropdown-toggle" data-toggle="dropdown"></div>
            <div class="dropdown-menu dropdown-menu-right my-dropdown-menu">
                <a class="dropdown-item" href="#">
                    <div class="d-flex align-items-center">
                        <box-icon type='solid' name='user'></box-icon>
                        <span class="ml-2">Profile</span>
                    </div>
                </a>
                <a class="dropdown-item" href="?logout">
                    <div class="d-flex align-items-center">
                        <box-icon type='' name='log-out'></box-icon>
                        <span class="ml-2">Log out</span>
                    </div>
                </a>
            </div>
        </div>
    </div>


    <div class="dashboard-container">
        <div class="sidebar">
            <a href="#dashboard"><box-icon type='solid' name='dashboard'></box-icon> Dashboard</a>
            <a href="#upload"><box-icon name='cloud-upload'></box-icon> Upload Book</a>
            <a href="#my-books"><box-icon name='book-content'></box-icon> My Books</a>
            <a href="#get-creative"><box-icon name='palette'></box-icon> Get Creative</a>
            <a href="#pricing"><box-icon name='dollar'></box-icon> Pricing</a>
            <a href="#settings"><box-icon name='cog'></box-icon> Settings</a>
        </div>

        <div class="content">

            <div id="dashboard" class="page active">
                <h4 style="text-align: justify; font-family: 'Nunito', Arial, sans-serif; text-transform: capitalize; padding: 10px; border-bottom: 3px solid #4CAF50; display: inline-block; width: auto;">Dashboard</h4>

                <!-- Pending Tasks Section -->
                <div class="pending-tasks-section">
                    <div class="pending-tasks-container" id="upload-column">
                        <img src="assets/img/icons/clock-icon.svg">
                        <h2><?php echo $book_count; ?></h2>
                        <h4>Books uploaded</h4>
                    </div>


                    <div class="pending-tasks-container">
                        <img src="assets/img/icons/clock-icon.svg">
                        <h2>0</h2>
                        <h4>Ongoing Tasks</h4>
                    </div>

                    <div class="pending-tasks-container">
                        <img src="assets/img/icons/clock-icon.svg">
                        <h2>0</h2>
                        <h4>Completed Tasks</h4>
                    </div>
                </div>

                <!-- Tabbed Content Section -->
                <div class="tabbed-content-section card mt-2">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-bottom">
                            <li class="nav-item"><a class="nav-link active" href="#bookUploaded" data-toggle="tab">Books uploaded</a></li>
                            <li class="nav-item"><a class="nav-link" href="#ongoingTasks" data-toggle="tab">Ongoing Tasks</a></li>
                            <li class="nav-item"><a class="nav-link" href="#completedTasks" data-toggle="tab">Completed Tasks</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="bookUploaded">
                                <div class="table-responsive" id="bookUploadedListTable">
                                    <?php
                                    // Establish a database connection
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

                                        // Fetch data from the uploadbooks table
                                        $sql = "SELECT * FROM uploadbooks WHERE user_id = '$user_id'";
                                        $result = $conn->query($sql);

                                        // Check if there are any rows returned
                                        if ($result->num_rows > 0) {
                                            // Output table structure
                                            echo "<div class='table-responsive' id='bookUploadedListTable'>";
                                            echo "<table class='table table-striped'>";
                                            echo "<thead>";
                                            echo "<tr>";
                                            echo "<th>Book Title</th>";
                                            echo "<th>Book Author</th>";
                                            echo "<th>Genre</th>"; // Changed column heading to "Genre"
                                            echo "<th>Publisher</th>"; // Add a new column for Publisher
                                            echo "<th>Language</th>"; // Add a new column for Language
                                            echo "<th>Publication Year</th>"; // Add a new column for Publication Year
                                            echo "<th>Upload Date</th>";
                                            echo "</tr>";
                                            echo "</thead>";
                                            echo "<tbody>";

                                            // Output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                // Extract filename from the file path
                                                $file_name = basename($row["file_path"]);

                                                echo "<tr>";
                                                echo "<td>" . $row["book_title"] . "</td>";
                                                echo "<td>" . $row["book_author"] . "</td>";
                                                echo "<td>" . $row["genre"] . "</td>"; // Display genre here
                                                echo "<td>" . $row["publisher"] . "</td>"; // Display publisher here
                                                echo "<td>" . $row["language"] . "</td>"; // Display language here
                                                echo "<td>" . $row["publication_year"] . "</td>"; // Display publication year here
                                                echo "<td>" . $row["upload_date"] . "</td>";
                                                echo "</tr>";
                                            }

                                            echo "</tbody>";
                                            echo "</table>";
                                            echo "</div>";
                                        } else {
                                            // No data found message
                                            echo "<p>No books uploaded yet.</p>";
                                        }
                                    }

                                    // Close connection
                                    $conn->close();
                                    ?>






                                </div>



                            </div>

                            <div class="tab-pane" id="ongoingTasks">
                                <div class="table-responsive" id="ongoingTaskListTable">
                                    <p>No ongoing tasks available.</p>
                                </div>
                            </div>

                            <div class="tab-pane" id="completedTasks">
                                <div class="table-responsive" id="completedTaskListTable">
                                    <p>No completed tasks available.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="upload" class="page">

                <!-- Tab panes -->
                <div>
                    <h4 style="text-align: justify; font-family: 'Nunito', Arial, sans-serif; text-transform: capitalize; padding: 10px; border-bottom: 3px solid #4CAF50; display: inline-block; width: auto;">Upload your book</h4>
                    <div class="tab-content" id="myTabContent" style="width: 900px; margin-top:50px;">
                        <!-- Upload Tab -->
                        <div class="tab-pane fade show active" id="upload-content" role="tabpanel" aria-labelledby="upload-tab">
                            <div class="container mt-4">
                                <form action="upload_process.php" id="uploadForm" method="post">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="book_title">Title:</label>
                                                <input type="text" class="form-control" id="book_title" name="book_title" placeholder="Enter the title of your book">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="book_author">Author:</label>
                                                <input type="text" class="form-control" id="book_author" name="book_author" placeholder="Enter the author's name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="genre">Genre:</label>
                                                <select class="form-control" id="genre" name="genre">
                                                    <option value="" selected disabled>-- Select a genre --</option>
                                                    <option value="fiction">Fiction</option>
                                                    <option value="non-fiction">Non-Fiction</option>
                                                    <option value="fantasy">Fantasy</option>
                                                    <option value="mystery">Mystery</option>
                                                    <option value="romance">Romance</option>
                                                    <!-- Add more genre options as needed -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="publisher">Publisher:</label>
                                                <input type="text" class="form-control" id="publisher" name="publisher" placeholder="Enter the publisher's name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="language">Language:</label>
                                                <select class="form-control" id="language" name="language">
                                                    <option value="" selected disabled>-- Select the language --</option>
                                                    <option value="English">English</option>
                                                    <option value="Spanish">Spanish</option>
                                                    <option value="French">French</option>
                                                    <option value="German">German</option>
                                                    <!-- Add more languages as needed -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="publication_year">Publication Year:</label>
                                                <select class="form-control" id="publication_year" name="publication_year">
                                                    <option value="" selected disabled>-- Select the publication year --</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2019">2019</option>
                                                    <!-- Add more years as needed -->
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="book_file">Choose a file:</label>
                                                <input type="file" class="form-control-file" id="book_file" name="book_file" placeholder="Select a file">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success" name="submit">Upload</button>
                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>
                        <!-- Drag & Drop Tab -->

                    </div>
                </div>
            </div>
            <div id="my-books" class="page">
                <h2 style="text-align: justify; font-family: 'Nunito', Arial, sans-serif; text-transform: capitalize; padding: 10px; border-bottom: 3px solid #4CAF50; display: inline-block; width: auto;">My Books</h2>
                <div class="book-grid">
                    <?php
                    // Establish a database connection
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

                    // Fetch data from the uploadbooks table
                    $username = $_SESSION['username']; // Assuming user_id is stored in session
                    $userSql = "SELECT * FROM users WHERE username = '$username'";
                    $userresult = $conn->query($userSql);
                    if ($userresult->num_rows > 0) {
                        $row = $userresult->fetch_assoc();
                        $user_id = $row["id"];
                        $sql = "SELECT * FROM uploadbooks WHERE user_id = '$user_id'";
                        $result = $conn->query($sql);
                    }

                    // Check if there are any rows returned
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            // Extract filename from the file path
                            $file_name = basename($row["file_path"]);

                            // Generate Google Docs Viewer URL for the uploaded document
                            $google_docs_viewer_url = "https://docs.google.com/viewer?url=" . urlencode("https://yourdomain.com/uploads/$file_name") . "&embedded=true";

                            // Display each book as a grid item
                            echo "<div class='book-item'>";
                            echo "<img src='path/to/book/image' alt='Book Cover'>";
                            echo "<h6>" . $row["book_title"] . "</h6>";
                            echo "<p>Author: " . $row["book_author"] . "</p>";
                            echo "<p>Genre: " . $row["genre"] . "</p>"; // Assuming "genre" is a column in your database

                            // Add button to open document in Google Docs Viewer
                            echo "<button class='btn btn-primary' onclick='openGoogleDocsViewer(\"$google_docs_viewer_url\")'>Read</button>";

                            echo "</div>";
                        }
                    } else {
                        // No books uploaded yet
                        echo "<p>No books uploaded yet.</p>";
                    }

                    // Close connection
                    $conn->close();
                    ?>

                </div>

            </div>
            <div id="get-creative" class="page">
                <div style="position: relative;">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal" style="position: absolute; top: 10px; right: 10px;">
                        <box-icon name='edit' color='white' style="margin-right: 5px;"></box-icon>
                    </button>
                    <h2 style="text-align: justify; font-family: 'Nunito', Arial, sans-serif; text-transform: capitalize; padding: 10px; border-bottom: 3px solid #4CAF50; display: inline-block; width: auto;">Start Writing</h2>
                    <!-- Text editor container -->
                    <div id="textEditor" style="display: none;">
                        <!-- Include your text editor here -->
                        <div id="editor"  contenteditable="true">
                            <!-- Your text editor content goes here -->
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bootstrap Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create New Book</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="bookForm" action="createbook.php" method="post">
                                <div class="form-group">
                                    <label for="title">Title:</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter the title of your book" required>
                                </div>
                                <div class="form-group">
                                    <label for="author">Author:</label>
                                    <input type="text" class="form-control" id="author" name="author" placeholder="Enter the author's name" required>
                                </div>
                                <div class="form-group">
                                    <label for="genre">Genre:</label>
                                    <select class="form-control" id="genre" name="genre" required>
                                        <option value="" selected disabled>-- Select a genre --</option>
                                        <option value="fiction">Fiction</option>
                                        <option value="non-fiction">Non-Fiction</option>
                                        <option value="fantasy">Fantasy</option>
                                        <option value="mystery">Mystery</option>
                                        <option value="romance">Romance</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter a brief description of your book" rows="3" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success" form="bookForm">Create</button>
                        </div>
                    </div>
                </div>
            </div>





            <div id="pricing" class="page">
                <h2 style="text-align: justify; font-family: 'Nunito', Arial, sans-serif; text-transform: capitalize; padding: 10px; border-bottom: 3px solid #4CAF50; display: inline-block; width: auto;">Pricing Content</h2>
                <p>This is the content of the Pricing page.</p>
            </div>
            <div id="settings" class="page">
                <h2 style="text-align: justify; font-family: 'Nunito', Arial, sans-serif; text-transform: capitalize; padding: 10px; border-bottom: 3px solid #4CAF50; display: inline-block; width: auto;">Settings Content</h2>
                <p>This is the content of the Settings page.</p>
            </div>
        </div>







    </div>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'))
            .then(editor => {
                console.log(editor);
            })
            .catch(error => {
                console.error(error);
            });
    </script>



    <script>
        const sidebarLinks = document.querySelectorAll('.sidebar a');
        const pages = document.querySelectorAll('.page');
        const userId = <?php echo json_encode($_SESSION['username']); ?>; // Get the user ID from PHP session

        function setActivePage(pageId) {
            pages.forEach(page => {
                if (page.id === pageId) {
                    page.classList.add('active');
                } else {
                    page.classList.remove('active');
                }
            });
            // Store the last visited page in session storage with the user ID as part of the key
            sessionStorage.setItem('lastVisitedPage_' + userId, pageId);
        }

        const lastVisitedPage = sessionStorage.getItem('lastVisitedPage_' + userId);
        if (lastVisitedPage) {
            setActivePage(lastVisitedPage);
        } else {
            setActivePage('dashboard');
        }

        sidebarLinks.forEach(link => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                const targetPageId = link.getAttribute('href').substring(1);
                setActivePage(targetPageId);
            });
        });
    </script>


    <script>
        // JavaScript to handle dropdown toggle
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const dropdownMenu = document.querySelector('.dropdown-menu');

        dropdownToggle.addEventListener('click', () => {
            dropdownMenu.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', (event) => {
            if (!dropdownMenu.contains(event.target) && !dropdownToggle.contains(event.target)) {
                dropdownMenu.classList.remove('active');
            }
        });
    </script>
    <script>
        var timeout; // Variable to store the timeout ID

        function resetTimer() {
            clearTimeout(timeout); // Clear previous timeout
            timeout = setTimeout(logout, 300000); // Set timeout for 5 minutes (300000 milliseconds)
        }

        // Add event listeners for user activity
        window.addEventListener('mousemove', resetTimer);
        window.addEventListener('keydown', resetTimer);
        window.addEventListener('click', resetTimer);

        function logout() {
            window.location.href = 'logout.php'; // Redirect to logout page
        }

        // Start the timer when the page loads
        resetTimer();
    </script>

    <script>
        // Intercept form submission
        document.getElementById('uploadForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent default form submission

            try {
                // Serialize form data
                const formData = new FormData(this);

                // Send POST request to upload_process.php
                const response = await fetch('upload_process.php', {
                    method: 'POST',
                    body: formData
                });

                // Check if request was successful
                if (!response.ok) {
                    throw new Error('Upload failed. Please try again.'); // Throw error if request failed
                }

                // Parse JSON response
                const responseData = await response.json();

                // Display appropriate notification based on response
                if (responseData.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Successful',
                        text: responseData.message // Display success message returned from server
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: responseData.message // Display error message returned from server
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message // Display any caught error message
                });
            }
        });
    </script>
   <script>
    // Intercept form submission
    document.getElementById('bookForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevent default form submission

        try {
            // Serialize form data
            const formData = new FormData(this);

            // Send POST request to createbook.php
            const response = await fetch('createbook.php', {
                method: 'POST',
                body: formData
            });

            // Check if request was successful
            if (!response.ok) {
                throw new Error('Upload failed. Please try again.'); // Throw error if request failed
            }

            // Parse JSON response
            const responseData = await response.json();

            // Hide the form and modal overlay
            $('#exampleModal').modal('hide');
            $('.modal-backdrop').remove();

            // Display appropriate notification based on response
            if (responseData.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Book Created Successfully',
                    text: responseData.message, // Display success message returned from server
                    showCancelButton: false, // Show the Ok button
                    // cancelButtonText: 'Ok', // Customize the Ok button text
                    onClose: function() {
                        // Show the text editor container
                        document.getElementById('textEditor').style.display = 'block';
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Ok button clicked, show the text editor container
                        document.getElementById('textEditor').style.display = 'block';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Book Creation Failed',
                    text: responseData.message // Display error message returned from server
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message // Display any caught error message
            });
        }
    });
</script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include PDF.js library -->
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>