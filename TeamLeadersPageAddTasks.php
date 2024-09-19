<?php
session_start();

$empNo = $_SESSION["empNo"];
$projectNo = isset($_GET['ProjectNo']) ? $_GET['ProjectNo'] : '';

$servername = "localhost";
$username = "admin";
$password = "team23";
$dbname = "MakeItAll";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskName = $_POST["taskName"];
    $description = $_POST["description"];
    $manHours = $_POST["manHours"];
    $projectNo = $_POST["projectNo"];
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];
    $confirmation = $_POST["confirmation"];

    // Check if taskMembers is set in the POST data
    if (isset($_POST['taskMembers']) && is_array($_POST['taskMembers'])) {
        // The selected employee numbers are now in an array
        $employeeNos = $_POST['taskMembers'];

        // Include the file containing the function for checking in-progress tasks
        include 'LeaderInProgressTasksChecker.php';
        $result = canAddTask($conn, $confirmation, $employeeNos);

        if ($result['allowed']) {
            try {
                $conn->begin_transaction();

                $sqlTask = "INSERT INTO Tasks (TaskName, Description, ManHours, ProjectNo, Deadline, Status, AssignedBy)
                            VALUES (?, ?, ?, ?, ?, ?, ?);";

                $stmtTask = $conn->prepare($sqlTask);
                $stmtTask->bind_param("ssiissi", $taskName, $description, $manHours, $projectNo, $deadline, $status, $empNo);
                $stmtTask->execute();
                $stmtTask->close();

                $taskNo = $conn->insert_id;

                // Prepare the SQL statement outside the loop
                $sqlAssignment = "INSERT INTO TaskAssignments (TaskNo, EmployeeNo) VALUES (?, ?)";
                $stmtAssignment = $conn->prepare($sqlAssignment);

                // Loop through each selected employee number and execute the prepared statement
                foreach ($employeeNos as $memberNo) {
                    // Bind parameters
                    $stmtAssignment->bind_param("ii", $taskNo, $memberNo);
                    // Execute the statement
                    $stmtAssignment->execute();
                }

                // Close the statement after the loop
                $stmtAssignment->close();

                $conn->commit();

                echo json_encode(["success" => true, "message" => "Task added successfully!"]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(["success" => false, "message" => "Error adding task. Please try again. " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "message" => $result['message']]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No employees selected"]);
    }

    exit; // Terminate the script after sending the JSON response
}

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
    <!-- Add additional styles here -->
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #f8f8f8;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .navbar-text img {
            display: block;
            margin: auto;
        }

        .well {
            margin-top: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            height: 100px;
            resize: vertical;
        }

        #employeeChecklist {
            margin-top: 10px;
        }

        .button-container {
            margin-top: 20px;
        }

        .styled-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            border-radius: 5px;
            color: #fff;
            background-color: #5cb85c;
            border: 1px solid #4cae4c;
        }

        .styled-button:hover {
            background-color: #4cae4c;
        }

        
    </style>
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
    <div class="well well-lg">
        <h2>Add New Task</h2>

        <!-- Task input form -->
        <form id="taskForm">
            <label for="taskName">Task Name:</label>
            <input type="text" name="taskName" required>

            <label for="description">Description:</label>
            <textarea name="description" required></textarea>

            <label for="manHours">Man Hours:</label>
            <input type="number" name="manHours" required>

            <label for="projectNo">Project Code:</label>
            <input type="text" name="projectNo" value="<?php echo $projectNo?>">

            <label for="taskMembers">Task Members:</label>
            <div id="employeeChecklist"></div>

            <label for="deadline">Deadline:</label>
            <input type="date" name="deadline" required>

            <label for="status">Status:</label>
            <input type="text" name="status" required>

            <input type="hidden" name="confirmation" id="confirmation" value="0">

            <button type="button" id="submitBtn" class="btn btn-success">Submit</button>
        </form>

        <!-- Link to return to the main page -->
        <div class="button-container">
            <?php
            $roleLink = 'teamleader.php';
            $params = '?ProjectNo=' . urlencode($projectNo) . '&Role=Leader';
            ?>
            <a href="<?php echo $roleLink . $params; ?>" class="styled-button">Return To Leader Page</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajax({
                type: "POST",
                url: "TeamLeadersPageFetchMembers.php?ProjectNo=<?php echo $projectNo; ?>",
                dataType: "json",
                success: function (response) {
                    // Populate the checklist with employee data
                    if (response.success) {
                        var employeeChecklist = $("#employeeChecklist");
                        $.each(response.employees, function (index, employee) {
                            employeeChecklist.append('<input type="checkbox" name="taskMembers[]" value=' + employee.EmployeeNo + '>' + employee.Username + '<br>');
                        });
                    } else {
                        alert("Error fetching employees. Please try again.");
                    }
                }
            });

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
                            window.location.href = "teamleader.php?ProjectNo=<?php echo urlencode($projectNo);?>&Role=Leader";
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
                                            window.location.href = "teamleader.php?ProjectNo=<?php echo urlencode($projectNo);?>&Role=Leader";
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
