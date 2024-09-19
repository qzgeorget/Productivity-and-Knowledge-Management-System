function getAllMembers(projectID) {
    fetch(`/api/data/members?projectID=${projectID}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            'Authorization': localStorage.getItem('jwt')
        }
    })
    .then(response => response.json())
    .then(results => {
        return results;
    })
    .catch(err => {
        //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
        alert(err);
    });
}

function getMember(userID) {
    fetch(`/api/data/users?userID=${userID}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            'Authorization': localStorage.getItem('jwt')
        }
    })
    .then(response => response.json())
    .then(results => {
        return results;
    })
    .catch(err => {
        //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
        alert(err);
    });
}

function createPieChart(projectID) {
    //create pieChart - need to track all categories for tasks in all sprints for project - everything else is irrelevant
    fetch(`/api/data/categories?projectID=${projectID}`, {
        method: "GET",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            'Authorization': localStorage.getItem('jwt')
        }
    })
    .then(response => response.json())
    .then(results => {
        if (results["status"] == 200) {
            //edit chart!!!!!!!!!!!!
            console.log(results["data"]);
            const categoryNames = [];
            const taskNums = [];
            for (category in results["data"]) {
                categoryNames.push(category[0]);
                taskNums.push(category[1]);
            }
            const pieChart = new Chart("pieChart", {
                type: "pie",
                data: {
                    labels: categoryNames,
                    datasets: [{
                        label: 'Task Category Distribution',
                        data: taskNums,
                        /*backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)'
                        ],*/
                        hoverOffset: 4
                    }]
                }
            });
        } else {
            alert(results["status"] + " " + results["status_message"]);
        }
    })
    .catch(err => {
        //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
        alert(err);
    });
}

