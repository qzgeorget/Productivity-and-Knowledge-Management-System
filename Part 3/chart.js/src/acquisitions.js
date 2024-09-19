
const selectedProject = 'Overall';

const selectedGraph = 'sprintVsHoursWorked';

const project = {
            "projectName": "Overall",
            "sprintVsHoursWorked": {
                "sprintData": [
                    {
                    "sprintNumber": "Sprint 1",
                    "hoursWorked": 400
                    },
                    {
                    "sprintNumber": "Sprint 2",
                    "hoursWorked": 450
                    },
                    {
                    "sprintNumber": "Sprint 3",
                    "hoursWorked": 420
                    },
                    {
                    "sprintNumber": "Sprint 4",
                    "hoursWorked": 470
                    }
                ]
            },
            "sprintVsTasksCompleted": {
                "sprintData": [
                    {
                    "sprintNumber": "Sprint 1",
                    "tasksCompleted": 100
                    },
                    {
                    "sprintNumber": "Sprint 2",
                    "tasksCompleted": 120
                    },
                    {
                    "sprintNumber": "Sprint 3",
                    "tasksCompleted": 110
                    },
                    {
                    "sprintNumber": "Sprint 4",
                    "tasksCompleted": 130
                    }
                ]
            },
            "taskDistributionByTaskTypes": {
                "taskAreas": {
                    "Coding": 350,
                    "Testing": 250,
                    "Documentation": 200,
                    "Refactoring": 150,
                    "Requirement Specification": 300
                }
            },
            "taskDistributionByStatuses": {
                "taskStatus": {
                    "To Do": 250,
                    "In Progress": 200,
                    "Finished": 450
                }
            }
}

const hoursWorked = project.sprintVsHoursWorked.sprintData;
const tasksCompleted = project.sprintVsTasksCompleted;
const taskTypes = project.taskDistributionByTaskTypes;
const taskStatuses = project.taskDistributionByStatuses;

new Chart(
    document.getElementById("myChart"),
    {
        type: 'bar',
        options: {
            animation:false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled:false
                }
            }
        },
        data: {
            labels: hoursWorked.map(row => row.sprintNumber),
            datasets: [{hoursWorked: hoursWorked.map(row => row.hoursWorked)}]
        }
    }
);
