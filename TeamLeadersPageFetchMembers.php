<?php
// fetch_employees.php

// Get the ProjectNo from the GET parameter
$projectNo = isset($_GET['ProjectNo']) ? $_GET['ProjectNo'] : '';

// Database connection details
$servername = "localhost";
$username = "admin";
$password = "team23";
$dbname = "MakeItAll";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Your SQL query to fetch employees based on the project
$sql = "SELECT Employees.EmployeeNo, Employees.Username 
        FROM Employees 
        JOIN ProjectAssignments ON ProjectAssignments.EmployeeNo = Employees.EmployeeNo
        WHERE ProjectAssignments.ProjectNo = ?";

// Prepare and execute the SQL query with a parameter
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $projectNo);
$stmt->execute();
$result = $stmt->get_result();

$employees = array();

// Fetch data and store it in the $employees array
while ($row = $result->fetch_assoc()) {
    $employees[] = array(
        'EmployeeNo' => $row['EmployeeNo'],
        'Username' => $row['Username']
    );
}

$stmt->close();
$conn->close();

// Return the result as JSON
echo json_encode(['success' => true, 'employees' => $employees]);
?>
