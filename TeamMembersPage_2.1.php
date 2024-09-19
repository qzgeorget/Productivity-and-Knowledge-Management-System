<?php
// Start a new or resume an existing session
session_start();
$empNo = $_SESSION["empNo"];

// Assuming you have a database connection established
$servername = "localhost";
$username = "admin";
$password = "team23";
$dbname = "MakeItAll";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the SQL query
$sql = "SELECT 
    Tasks.TaskNo,
    Tasks.TaskName, 
    Tasks.Description, 
    Tasks.ManHours, 
    Tasks.ProjectNo, 
    Tasks.Deadline, 
    Tasks.Status,
    Employees.Name AS AssignedBy
FROM 
    Tasks
JOIN 
    TaskAssignments ON TaskAssignments.TaskNo = Tasks.TaskNo
JOIN 
    Employees ON Tasks.AssignedBy = Employees.EmployeeNo
WHERE 
    TaskAssignments.EmployeeNo = ?
ORDER BY 
    Tasks.Status DESC;";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $empNo);
$stmt->execute();

// Get the result set
$result = $stmt->get_result();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>To-Do List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="TeamStyle.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="TeamMembersPage_1.2.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    
    <style>
    body {
        background-color: #f5f5f5;
        font-family: 'Arial', sans-serif;
        margin-top: 500;
        padding: 0;
    }

    .well {
        background-color: #fff;
        margin-top: 75px;
        padding: 20px;
        border-radius: 5px;
        overflow: auto;
        height: 950px
    }

    h2 {
        color: #333;
        font-size: 28px;
    }

    .panel {
        margin-top: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .panel-heading {
        background-color: #f5f5f5;
        padding: 10px;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
    }

    .panel-body {
        padding: 15px;
    }

    .button-container {
        margin-top: 150px;
    }

    .btn {
        margin-right: 10px;
    }

    .search-container {
        border: 1px solid #ccc; 
        border-radius: 5px; 
        padding: 10px; 
        margin-top: 100px;
        margin-bottom: 20px;
    }

    #searchbar {
        border: 1px solid #ccc; 
        border-radius: 5px; 
        padding: 10px;
    }
    form {
        display: inline-block;
    }

    input[type="text"] {
        padding: 5px;
    }

    button {
        padding: 5px 10px;
        background-color: #5bc0de;
        color: #fff;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    button:hover {
        background-color: #46b8da;
    }

    .dropdown {
        display: inline-block;
        margin-right: 10px;
        margin-top: 120px;
        margin-bottom: 20px;
    }

    .dropdown-toggle {
        background-color: #5cb85c;
        color: #fff;
        border: none;
        border-radius: 3px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .dropdown-toggle:hover {
        background-color: #4cae4c;
    }

    .dropdown-menu {
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu li {
        padding: 5px;
    }

    .modal-content {
        background-color: #fff; /* Set your desired background color for the modal content */
        border-radius: 5px;
    }

    .modal-header {
        background-color: #5bc0de;
        color: #fff;
        padding: 15px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .modal-body {
        padding: 15px;
    }

    .modal-footer {
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .radio {
        margin-top: 15px;
    }

    .radio input {
        margin-right: 30px;
        verticla-align:middle;
    }

    #result {
        color: green;
    }
</style>


</head>

<body>
    <!-- Header Section -->
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

    <!-- Main Content Section -->
    <div class="well well-lg">

        <!-- Dropdown for Ordering Tasks -->
        <div class="dropdown pull-right">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Order By
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a href="#" onclick="replaceTable('Deadline', document.getElementById('searchbar').value)">Deadline</a></li>
                <li><a href="#" onclick="replaceTable('ManHours', document.getElementById('searchbar').value)">ManHour</a></li>
                <li><a href="#" onclick="replaceTable('ProjectNo', document.getElementById('searchbar').value)">Project Code</a></li>
            </ul>
        </div>

        

        <!-- Search Form -->
        <div class="search-container">
            <form action="#" onsubmit="return false;">
                <input id='searchbar' type="text" name="search">
                <button type="submit" onclick="replaceTable('Search', document.getElementById('searchbar').value)">Submit</button>
                <!-- Button to Add New Task -->
                <a type="button" href="TeamMembersPageAddTasks.php" class="btn btn-success">Add New Task</a>
            </form>
        </div>

        <h2>To-Do List</h2>

        <!-- Task Panels and Modals -->
        <div id='tasks'>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="panel panel-default" data-toggle="modal" data-target="#task<?php echo $row['TaskNo']; ?>Modal">
                    <div class="panel-heading"><?php echo $row['TaskName']; ?></div>
                    <div class="panel-body">
                        <p>Assigned By: <?php echo $row['AssignedBy']; ?></p>
                        <p>Deadline: <?php echo $row['Deadline']; ?></p>
                        <p>Man-Hours Required: <?php echo $row['ManHours']; ?> hours</p>
                        <p>Project Code: PRJ-<?php echo $row['ProjectNo']; ?></p>
                        <p>Status: <?php echo $row['Status']; ?></p>
                    </div>
                </div>

                <div class="modal fade" id="task<?php echo $row['TaskNo']; ?>Modal" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><?php echo $row['TaskName']; ?></h4>
                            </div>
                            <div class="modal-body">
                                <p>Task Description: <?php echo $row['Description']; ?></p>
                                <p>Assigned By: <?php echo $row['AssignedBy']; ?></p>
                                <p>Deadline: <?php echo $row['Deadline']; ?></p>
                                <p>Man-Hours Required: <?php echo $row['ManHours']; ?> hours</p>
                                <p>Project Code: PRJ-<?php echo $row['ProjectNo']; ?></p>

                                <!-- Radio buttons to update task status -->
                                <div class="radio">
                                    <input type="hidden" name="taskNo" value="<?php echo $row['TaskNo']; ?>">
                                    <label>
                                        <input type="radio" name="status" value="Not Started"> Not Started
                                    </label>
                                    <label>
                                        <input type="radio" name="status" value="In Progress"> In Progress
                                    </label>
                                    <label>
                                        <input type="radio" name="status" value="Finished"> Finished
                                    </label>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Button to Return to Dashboard -->
        <div class="button-container">
            <a type="button" href="TeamMembersPage_2.0.php" class="btn btn-primary">Return To Dashboard</a>
        </div>

    </div>

    <!-- JavaScript Section -->
    <script>
        // Function to fetch and replace tasks based on ordering or searching
        function replaceTable(orderBy, queryText) {
            $.ajax({
                url: 'TeamMembersPageFetchTasks.php',
                type: 'POST',
                data: {
                    orderBy: orderBy,
                    queryText: queryText
                },
                success: function (data) {
                    // Clear existing content
                    $('#tasks').empty();

                    // Append new content
                    $('#tasks').append(data);
                },
                error: function (error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        // Event handler for radio button click to update task status
        $(document).ready(function () {
            $('input[type="radio"]').click(function () {
                var selectedStatus = $(this).val();
                var taskNo = $(this).closest('.modal-content').find('input[name="taskNo"]').val(); // Retrieve taskNo from the modal

                $.ajax({
                    url: "UpdateTaskStatus.php",
                    method: "POST",
                    data: {
                        taskNo: taskNo,
                        newStatus: selectedStatus
                    },
                    success: function (data) {
                        $('#result').html(data);
                        location.reload();
                    }
                });
            });
        });
    </script>
</body>

</html>
