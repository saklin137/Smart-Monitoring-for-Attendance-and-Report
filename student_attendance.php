<?php
session_start();

if (!isset($_SESSION['username'])) {
    
    header("Location: login.html");
    exit();
}


if (!isset($_SESSION['subject'])) {

    echo "Error: Subject not set in session.";
    exit();
}


$subject = $_SESSION['subject'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="student_attendance.css">
    <title>Student Attendance Sheet</title>
    <style>
        #loader {
            display: none; /* Hidden by default */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px; /* Adjust the size as needed */
            height: 100px; /* Adjust the size as needed */
        }
    
     </style>
</head>
<body>
    <div class="container">
        <div class="screen">

        <div class="button-container" style="display: flex; justify-content:space-between;;">
         <i class="fas fa-arrow-left back-button" style="font-size: 24px; color: white; cursor: pointer; margin-right: 10px;" onclick="goBack()" title="Back"></i>
         <i class="fas fa-home home-button" style="font-size: 24px; color: white; cursor: pointer; margin-right: 10px;" onclick="goHome()" title="Home"></i>
         </div>

        <h1>Student Attendance Sheet</h1>
          <div id="loader"></div>

            <div class="fade-line"></div>
            <div class="row">
                <div class="username" id="username"  style="font-size: 20px; color: #fff;">User Name</div>
                <button class="logout-btn" onclick="logout()">Log Out</button>
            </div>

            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Roll Number</th>
                        <th>Section</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendance-body">
                   
                </tbody>
            </table>

            <button type="button" onclick="submitAttendance()" class="submit">
                <span class="button-text">Submit</span>
            </button>
        </div>
    </div>       

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.14/lottie.min.js"></script>

    <script>    
        const username = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>";
        const subject = "<?php echo isset($_SESSION['subject']) ? $_SESSION['subject'] : ''; ?>";
        console.log(subject);

        localStorage.setItem('username', username);
        document.getElementById('username').innerText = username;

        window.onload = function() {
            const jsonData = JSON.parse(localStorage.getItem('jsonData'));
            

             const tableBody = document.getElementById('attendance-body');

            if (jsonData && jsonData.length > 0) {
                document.getElementById('loader').style.display = 'block';
                jsonData.forEach(student => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${student.name}</td>
                        <td class="roll-number-cell" data-roll-number="${student.roll_number}" data-section="${student.section}">${student.roll_number}</td>
                        <td>${student.section}</td>
                        <td><?php echo $subject; ?></td>
                        <td class="status-cell">
                            <label class="custom-checkbox">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
                 document.getElementById('loader').style.display = 'none';
            }

            // Add event listener to the entire table body for event delegation
            tableBody.addEventListener('click', function(event) {
                const target = event.target;
                if (target.classList.contains('roll-number-cell')) {
                    const rollNumber = target.dataset.rollNumber;
                    const section1 = target.dataset.section;
                    console.log(section1);
                    showStudentDetails(rollNumber , section1);
                }
            });
        };

        function submitAttendance() {
            const checkboxes = document.querySelectorAll('.custom-checkbox input[type="checkbox"]');
            const attendanceData = [];

            checkboxes.forEach((checkbox, index) => {
                const studentRow = checkbox.closest('tr');
                const studentName = studentRow.querySelector('td:nth-child(1)').textContent;
                const studentRoll = studentRow.querySelector('td:nth-child(2)').textContent;
                const section = studentRow.querySelector('td:nth-child(3)').textContent;
                const subject = studentRow.querySelector('td:nth-child(4)').textContent;
                const status = checkbox.checked ? 'Present' : 'Absent';

                attendanceData.push({
                    studentName: studentName,
                    studentRoll:studentRoll,
                    section: section,
                    subject: subject,
                    status: status
                });
            });

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_attendance.php', true);
            document.getElementById('loader').style.display = 'block';
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert("Attendance saved successfully");
                    window.location.href = 'get_details.php';
                    document.getElementById('loader').style.display = 'none';
                    console.log('Attendance saved successfully.');
                    localStorage.removeItem('jsonData');
                } else {
                    console.error('Error saving attendance.');
                     document.getElementById('loader').style.display = 'none';
                }
            };
            xhr.send(JSON.stringify(attendanceData));
             document.getElementById('loader').style.display = 'none';
        }

    function showStudentDetails(rollNumber, section) {
    // Get the table body
    const tableBody = document.getElementById('attendance-body');
    
    // Find the target row by iterating over all rows
    let targetRow = null;
    for (const row of tableBody.rows) {
        const rollNumberCell = row.querySelector('.roll-number-cell');
        if (rollNumberCell && rollNumberCell.textContent === rollNumber) {
            targetRow = row;
            break;
        }
    }
    
    // Check if the target row is found
    if (!targetRow) {
        console.error("Target row not found.");
        return;
    }
    
    // Check if the detailed table row is already created
    const detailedTableRow = document.getElementById(`student-table-row-${rollNumber}`);
    if (detailedTableRow) {
        // Toggle visibility of the detailed table row
        detailedTableRow.style.display = detailedTableRow.style.display === 'none' ? 'table-row' : 'none';
        return;
    }
    
    // Fetch student details (name, parent name, parent phone, student phone)
    const xhrDetails = new XMLHttpRequest();
    xhrDetails.onreadystatechange = function() {
        document.getElementById('loader').style.display = 'block';
        if (this.readyState === 4) {
            if (this.status === 200) {
                try {
                    console.log("Response from fetch_student_details.php:", this.responseText);
                    const studentData = JSON.parse(this.responseText);
                    if (!studentData.error) {
                        console.log("Student data:", studentData);
                        // Fetch total and percentage of class attendance
                        const xhrAttendance = new XMLHttpRequest();
                        xhrAttendance.onreadystatechange = function() {
                            if (this.readyState === 4) {
                                if (this.status === 200) {
                                    try {
                                        console.log("Response from fetch_attendance_details.php:", this.responseText);
                                        const attendanceData = JSON.parse(this.responseText);
                                        if (!attendanceData.error) {
                                            console.log("Attendance data:", attendanceData);
                                            // Create the detailed table row
                                            const row = document.createElement('tr');
                                            row.setAttribute('id', `student-table-${rollNumber}`);
                                            row.innerHTML = `
                                                <td colspan="5">
                                                    <table class="detailed-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Parent Name</th>
                                                                <th>Parent Phone NUmber</th>
                                                                <th>Student Phone Number</th>
                                                                <th>Total Attendance</th>
                                                                <th>Total Class</th>
                                                                <th>% of Class Attendance</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>${studentData.parent_name}</td>
                                                                <td>${studentData.parent_phone}</td>
                                                                <td>${studentData.student_phone}</td>
                                                                <td>${attendanceData.total_attendance}</td>
                                                                <td>${attendanceData.total_class_count}</td>
                                                                <td>${attendanceData.percentage_attendance.toFixed(2)}%</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            `;
                                            // Insert the detailed table row after the target row
                                            const newRow = document.createElement('tr');
                                            newRow.innerHTML = `<td colspan="5"></td>`;
                                            newRow.setAttribute('id', `student-table-row-${rollNumber}`);
                                            targetRow.parentNode.insertBefore(newRow, targetRow.nextSibling);
                                            newRow.querySelector('td').appendChild(row);
                                             document.getElementById('loader').style.display = 'none';
                                        } else {
                                            console.error("Error from fetch_attendance_details.php:", attendanceData.error);
                                            document.getElementById('loader').style.display = 'none';
                                        }
                                    } catch (error) {
                                        console.error("Error parsing JSON from fetch_attendance_details.php:", error);
                                         document.getElementById('loader').style.display = 'none';
                                    }
                                } else {
                                    console.error("XHR request to fetch_attendance_details.php failed with status:", this.status);
                                     document.getElementById('loader').style.display = 'none';
                                }
                            }
                        };
                        xhrAttendance.open('GET', `fetch_attendance_details.php?roll_number=${rollNumber}&section=${section}`, true);
                        xhrAttendance.send();
                    } else {
                        console.error("Error from fetch_student_details.php:", studentData.error);
                         document.getElementById('loader').style.display = 'none';
                    }
                } catch (error) {
                    console.error("Error parsing JSON from fetch_student_details.php:", error);
                     document.getElementById('loader').style.display = 'none';
                }
            } else {
                console.error("XHR request to fetch_student_details.php failed with status:", this.status);
                 document.getElementById('loader').style.display = 'none';
            }
        }
    };
    xhrDetails.open('GET', `fetch_student_details.php?roll_number=${rollNumber}`, true);
    xhrDetails.send();
}

function goBack() {
    window.history.back();
}

function goHome() {
    localStorage.clear(); // Clear localStorage
    sessionStorage.clear(); // Clear sessionStorage
   logout()
}





        function logout(){    
            window.location.href = 'logout.php';
        }

        window.onbeforeunload = function() {
            destroySession();
        };
    </script>
</body>
</html>