//for overview, memberData = getAllMembers(projectID)
//for specific user barchart, memberData = fetch(`/api/data/users?userID=${userID}`)
function createDoughnutChart(projectID, memberData) {
    //are we still doing lineGraph??!!!!!!!!!!!!!!!
    //create overview doughnut chart - need to track todo, doing, done tasks for all members in all sprints for project - sprints are irrelevant
    //or create doughnut chart - need to track todo, doing, done tasks for 1 member in all sprints for project - all other members are irrelevant
    const tasks = [0, 0, 0];//array: {todo, doing, done}
    
    memberData.then(results => {
        if (results["status"] == 200) {
            for (let i=0; i < results["data"].length; i++) {
                //fetch all tasks for each team member in that project
                fetch(`/api/data/tasks?userID=${results["data"][i][0]}&projectID=${projectID}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        'Authorization': localStorage.getItem('jwt')
                    }
                })
                .then(response => response.json())
                .then(taskResults => {
                    if (taskResults["status"] == 200) {
                        for (let j=0; j < taskResults["data"].length; j++) {
                            //1 for todo, 2 for doing, 3 for done?!!!!!!!!!!!!!!
                            if (taskResults["data"][j][8] == 1) {
                                tasks[0]++;
                            } else if (taskResults["data"][j][8] == 2) {
                                tasks[1]++;
                            } else {
                                tasks[2]++;
                            }
                        }
                        console.log(tasks);
                        const doughnutChart = new Chart("doughnutChart", {
                            type: "doughnut",
                            data: {
                                labels: ["To Do", "Doing", "Done"],
                                datasets: [{
                                    label: 'Task Status Distribution',
                                    data: tasks,
                                    /*backgroundColor: [
                                        'rgb(255, 99, 132)',
                                        'rgb(54, 162, 235)',
                                        'rgb(255, 205, 86)'
                                    ],*/
                                    hoverOffset: 4
                                }]
                            }
                        });
                    } else {
                        alert(taskResults["status"] + " " + taskResults["status_message"]);
                    }
                })
                .catch(err => {
                    //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
                    alert(err);
                })
            }
        } else {
            alert(results["status"] + " " + results["status_message"]);
        }
    })
}

/*
function createTable(projectID) {
    //are we still doing lineGraph??!!!!!!!!!!!!!!!
    //create table - need to track todo, doing, done tasks for each member in all sprints for project - sprints are irrelevant
    const members = [[]];//array: {{firstName, secondName}}
    const tasks = [[]];//array: {{todo, doing, done}}, separate inner array for each member
    
    const memberData = getAllMembers(projectID)
    memberData.then(results => {
        if (results["status"] == 200) {
            for (let i=0; i < results["data"].length; i++) {
                //fetch all tasks for each team member in that project
                members.push([results["data"][i][1], results["data"][i][2]])
                fetch(`/api/data/tasks?userID=${results["data"][i][0]}&projectID=${projectID}`, {
                    method: "GET",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" }
                })
                .then(response => response.json())
                .then(taskResults => {
                    if (taskResults["status"] == 200) {
                        tasks.push([0, 0, 0])//make sure this is pushing to correct index!!!!!!!!!!!!
                        for (let j=0; j < taskResults["data"].length; j++) {
                            //1 for todo, 2 for doing, 3 for done?!!!!!!!!!!!!!!
                            if (taskResults["data"][j][8] == 1) {
                                tasks[i][0]++;
                            } else if (taskResults["data"][j][8] == 2) {
                                tasks[i][1]++;
                            } else {
                                tasks[i][2]++;
                            }
                        }
                        console.log(members);
                        console.log(tasks);
                        for (let j=0; j < members.length; j++) {
                            const newRow = document.createElement("tr");
                            const newHeader = document.createElement("th");
                            newHeader.scope = "row";
                            newHeader.innerText = members[j][0] + " " + members[j][1];
                            newRow.appendChild(newHeader);
                            const newToDo = document.createElement("td");
                            newToDo.innerText = tasks[j][0];
                            newRow.appendChild(newToDo);
                            const newDoing = document.createElement("td");
                            newDoing.innerText = tasks[j][1];
                            newRow.appendChild(newDoing);
                            const newDone = document.createElement("td");
                            newDone.innerText = tasks[j][2];
                            newRow.appendChild(newDone);
                            const newTotal = document.createElement("td");
                            newTotal.innerText = String(tasks[j][0] + tasks[j][1] + tasks[j][2]);
                            newRow.appendChild(newTotal);
                            $("tbody").append(newRow);
                        }
                    } else {
                        alert(taskResults["status"] + " " + taskResults["status_message"]);
                    }
                })
                .catch(err => {
                    //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
                    alert(err);
                })
            }
        } else {
            alert(results["status"] + " " + results["status_message"]);
        }
    })
}
*/

//for overview, memberData = getAllMembers(projectID)
//for specific user barchart, memberData = fetch(`/api/data/users?userID=${userID}`)
function createBarChart(projectID, memberData) {
    //create overview barChart - need to track todo, doing, done tasks for each sprint for all members in project - members are irrelevant
    //or create barChart - need to track todo, doing, done tasks for each sprint for 1 member in project - all other members are irrelevant
    const sprints = {"1": [0, 0, 0]};//dictionary, key is is sprint nums, values are {todo, doing, done} tasks for each sprint
    memberData.then(results => {
        if (results["status"] == 200) {
            for (let i=0; i < results["data"].length; i++) {
                //fetch all tasks for each team member in that project
                fetch(`/api/data/tasks?userID=${results["data"][i][0]}&projectID=${projectID}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        'Authorization': localStorage.getItem('jwt')
                    }
                })
                .then(response => response.json())
                .then(taskResults => {
                    if (taskResults["status"] == 200) {
                        for (let j=0; j < taskResults["data"].length; j++) {
                            //hasOwn() may or may not work!!!!!!!!!!!!!!!!
                            let sprintNum = taskResults["data"][j][9]
                            if (Object.hasOwn(sprints, sprintNum)) {
                                //1 for todo, 2 for doing, 3 for done
                                if (taskResults["data"][j][8] == 1) {
                                    sprints[sprintNum][0] = sprints[sprintNum][0] + 1
                                } else if (taskResults["data"][j][8] == 2) {
                                    sprints[sprintNum][1] = sprints[sprintNum][1] + 1
                                } else {
                                    sprints[sprintNum][2] = sprints[sprintNum][2] + 1
                                }
                            } else {
                                //1 for todo, 2 for doing, 3 for done
                                if (taskResults["data"][j][8] == 1) {
                                    sprints[sprintNum] = [sprints[sprintNum][0] + 1, 0, 0]
                                } else if (taskResults["data"][j][8] == 2) {
                                    sprints[sprintNum] = [0, sprints[sprintNum][1] + 1, 0]
                                } else {
                                    sprints[sprintNum] = [0, 0, sprints[sprintNum][2] + 1]
                                }
                            }
                        }
                        const toDoTasks = [];
                        toDoTasks.length = (Object.keys(sprints)).length;
                        const doingTasks = [];
                        doingTasks.length = (Object.keys(sprints)).length;
                        const doneTasks = [];
                        doneTasks.length = (Object.keys(sprints)).length;
                        for (key in Object.keys(sprints)) {
                            toDoTasks[key - 1] = sprints[key][0];
                            doingTasks[key - 1] = sprints[key][1];
                            doneTasks[key - 1] = sprints[key][2];
                        }
                        //edit barChart!!!!!!!!!!!!!
                        const barChart = new Chart("tasksBarChart", {
                            type: 'bar',
                            data: {
                                labels: Object.keys(sprints),
                                datasets: [
                                    {
                                        label: "To Do",
                                        backgroundColor: "red",
                                        data: toDoTasks
                                    },
                                    {
                                        label: "Doing",
                                        backgroundColor: "green",
                                        data: doingTasks
                                    },
                                    {
                                        label: "Done",
                                        backgroundColor: "blue",
                                        data: doneTasks
                                    }
                                ]
                            },
                            options: {
                                scales: {
                                    xAxes: [{
                                        stacked: true
                                    }],
                                    yAxes: [{
                                        stacked: true
                                    }]
                                }
                            }
                        });
                    } else {
                        alert(taskResults["status"] + " " + taskResults["status_message"]);
                    }
                })
                .catch(err => {
                    //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
                    alert(err);
                })
            }
        } else {
            alert(results["status"] + " " + results["status_message"]);
        }
    })
}

