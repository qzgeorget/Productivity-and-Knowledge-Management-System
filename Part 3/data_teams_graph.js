
function updateMainDisplay(selectedProject){

    //Separating the JSON data
    const tasksCompleted = selectedProject.sprintVsTasksCompleted.sprintData;
    const hoursWorked = selectedProject.sprintVsHoursWorked.sprintData;
    const taskTypes = selectedProject.taskDistributionByTaskTypes.taskAreas;
    const taskStatuses = selectedProject.taskDistributionByStatuses.taskStatus;

    //Project Title Generation
    const TitleBracket = document.getElementById("TitleBracket");
    TitleBracket.innerHTML = "<h3>" + selectedProject.projectName + "</h3>";

    //Task Completed vs Sprints Graph Generation
    const TaskCompletedDescription = document.getElementById("TaskCompletedDescription");
    document.getElementById("TaskCompletedTab").replaceChildren(TaskCompletedDescription);
    const TaskCompletedCanvas = document.createElement("canvas");
    TaskCompletedCanvas.id = "TaskCompletedCanvas";
    document.getElementById("TaskCompletedTab").appendChild(TaskCompletedCanvas);
    new Chart(
        document.getElementById("TaskCompletedCanvas"),
        {
            type: 'bar',
            options: {
                plugins: {
                    legend: {display: false},
                    title: {display: true, text: "Tasks Completed by Sprints"}
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Tasks Completed'
                        }
                    }
                }
            },
            data: {
                labels: tasksCompleted.map(row => row.sprintNumber),
                datasets: [{
                    data: tasksCompleted.map(row => row.tasksCompleted),
                    backgroundColor: 'rgb(13,107,213)'
                }]
            }
        }
    );
    

    //Hours Worked vs Sprints Graph Generation
    const HoursWorkedDescription = document.getElementById("HoursWorkedDescription");
    document.getElementById("HoursWorkedTab").replaceChildren(HoursWorkedDescription);
    const HoursWorkedCanvas = document.createElement("canvas");
    HoursWorkedCanvas.id = "HoursWorkedCanvas";
    document.getElementById("HoursWorkedTab").appendChild(HoursWorkedCanvas);
    new Chart(
        document.getElementById("HoursWorkedCanvas"),
        {
            type: 'bar',
            options: {
                plugins: {
                    legend: {display: false},
                    title: {display: true, text: "Hours Worked by Sprints"}
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours Worked'
                        }
                    }
                }
            },
            data: {
                labels: hoursWorked.map(row => row.sprintNumber),
                datasets: [{data: hoursWorked.map(row => row.hoursWorked),
                    backgroundColor: 'rgb(13,107,213)'
                }]
            }
        }
    );
    

    //Task Types Graph Generation
    const TaskTypeDescription = document.getElementById("TaskTypeDescription");
    document.getElementById("TaskDistribution-Type").replaceChildren(TaskTypeDescription);
    const TaskTypeCanvas = document.createElement("canvas");
    TaskTypeCanvas.id = "TaskTypeCanvas";
    document.getElementById("TaskDistribution-Type").appendChild(TaskTypeCanvas);
    new Chart(
        document.getElementById("TaskTypeCanvas"),
        {
            type: 'pie',
            options: {plugins: {
                legend: {display: true, position: 'bottom'},
                title: {
                    display: true,
                    text: "Task Distribution By Type"
                }
            }},
            data: {
                labels: Object.keys(taskTypes),
                datasets: [{data: Object.values(taskTypes)}]
            }
        }
    );
    

    //Task Status Graph Generation
    const TaskStatusDescription = document.getElementById("TaskStatusDescription");
    document.getElementById("TaskDistribution-Status").replaceChildren(TaskStatusDescription);
    const TaskStatusCanvas = document.createElement("canvas");
    TaskStatusCanvas.id = "TaskStatusCanvas";
    document.getElementById("TaskDistribution-Status").appendChild(TaskStatusCanvas);
    new Chart(
        document.getElementById("TaskStatusCanvas"),
        {
            type: 'doughnut',
            options: {plugins: {
                legend: {display: true, position: 'bottom'},
                title: {
                    display: true,
                    text: "Task Distribution By Status"
                }
            }},
            data: {
                labels: Object.keys(taskStatuses),
                datasets: [{data: Object.values(taskStatuses)}]
            }
        }
    );
    
}

// Read the content of the dummy data
fetch('DummyData.txt')
    .then(response => response.text())
    .then(text => {
        const apiData = JSON.parse(text);
        const projectList = apiData.projects;

        // Create options in the Title Card drop down to view projects
        const projectSelector = document.getElementById('ProjectSelectionDropDown');
        // Create a new option element for each project
        projectList.forEach(project => {
            const projectOption = document.createElement('option');
            projectOption.value = project.projectName;
            projectOption.textContent = project.projectName; 
            projectSelector.appendChild(projectOption);
        });

        projectSelector.addEventListener("change", function(event) {
            let selectedProject;
            projectList.forEach(project => {
                if (project.projectName == event.target.value){
                    selectedProject = project;
                }
            });
            updateMainDisplay(selectedProject);
        });
    })
    .catch(error => console.error('Error fetching or parsing data:', error));








//Chart tabs
$(function() {
    // Initialize outer tabs
    $("#GraphTabs").tabs();
});

//Drop down tabs
$(document).ready(function() {
    // Show/hide dropdown content on click
    $(".dropdown-toggle").click(function() {
        $(".dropdown-content").toggle();
    });
    
    // Hide dropdown content when clicking outside of it
    $(document).click(function(event) {
        if (!$(event.target).closest(".dropdown").length) {
        $(".dropdown-content").hide();
        }
    });
    
    // Show corresponding tab content when clicking a dropdown link
    $(".dropdown-content a").click(function(event) {
        event.preventDefault();
        var tabId = $(this).attr("href");
        $(".tab-content").hide();
        $(tabId).show();
    });
});