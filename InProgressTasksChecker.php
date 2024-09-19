<?php

// Function to check if it's allowed to add a new task
function canAddTask($empNo, $conn, $confirmation) {
    // Set task and hour limits
    $taskLimit = 3;  // You can set this to your desired limit
    $hourLimit = 9;

    // Count the number of tasks in progress for the employee
    $countSql = "SELECT COUNT(*) AS count 
    FROM Tasks 
    JOIN TaskAssignments ON TaskAssignments.TaskNo = Tasks.TaskNo
    WHERE TaskAssignments.EmployeeNo = ? AND Status = 'In Progress'";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("i", $empNo);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();
    $countStmt->close();

    // Calculate the total hours in progress for the employee
    $hourSql = "SELECT SUM(Subquery.IndivHour) AS totalHour
    FROM (SELECT Tasks.TaskNo AS TaskNo,(Tasks.ManHours/ COUNT(TaskAssignments.TaskNo) ) AS IndivHour 
        FROM TaskAssignments 
        JOIN Tasks ON Tasks.TaskNo = TaskAssignments.TaskNo 
        GROUP BY TaskAssignments.TaskNo) AS Subquery
    JOIN TaskAssignments ON TaskAssignments.TaskNo = Subquery.TaskNo
    JOIN Tasks ON Tasks.TaskNo = TaskAssignments.TaskNo
    WHERE TaskAssignments.EmployeeNo = ? AND Tasks.Status = 'In Progress';";
    $hourStmt = $conn->prepare($hourSql);
    $hourStmt->bind_param("i", $empNo);
    $hourStmt->execute();
    $hourResult = $hourStmt->get_result();
    $hourRow = $hourResult->fetch_assoc();
    $hourStmt->close();

    // If confirmation is provided, allow the task to be added
    if ($confirmation == 1) {
        return ['allowed' => true];
    } else {
        // Check task and hour limits
        $countpass = false;
        $hourpass = false;

        // If both task and hour limits are reached
        if (($countRow['count'] >= $taskLimit) && ($hourRow['totalHour'] >= $hourLimit)) {
            return [
                'allowed' => false,
                'message' => "You have reached the maximum limit for the number of tasks and hours at the current moment. Proceeding with this task assignment may overload you."
            ];
        } else if ($hourRow['totalHour'] >= $hourLimit) {
            // If only the hour limit is reached
            return [
                'allowed' => false,
                'message' => "You have reached the maximum limit for the number of hours at the current moment. Proceeding with this task assignment may overload you."
            ];
        } else if ($countRow['count'] >= $taskLimit) {
            // If only the task limit is reached
            return [
                'allowed' => false,
                'message' => "You have reached the maximum limit for the number of tasks at the current moment. Proceeding with this task assignment may overload you."
            ];
        } else {
            // If both limits are not reached, allow the task to be added
            return ['allowed' => true];
        }
    }
}
?>