//for overview, memberData = getAllMembers(projectID)
//for specific user barchart, memberData = fetch(`/api/data/users?userID=${userID}`)
function createBarChartHours(projectID, memberData) {
    //create overview barChart - need to track current hours for each task in each sprint for all members in project - members are irrelevant
    //or create barChart - need to current hours for each task in each sprint for 1 member in project - all other members are irrelevant
    const sprints = {"1": 0};//dictionary, key is is sprint nums, values is number of hours spent on tasks for each sprint
    memberData.then(results => {
        if (results["status"] == 200) {
            for (let i=0; i < results["data"].length; i++) {
                //fetch all tasks for each team member in that project
                fetch(`/api/data/tasks?userID=${results["data"][i][0]}&projectID=${projectID}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        'Authorization': localStorage.getItem('jwt')
                    }
                })
                .then(response => response.json())
                .then(taskResults => {
                    if (taskResults["status"] == 200) {
                        for (let j=0; j < taskResults["data"].length; j++) {
                            sprints[taskResults["data"][j][9]] = sprints[taskResults["data"][j][9]] + taskResults["data"][j][7];
                        }
                        //edit barChart!!!!!!!!!!!!!
                        const barChart = new Chart("hoursBarChart", {
                            type: 'bar',
                            data: {
                                labels: Object.keys(sprints),
                                datasets: [{
                                    label: 'Hours Worked',
                                    data: Object.values(sprints),
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    } else {
                        alert(taskResults["status"] + " " + taskResults["status_message"]);
                    }
                })
                .catch(err => {
                    //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
                    alert(err);
                })
            }
        } else {
            alert(results["status"] + " " + results["status_message"]);
        }
    })
}

$(document).ready(async function() {
    let projectID;
    let currentToken = localStorage.getItem("jwt");
    //if user is manager, get project info for all projects!!!!!!!!
    //may need to make new api call to list all projects as currently it will just list user's projects!!!!!!!!!!!!!
    //if user is only project leader, check how many projects user is leader of
    projectID = await fetch("/api/data/project_leaders", {
        method: "GET",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            'Authorization': localStorage.getItem('jwt')
        }
    })
    .then(response => response.json())
    .then(results => {
        if (results["status"] == 200) {
            if (results["data"].length == 0) {
                //show empty message!!!!!!!!!
            } else if (results["data"].length > 1) {
                //populate and show dropdown list with user's projects
                $("#ProjectSelectionDropDown").show();
                $("#ProjectSelectionDropDown option")[0].value = results["data"][0][0];
                $("#ProjectSelectionDropDown option")[0].text(results["data"][0][1])
                for (let i=1; i < results["data"].length; i++) {
                    const projectOption = document.createElement("option");
                    projectOption.value = results["data"][i][0];
                    projectOption.innerText = results["data"][i][1];
                    $("#ProjectSelectionDropDown").append(projectOption);
                }
            } else {
                //show project title
                $(".projectTitle").attr("id", results["data"][0][2]);
                $(".projectTitle").val(results["data"][0][1]);
                $("#ProjectSelectionDropDown").hide();
            }
            //manually select 1st project in dropdown - check this works!!!!!!!!!!!!!
            $("#ProjectSelectionDropDown").val(results["data"][0][0]);
            //return projectID for currently selected project
            return results["data"][0][0];
        } else {
            alert(results["status"] + " " + results["status_message"]);
        }
    })
    .catch(err => {
        //DO PROPER HTTP ERROR MESSAGE!!!!!!!!!!!!
        alert(err);
    });
    projectID.then(result => {
        if (result != null) {
            //load graphs using project id
            createPieChart(result);
            let memberData = getAllMembers(result);
            createDoughnutChart(result, memberData);
            //load both bar charts as they are on separate tabs
            createBarChart(result, memberData);
            createBarChartHours(result, memberData);
            //populate member select bars
            memberData.then(results => {
                if (results["status"] == 200) {
                    for (let i=0; i < results["data"].length; i++) {
                        const memberOption = document.createElement("option");
                        memberOption.value = results["data"][i][0];
                        memberOption.innerText = `${results["data"][i][1]} ${results["data"][i][2]}`;
                        $("#projectMemberSelectBar").append(memberOption);
                        $("#projectMemberSelectDoughnut").append(memberOption);
                    }
                } else {
                    alert(results["status"] + " " + results["status_message"]);
                }
            });
        }
    });

    //apply event handlers

    //user can choose from dropdown list what project they want to see
    $("#ProjectSelectionDropDown").on("change", function() {
        //load graphs for newly selected project
        let projectID = $("#ProjectSelectionDropDown").val();
        createPieChart(projectID);
        let memberData = getAllMembers(projectID);
        createDoughnutChart(projectID, memberData);
        //load both bar charts as they are on separate tabs
        createBarChart(projectID, memberData);
        createBarChartHours(projectID, memberData);
        //clear and repopulate member select bars
        memberData.then(results => {
            if (results["status"] == 200) {
                $("#projectMemberSelectBar").empty();
                $("#projectMemberSelectDoughnut").empty()
                const overviewOption = document.createElement("option");
                overviewOption.value = "overview";
                overviewOption.innerText = "Overview";
                $("#projectMemberSelectBar").append(overviewOption);
                $("#projectMemberSelectDoughnut").append(overviewOption);
                for (let i=0; i < results["data"].length; i++) {
                    const memberOption = document.createElement("option");
                    memberOption.value = results["data"][i][0];
                    memberOption.innerText = `${results["data"][i][1]} ${results["data"][i][2]}`;
                    $("#projectMemberSelectBar").append(memberOption);
                    $("#projectMemberSelectDoughnut").append(memberOption);
                }
            } else {
                alert(results["status"] + " " + results["status_message"]);
            }
        });
    });
    //dropdown list to select which member's data is being shown for bar chart
    $("#projectMemberSelectBar").on("change", function() {
        let projectID = $("#ProjectSelectionDropDown").val();
        if ($("#projectMemberSelectBar").val() == "overview") {
            //load overview bar charts
            let memberData = getAllMembers(projectID);
            createBarChart(projectID, memberData);
            createBarChartHours(projectID, memberData);
        } else {
            //load member specific bar chart
            let memberData = getMember($("#projectMemberSelectBar").val());
            createBarChart(projectID, memberData);
            createBarChartHours(projectID, memberData);
        }
    });
    //dropdown list to select which member's data is being shown for doughnut chart
    $("#projectMemberSelectDoughnut").on("change", function() {
        let projectID = $("#ProjectSelectionDropDown").val();
        if ($("#projectMemberSelectDoughnut").val() == "overview") {
            //load overview doughnut chart
            let memberData = getAllMembers(projectID);
            createDoughnutChart(projectID, memberData);
        } else {
            //load member specific doughnut chart
            let memberData = getMember($("#projectMemberSelectDoughnut").val());
            createDoughnutChart(projectID, memberData);
        }
    })
    //should there be dropdown list to select which member's data is being shown for pie chart??!!!!!!!!!
})