<?php
    // Start a new or resume an existing session
    session_start();

    // Get employee number from the session
    $empNo = $_SESSION["empNo"];

    // Database connection parameters
    $servername = "localhost";
    $username = "admin";
    $password = "team23";
    $dbname = "MakeItAll";

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL query to fetch tasks in progress assigned to the employee
    $sql = "SELECT 
        Tasks.TaskNo,
        Tasks.TaskName, 
        Tasks.Description, 
        Tasks.ManHours, 
        Tasks.ProjectNo, 
        Tasks.Deadline, 
        Employees.Name AS AssignedBy
    FROM 
        Tasks
    JOIN 
        TaskAssignments ON TaskAssignments.TaskNo = Tasks.TaskNo
    JOIN
        Employees ON Tasks.AssignedBy = Employees.EmployeeNo
    WHERE 
        TaskAssignments.EmployeeNo = ? AND 
        Tasks.Status = 'In Progress'
    ORDER BY 
        Tasks.Deadline;";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $empNo);
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch all tasks into an array
    $tasks = [];
    while ($task = $result->fetch_assoc()) {
        $tasks[] = $task;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Productivity Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- External Stylesheets -->
    <!--<link rel="stylesheet" type="text/css" href="TeamMembersPage_1.2.css">-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- External Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

    <!-- Calendar Initialization Script -->
    <script>
        // Initialize FullCalendar
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                selectable: true,
                selectHelper: true,
                select: function () {
                    $('#taskModal').modal('toggle');
                },
                events: [
                    // Define your events here
                    <?php foreach ($tasks as $task) : ?>
                        {
                            title: '<?php echo $task['TaskName']; ?>',
                            start: '<?php echo $task['Deadline']; ?>',
                            end: '<?php echo $task['Deadline']; ?>',
                            description: '<?php echo addslashes($task['Description']); ?>',
                            taskId: <?php echo $task['TaskNo']; ?> // Add a unique identifier for each task
                        },
                    <?php endforeach; ?>
                ]
            });
        });
    </script>

<style>
    body {
        height: 100vh;
        margin: 0;
        background-color: #30363d;
        overflow: auto;
    }

    header {
        background: #1c1f23;
        position: fixed;
        top: 0px;
        left: 0px;
        width: 100%;
        z-index: 997;
        height: 120px;
    }

    header::after {
        content: '';
        display: table;
        clear: both;
    }

    nav {
        text-align: center;
        display: inline-block;
    }

    .navbar-text {
        color: gold;
        font-size: 55px;
        text-align: center;
    }

    .content-container {
        margin-top: 140px;
        display: flex;
        justify-content: space-around;
        overflow: auto;
    }

    .todo-list,
    .calendar-container {
        width: 48%;
    }

    .todo-list {
        margin-right: 2%;
    }

    .forum-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: rgb(209, 178, 4);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        margin-right: 10px;
        margin-top: 35px;
        width: 120px;
        align-items: right;
    }

    .forum-buttons {
        float: right;
        margin-right: 20px;
        align-items: right;
    }

    h1 {
        color: #fff;
        text-align: center;
        margin-bottom: 50px;
    }

    table {
        margin-top: 50px;
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
        text-align: center;
        color: white;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid white;
        /* Border color */
    }

    .styled-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4CAF50;
        /* Green background color */
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .styled-button:hover {
        background-color: #45a049;
        /* Darker green on hover */
    }

    /* Calendar Styles */
.calendar-container {
    width: 80%;
    margin: 20px auto;
}

.fc {
    background-color: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    overflow: hidden;
}

.fc-toolbar {
    background-color: #333;
    color: #fff;
    border: none;
    border-radius: 6px 6px 0 0;
}

.fc-button {
    background-color: #333;
    color: #fff;
    border: none;
}

.fc-button:hover {
    background-color: #555;
}

.fc-header-title h2 {
    color: #fff;
    font-size: 20px;
}

.fc-day-header {
    background-color: #555;
    color: #fff;
    font-weight: bold;
}

.fc-day {
    border: 1px solid #ddd;
    background-color: #f5f5f5;
    height: 80px;
    overflow: hidden;
}

.fc-day:hover {
    background-color: #e0e0e0;
}

.fc-day-number {
    color: #333;
    font-size: 18px;
    font-weight: bold;
}

.fc-event {
    background-color: #428bca;
    color: #fff;
    border: none;
    border-radius: 3px;
    padding: 5px;
    cursor: pointer;
    margin-bottom: 5px;
}

.fc-event:hover {
    background-color: #3071a9;
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
    <div class="content-container">
        <!-- To-Do List Section -->
        <div class="col-lg-4">

            <div class="well well-lg">
                <h3>To-Do List</h3>
                <?php foreach ($tasks as $task) : ?>
                    <div class="panel panel-default" data-toggle="modal" data-target="#taskModal_<?php echo $task['TaskNo']; ?>">
                        <div class="panel-heading"><?php echo $task['TaskName']; ?></div>
                        <div class="panel-body">
                            <p>Deadline: <?php echo $task['Deadline']; ?></p>
                            <p>Man-Hours Required: <?php echo $task['ManHours']; ?> hours</p>
                            <p>Project Code: PRJ-<?php echo $task['ProjectNo']; ?></p>
                        </div>
                    </div>
                    <!-- Task Modal for Each Task -->
                    <div class="modal fade" id="taskModal_<?php echo $task['TaskNo']; ?>" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title"><?php echo $task['TaskName']; ?></h4>
                                </div>
                                <div class="modal-body">
                                    <p>Task Description: <?php echo $task['Description']; ?></p>
                                    <p>Assigned By: <?php echo $task['AssignedBy']; ?></p>
                                    <p>Deadline: <?php echo $task['Deadline']; ?></p>
                                    <p>Man-Hours Required: <?php echo $task['ManHours']; ?> hours</p>
                                    <p>Project Code: PRJ-<?php echo $task['ProjectNo']; ?></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Button to See All Tasks -->
                <div class="button-container">
                    <a type="button" href="TeamMembersPage_2.1.php" class="btn btn-primary">See All Tasks
                        <span class="badge">
                            <?php echo count($tasks); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="col-lg-8">
            <div id="calendar" class="calendar-container"></div>
        </div>
    </div>

</body>

</html>
