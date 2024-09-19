<?php
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
    $sql = "SELECT ProjectNo, Role FROM ProjectAssignments WHERE EmployeeNo = ?";
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
<html>
<head>
    <meta charset="UTF-8">
    <title>Profile Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body {
            height: 100vh;
            margin: 0;
            background-color: #30363d;
            overflow: hidden;
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

        .row.content {height: 550px}

        .sidenav {
          background-color: #f1f1f1;
          height: 145%;
        }
    
        .fixed-height {
            height: 300px; 
            overflow: auto; 
        }
    
        .calendar {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin: 10px;
        }
    
        .calendar-header {
            background-color: #333;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
        }
    
        .day-header {
            background-color: #666;
            color: #fff;
            font-weight: bold;
        }
    
        .milestone{
            background-color: gold;
        }
    
        td {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
        }
    
        .empty-cell {
            background-color: #f5f5f5;
        }
    
        .bullet-list li {
            margin-bottom: 5px; 
        }
    
        .fit-image {
            max-width: 80%; 
            max-height: 80%; 
            display: block;
            margin: 0 auto;
        }
    
    .content-container {
    margin-top: 140px; 
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
    .list-item, .separator {
    display: inline;
    margin-right: 5px; 
    }
    h1 {
        color: #fff;
        text-align:center;
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
    th, td {
        padding: 10px;
        border: 1px solid white; /* Border color */
    }
    .styled-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4CAF50; /* Green background color */
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .styled-button:hover {
        background-color: #45a049; /* Darker green on hover */
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
                <a href="mainforum.php" class="forum-button">Forum</a>
                <a href="manager.php" class="forum-button">Productivity</a>
                <a href="index.php" class="forum-button">Sign Out</a>
            </div>
        </nav>
    </header>

    <div class="container-fluid content-container">
        <div class="row content">
            <div class="row">
                <!-- Display employee number as the title below the navbar -->
                <h1>Welcome to MakeItAll Employee <?php echo $empNo; ?></h1>
            </div>
            <div class="row">
                <h1>Your Projects:</h1>
            </div>

            <!-- Display a table with project details -->
            <table border="1">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['ProjectNo']; ?></td>
                        <td><?php echo $row['Role']; ?></td>
                        <td>
                            <!-- Create a link based on the role -->
                            <?php
                                if ($row['Role'] == 'Leader') {
                                    $roleLink = 'teamleader.php';
                                    $params = '?ProjectNo=' . urlencode($row['ProjectNo']) . '&Role=' . urlencode($row['Role']);
                                    echo '<a href="' . $roleLink . $params . '" class="styled-button">View</a>';
                                } else {
                                    $roleLink = 'TeamMembersPage_2.0.php';
                                    echo '<a href="' . $roleLink . '" class="styled-button">View</a>';
                                }
                            ?>

                            
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>