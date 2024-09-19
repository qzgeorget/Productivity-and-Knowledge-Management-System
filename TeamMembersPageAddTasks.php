<?php
// Start or resume the session
session_start();

// Get the employee number from the session
$empNo = $_SESSION["empNo"];

// Database connection parameters
$servername = "localhost";
$username = "admin";
$password = "team23";
$dbname = "MakeItAll";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission (POST request)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $taskName = $_POST["taskName"];
    $description = $_POST["description"];
    $manHours = $_POST["manHours"];
    $projectNo = $_POST["projectNo"];
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];
    $confirmation = $_POST["confirmation"];

    // Include the task checker script
    include 'InProgressTasksChecker.php';
    $result = canAddTask($empNo, $conn, $confirmation);

    // Check if the task can be added
    if ($result['allowed']) {
        try {
            $conn->begin_transaction();

            // Insert new task into the Tasks table
            $sqlTask = "INSERT INTO Tasks (TaskName, Description, ManHours, ProjectNo, Deadline, Status, AssignedBy)
                        VALUES (?, ?, ?, ?, ?, ?, ?);";

            $stmtTask = $conn->prepare($sqlTask);
            $stmtTask->bind_param("ssiissi", $taskName, $description, $manHours, $projectNo, $deadline, $status, $empNo);
            $stmtTask->execute();
            $stmtTask->close();

            // Get the inserted task's ID
            $taskNo = $conn->insert_id;

            // Assign the task to the employee in the TaskAssignments table
            $sqlAssignment = "INSERT INTO TaskAssignments (TaskNo, EmployeeNo) VALUES (?, ?);";
            $stmtAssignment = $conn->prepare($sqlAssignment);
            $stmtAssignment->bind_param("ii", $taskNo, $empNo);
            $stmtAssignment->execute();
            $stmtAssignment->close();

            // Commit the transaction
            $conn->commit();

            // Send JSON response for success
            echo json_encode(["success" => true, "message" => "Task added successfully!"]);
        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            $conn->rollback();
            echo json_encode(["success" => false, "message" => "Error adding task. Please try again. " . $e->getMessage()]);
        }
    } else {
        // Send JSON response for failure
        echo json_encode(["success" => false, "message" => $result['message']]);
    }

    // Terminate the script after sending the JSON response
    exit;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Task</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="TeamStyle.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<body>
    <header>
        <nav>
            <p class="navbar-text">
                <img src="mialogo.png" alt="Logo Description" width="300" height="80">
            </p>
            <div class="forum-buttons pull-right">
                <a href="mainforum.php" class="forum-button">Wiki</a>
                <a href="profile.php" class="forum-button">My Profile</a>
                <a href="index.php" class="forum-button">Sign Out</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="well well-lg">
            <h2>Add New Task</h2>

            <!-- Task input form -->
            <form id="taskForm">
                <div class="form-group">
                    <label for="taskName">Task Name:</label>
                    <input type="text" class="form-control" name="taskName" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="manHours">Man Hours:</label>
                    <input type="number" class="form-control" name="manHours" required>
                </div>

                <div class="form-group">
                    <label for="projectNo">Project Code:</label>
                    <input type="text" class="form-control" name="projectNo" required>
                </div>

                <div class="form-group">
                    <label for="deadline">Deadline:</label>
                    <input type="date" class="form-control" name="deadline" required>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <input type="text" class="form-control" name="status" required>
                </div>

                <!-- Hidden field for confirmation -->
                <input type="hidden" name="confirmation" id="confirmation" value="0">

                <!-- Submit button -->
                <button type="button" id="submitBtn" class="btn btn-success">Submit</button>
            </form>

            <!-- Link to return to the main page -->
            <div class="button-container">
                <a type="button" href="TeamMembersPage_2.1.php" class="btn btn-primary">Return To Tasks</a>
            </div>
        </div>
    </div>

    <!-- jQuery script for handling form submission -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#submitBtn").click(function () {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                    data: $("#taskForm").serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            // Redirect or perform any other action upon successful submission
                            alert(response.message);
                            window.location.href = "TeamMembersPage_2.1.php";
                        } else {
                            if (confirm(response.message + '\nDo you still want to add the task?')) {
                                // If confirmed, proceed to add the task
                                document.getElementById("confirmation").value = 1;
                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                                    data: $("#taskForm").serialize(),
                                    dataType: "json",
                                    success: function (response) {
                                        if (response.success) {
                                            // Redirect or perform any other action upon successful submission
                                            alert(response.message);
                                            window.location.href = "TeamMembersPage_2.1.php";
                                        }
                                    }
                                });
                            }
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
