<?php

// Function to check if it's allowed to add a new task
function canAddTask($conn, $confirmation, $employeeNos) {
    // Set task and hour limits
    $taskLimit = 3;  // You can set this to your desired limit
    $hourLimit = 9;

    // If confirmation is provided, allow the task to be added
    if ($confirmation == 1) {
        return ['allowed' => true];
    } else {
        $countpass = true;
        $hourpass = true;
        $failEmps = array(); // Array to store employees who exceeded limits

        // Loop through each employee
        foreach ($employeeNos as $empNo) {
            // Count the number of tasks in progress for the employee
            $countSql = "SELECT COUNT(*) AS count, Employees.Username
            FROM Tasks 
            JOIN TaskAssignments ON TaskAssignments.TaskNo = Tasks.TaskNo
            JOIN Employees ON Employees.EmployeeNo = TaskAssignments.EmployeeNo
            WHERE TaskAssignments.EmployeeNo = ? AND Status = 'In Progress'
            GROUP BY Employees.Username";

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

            // Check if the employee exceeds the task count and hour limit
            if (($countRow['count'] >= $taskLimit) && ($hourRow['totalHour'] >= $hourLimit)){
                $countpass = false;
                $hourpass = false;
                $failEmps[] = $countRow['Username'];
            } else if ($countRow['count'] >= $taskLimit) {
                $countpass = false;
                $failEmps[] = $countRow['Username'];
            } else if ($hourRow['totalHour'] >= $hourLimit) {
                $hourpass = false;
                $failEmps[] = $countRow['Username'];
            }
        }

        // If both task and hour limits are exceeded for any employee
        if ((!$countpass) && (!$hourpass)) {
            return [
                'allowed' => false,
                'message' => "Employee(s):\n\n" . implode(", ", $failEmps) . "\n\nhave reached the maximum limit for the number of tasks and hours at the current moment. Proceeding with this task assignment may overload you."
            ];
        } else if (!$hourpass){
            // If only the hour limit is exceeded for any employee
            return [
                'allowed' => false,
                'message' => "Employee(s):\n\n" . implode(", ", $failEmps) . "\n\nhave reached the maximum limit for the number of hours at the current moment. Proceeding with this task assignment may overload you."
            ];
        } else if (!$countpass) {
            // If only the task limit is exceeded for any employee
            return [
                'allowed' => false,
                'message' => "Employee(s):\n\n" . implode(", ", $failEmps) . "\n\nhave reached the maximum limit for the number of tasks at the current moment. Proceeding with this task assignment may overload you."
            ];
        } else {
            // If no employee exceeds the limits, allow the task to be added
            return ['allowed' => true];
        }
    }
}
?>
