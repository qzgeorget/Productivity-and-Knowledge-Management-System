<?php
// Start or resume the session
session_start();

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

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the POST request
    $taskNo = $_POST['taskNo'];
    $newStatus = $_POST['newStatus'];

    // Prepare and execute the SQL query to update task status
    $updateSql = "UPDATE Tasks SET Status = ? WHERE TaskNo = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $newStatus, $taskNo);
    $updateStmt->execute();

    // Check for success and send response
    $success = $updateStmt->affected_rows > 0;
    $response = ['success' => $success];

    // Encode the response as JSON and echo it
    echo json_encode($response);
}

// Close the database connection
$conn->close();
?>
