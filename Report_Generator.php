<?php
session_start();

if (!isset($_SESSION['username'])) {
    
    header("Location: login.html");
    exit();
}
$username = $_SESSION['username'];

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$stmt = $conn->prepare("SELECT name, email FROM re_table WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();

// Bind result variables
$stmt->bind_result($name, $email);

// Fetch the result
$stmt->fetch();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report Generator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.5s ease;
            opacity: 1;
            visibility: visible;
        }
        .container.hidden {
            opacity: 0;
            visibility: hidden;
            transform: scale(0.95);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            color: #555;
            transition: color 0.3s ease;
        }
        input[type="date"],
        input[type="text"] {
            width: calc(100% - 24px);
            padding: 10px;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
            background-color: #f8f8f8;
            transition: background-color 0.3s ease;
        }
        input[type="date"]:focus,
        input[type="text"]:focus {
            background-color: #e9ecef;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            outline: none;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 2px;
            margin-bottom: 20px;
        }
        .wrapper {
            position: relative;
            margin-bottom: 20px;
        }
        .select-btn {
            position: relative;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #f8f8f8;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .select-btn:hover {
            background-color: #e0e0e0;
        }
        .selected-subject,
        .selected-section {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #333;
        }
        .options {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            z-index: 1000;
            width: calc(100% - 30px);
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-height: 160px;
            overflow-y: auto;
            animation: slideDown 0.3s ease;
            transform-origin: top;
        }
        .options.active {
            display: block;
        }
        .option {
            padding: 8px 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .option:hover {
            background-color: #f0f0f0;
        }
        .search-bar {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .search-input {
            width: calc(100% - 20px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        #sectionOptions {
            display: none;
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            z-index: 1000;
            width: calc(100% - 30px);
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-height: 160px;
            overflow-y: auto;
            animation: slideDown 0.3s ease;
            transform-origin: top;
        }
        #sectionOptions.active {
            display: block;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: scaleY(0);
            }
            to {
                opacity: 1;
                transform: scaleY(1);
            }
        }
        .context-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: #007bff;
            color: #fff;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 650px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease, transform 0.5s ease;
        }
        .context-row.visible {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
        }
        .context-item {
            margin: 0 10px;
            font-weight: bold;
            font-size: 18px;
        }
        .context-btn {
            padding: 10px 20px;
            background-color: #fff;
            color: #007bff;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            outline: none;
            white-space: nowrap;
            width: 20%;
        }
        .context-btn:hover {
            background-color: #e0e0e0;
            color: #0056b3;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        #reportTable {
            width: 100%;
            align-items: center;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            display: none;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        #reportTable th, #reportTable td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #reportTable th {
            background-color: #007bff;
            color: white;
        }

        #reportTable td {
            background-color: #f9f9f9;
        }

        #reportTable tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }

        #reportTable tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }

        #reportTable tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        #reportTable tr:last-child td:last-child {
            border-bottom-right-radius:10px;
        }

        .download-icon {
            position: absolute;
            top: 15%; 
            left: calc(100% - 70px); 
            z-index: 1000; 
            cursor: pointer;
            transform: translateY(-50%); /* Center vertically */
            transition: opacity 0.5s ease, visibility 0.5s ease;
            opacity: 0;
            visibility: hidden;
        }

        .download-icon.visible {
            opacity: 1;
            visibility: visible;
        }

        .download-icon i {
            font-size: 24px;
            color: #63ee81; /* Adjust color as needed */
        }
        .merged-cell {
        border: 2px solid green;
        font-weight: bold;
        }

        #loader {
            display: none; /* Hidden by default */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px; /* Adjust the size as needed */
            height: 100px; /* Adjust the size as needed */
        }
        .box-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }    

        .report-box {
            padding: 10px 20px;
            margin: 0 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .report-box.active {
            background-color: #007bff;
            color: #fff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        }

        .Attendance, .classbox {
            display: none;
        }

        .Attendance.active, .classbox.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="context-row">
        <span class="context-item" id="contextStartDate">StartDate</span>
        <span class="context-item" id="contextEndDate">EndDate</span>
        <span class="context-item" id="contextSubject">SubjectName</span>
        <span class="context-item" id="contextSection">Section</span>
        <button class="context-btn" id="contextButton">Modify</button>
    </div>

    <div id="downloadIcon" class="download-icon">
        <i class="fas fa-download" title="Pdf Download"></i>
    </div>
    <div id="loader"></div>
    <table id="reportTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Student Roll</th>
                <th>Student Name</th>
                <th>Status</th>
                <th>Attendance Count</th>
            </tr>
        </thead>
        <tbody id="reportTableBody"></tbody>
    </table>

    <div class="container">
        <div class="button-container" style="display: flex; justify-content:space-between;;">
            <i class="fas fa-arrow-left back-button" style="font-size: 24px; color: rgb(242, 238, 27); cursor: pointer; margin-right: 10px;" onclick="goBack()" title="Back"></i>

            <div class="box-container">
                <div id="attendanceBox" class="report-box active">Attendance Report</div>
                <div id="classBox" class="report-box">Class Report</div>
            </div>

            <i class="fas fa-home home-button" style="font-size: 24px; color: rgb(242, 238, 27); cursor: pointer; margin-right: 10px;" onclick="goHome()" title="Home"></i>
        </div>

        <div class="Attendance active">
            <h1>Attendance Report Generator</h1>
            <form id="attendanceForm">
                <div class="form-group">
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" name="startDate" required>
                    <div id="startDateError" class="error"></div>
                </div>
                <div class="form-group">
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" name="endDate" required>
                    <div id="endDateError" class="error"></div>
                </div>
                <div class="wrapper">
                    <label for="subjectSelect">Subject:</label>
                    <div class="select-btn" id="subjectSelect">
                        <span class="selected-subject">Select Subject</span>
                    </div>
                    <ul id="subjectOptions" class="options"></ul>
                    <div id="subjectError" class="error"></div>
                </div>
                <div class="form-group">
                    <label for="section">Section:</label>
                    <div class="wrapper">
                        <div class="select-btn" id="sectionSelect">
                            <span class="selected-section">Select Section</span>
                        </div>
                        <ul id="sectionOptions" class="options">
                            <li class="option">A</li>
                            <li class="option">B</li>
                            <li class="option">C</li>
                            <li class="option">D</li>
                        </ul>
                        <div id="sectionError" class="error"></div>
                    </div>
                </div>
                <button type="submit" id="generateReportButton">Generate Report</button>
            </form>
        </div>

        <div class="classbox">
            <h1>Class Report Generator</h1>
            <form id="classForm">
                <div class="form-group">
                    <label for="classStartDate">Start Date:</label>
                    <input type="date" id="classStartDate" name="classStartDate" required>
                    <div id="classStartDateError" class="error"></div>
                </div>
                <div class="form-group">
                    <label for="classEndDate">End Date:</label>
                    <input type="date" id="classEndDate" name="classEndDate" required>
                    <div id="classEndDateError" class="error"></div>
                </div>
                <button type="submit" id="generateClassReportButton">Generate Class Report</button>
            </form>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.14/lottie.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("attendanceForm");
    const subjectOptions = document.getElementById("subjectOptions");
    const sectionOptions = document.getElementById("sectionOptions");
    const subjectSelect = document.getElementById("subjectSelect");
    const sectionSelect = document.getElementById("sectionSelect");
    const selectedSubject = subjectSelect.querySelector(".selected-subject");
    const selectedSection = sectionSelect.querySelector(".selected-section");
    const startDate = document.getElementById("startDate");
    const endDate = document.getElementById("endDate");
    const contextRow = document.querySelector(".context-row");
    const contextStartDate = document.getElementById("contextStartDate");
    const contextEndDate = document.getElementById("contextEndDate");
    const contextSubject = document.getElementById("contextSubject");
    const contextSection = document.getElementById("contextSection");
    const contextButton = document.getElementById("contextButton");
    const reportTable = document.getElementById("reportTable");
    const reportTableBody = document.getElementById("reportTableBody");
    const downloadIcon = document.getElementById('downloadIcon');
    const startDateError = document.getElementById("startDateError");
    const endDateError = document.getElementById("endDateError");
    const subjectError = document.getElementById("subjectError");
    const sectionError = document.getElementById("sectionError");
    const loader = document.getElementById('loader');

    const attendanceBox = document.getElementById('attendanceBox');
    const classBox = document.getElementById('classBox');
    const attendanceContainer = document.querySelector('.Attendance');
    const classContainer = document.querySelector('.classbox');

       attendanceBox.addEventListener('click', function() {
                if (!attendanceBox.classList.contains('active')) {
                    attendanceBox.classList.add('active');
                    classBox.classList.remove('active');
                    classContainer.classList.add('hidden');
                    setTimeout(() => {
                        classContainer.classList.remove('active');
                        attendanceContainer.classList.add('active');
                        setTimeout(() => {
                            attendanceContainer.classList.remove('hidden');
                        }, 50); // small delay to ensure the hidden class is removed after animation starts
                    }, 300); // duration of the animation
                }
            });

            classBox.addEventListener('click', function() {
                if (!classBox.classList.contains('active')) {
                    classBox.classList.add('active');
                    attendanceBox.classList.remove('active');
                    attendanceContainer.classList.add('hidden');
                    setTimeout(() => {
                        attendanceContainer.classList.remove('active');
                        classContainer.classList.add('active');
                        setTimeout(() => {
                            classContainer.classList.remove('hidden');
                        }, 50); // small delay to ensure the hidden class is removed after animation starts
                    }, 300); // duration of the animation
                }
            });

    var animation = lottie.loadAnimation({
            container: document.getElementById('loader'), 
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'Animation.json' 
        });

    if (form && subjectOptions && sectionOptions && subjectSelect && sectionSelect && selectedSubject && selectedSection) {
        const subjectSearchInput = document.createElement("input");
        subjectSearchInput.setAttribute("type", "text");
        subjectSearchInput.setAttribute("placeholder", "Search Subject");
        subjectSearchInput.classList.add("search-input");
        subjectSearchInput.addEventListener("click", function (event) {
            event.stopPropagation();
        });
        subjectSearchInput.addEventListener("input", function (event) {
            filterSubjects(event.target.value.toLowerCase());
        });

        const searchBar = document.createElement("li");
        searchBar.classList.add("search-bar");
        searchBar.appendChild(subjectSearchInput);
        subjectOptions.prepend(searchBar);

        function filterSubjects(searchTerm) {
            const options = subjectOptions.querySelectorAll(".option");
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    option.style.display = "block";
                } else {
                    option.style.display = "none";
                }
            });
        }

        function fetchSubjects() {
            console.log("Fetching subjects...");
            return fetch("get_subjects.php")
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Failed to fetch subjects");
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error("Error fetching subjects:", error.message);
                    return [];
                });
        }

        async function populateSubjectOptions() {
            try {
                const subjects = await fetchSubjects();
                console.log("Subjects:", subjects);

                subjects.forEach(subject => {
                    const option = document.createElement("li");
                    option.classList.add("option");
                    option.textContent = `${subject.name} (${subject.code})`;
                    subjectOptions.appendChild(option);
                });
            } catch (error) {
                console.error("Error populating subject options:", error.message);
            }
        }

        populateSubjectOptions();

        subjectSelect.addEventListener("click", function () {
            subjectOptions.classList.toggle("active");
            subjectSearchInput.focus();
        });

        subjectOptions.addEventListener("click", function (event) {
            if (event.target.classList.contains("option") && !event.target.classList.contains("search-bar")) {
                const selectedText = event.target.textContent;
                selectedSubject.textContent = selectedText;
                contextSubject.textContent = selectedText;
                subjectOptions.classList.remove("active");
                subjectError.textContent = "";
            }
        });

        sectionSelect.addEventListener("click", function () {
            sectionOptions.classList.toggle("active");
        });

        sectionOptions.addEventListener("click", function (event) {
            if (event.target.classList.contains("option")) {
                const selectedText = event.target.textContent;
                selectedSection.textContent = selectedText;
                contextSection.textContent = selectedText;
                sectionOptions.classList.remove("active");
                sectionError.textContent = "";
            }
        });

        document.addEventListener("click", function (event) {
            if (!event.target.closest("#subjectSelect")) {
                subjectOptions.classList.remove("active");
            }
            if (!event.target.closest("#sectionSelect")) {
                sectionOptions.classList.remove("active");
            }
        });

        document.getElementById("startDate").addEventListener("input", function () {
            contextStartDate.textContent = this.value || "StartDate";
            startDateError.textContent = "";
        });

        document.getElementById("endDate").addEventListener("input", function () {
            contextEndDate.textContent = this.value || "EndDate";
            endDateError.textContent = "";
        });

        contextButton.addEventListener("click", function () {
            contextRow.classList.remove("visible");
            downloadIcon.classList.remove('visible');
            reportTable.style.display = "none";
            document.querySelector(".container").classList.remove("hidden");
        });
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let isValid = true;

        startDateError.textContent = "";
        endDateError.textContent = "";
        subjectError.textContent = "";
        sectionError.textContent = "";

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        if (isNaN(start.getTime())) {
            startDateError.textContent = "Please enter a valid start date.";
            isValid = false;
        }

        if (isNaN(end.getTime())) {
            endDateError.textContent = "Please enter a valid end date.";
            isValid = false;
        }

        if (start > end) {
            endDateError.textContent = "End date must be after start date.";
            isValid = false;
        }

        if (selectedSubject.textContent === "Select Subject") {
            subjectError.textContent = "Please select a subject.";
            isValid = false;
        }

        if (selectedSection.textContent === "Select Section") {
            sectionError.textContent = "Please select a section.";
            isValid = false;
        }

        if (isValid) {
            contextRow.classList.add("visible");
            downloadIcon.classList.add('visible');
            document.querySelector(".container").classList.add("hidden");

            contextStartDate.textContent = formatDateToDDMMYYYY(startDate.value);
            contextEndDate.textContent = formatDateToDDMMYYYY(endDate.value);
            contextSubject.textContent = selectedSubject.textContent;
            contextSection.textContent = selectedSection.textContent;

            window.scrollTo({ top: 0, behavior: "smooth" });

            function formatDateToDDMMYYYY(dateString) {
                const [year, month, day] = dateString.split('-');
                return `${day}-${month}-${year}`;
            }

            // Extract subject code from subject text
            const subjectText = selectedSubject.textContent;
            const subjectCode = subjectText.match(/\(([^)]+)\)/)[1];
            console.log({
                startDate: formatDateToDDMMYYYY(startDate.value),
                endDate: formatDateToDDMMYYYY(endDate.value),
                subjectCode: subjectCode,
                section: selectedSection.textContent
            });

            document.getElementById('loader').style.display = 'block';
            fetch('fetch_report.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                startDate: formatDateToDDMMYYYY(startDate.value),
                endDate: formatDateToDDMMYYYY(endDate.value),
                subjectCode: subjectCode,
                section: selectedSection.textContent
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                localStorage.setItem('reportData', JSON.stringify(data));
                // Clear previous table data
                reportTableBody.innerHTML = '';

                if (data.length > 0) {
                    let prevDate = null;
                    let prevTimeSlot = null;
                    let dateCell, timeSlotCell;
                    let dateRowSpan = 0;
                    let timeSlotRowSpan = 0;

                    data.forEach((row, index) => {
                        const tr = document.createElement('tr');

                        if (row.date !== prevDate) {
                            if (dateCell) {
                                dateCell.rowSpan = dateRowSpan;
                            }
                            prevDate = row.date;
                            dateRowSpan = 1;
                            dateCell = document.createElement('td');
                            dateCell.textContent = row.date;
                            dateCell.classList.add('merged-cell');
                            dateCell.style.border = '2px solid green';
                            dateCell.style.fontWeight = 'bold';
                            tr.appendChild(dateCell);
                        } else {
                            dateRowSpan++;
                        }

                        if (row.time_Slot !== prevTimeSlot) {
                            if (timeSlotCell) {
                                timeSlotCell.rowSpan = timeSlotRowSpan;
                            }
                            prevTimeSlot = row.time_Slot;
                            timeSlotRowSpan = 1;
                            timeSlotCell = document.createElement('td');
                            timeSlotCell.textContent = row.time_Slot;
                            timeSlotCell.classList.add('merged-cell');
                            timeSlotCell.style.border = '2px solid green';
                            timeSlotCell.style.fontWeight = 'bold';
                            tr.appendChild(timeSlotCell);
                        } else {
                            timeSlotRowSpan++;
                        }

                        if (index === data.length - 1) {
                            if (dateCell) {
                                dateCell.rowSpan = dateRowSpan;
                            }
                            if (timeSlotCell) {
                                timeSlotCell.rowSpan = timeSlotRowSpan;
                            }
                        }

                        const studentIdCell = document.createElement('td');
                        studentIdCell.textContent = row.Student_Roll;
                        tr.appendChild(studentIdCell);

                        const studentNameCell = document.createElement('td');
                        studentNameCell.textContent = row.Student_Name;
                        tr.appendChild(studentNameCell);

                        const attendanceCell = document.createElement('td');
                        attendanceCell.textContent = row.Status;
                        tr.appendChild(attendanceCell);

                        const scoreCell = document.createElement('td');
                        scoreCell.textContent = row.Attendance_Count;
                        tr.appendChild(scoreCell);

                        reportTableBody.appendChild(tr);
                    });

                    reportTable.style.display = "table";
                    document.getElementById('loader').style.display = 'none';
                } else {
                    reportTable.style.display = "none";
                    alert("No data available for the selected criteria.");
                    document.getElementById('loader').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching report:', error);
                document.getElementById('loader').style.display = 'none';
                const errorRow = document.createElement('tr');
                const errorCell = document.createElement('td');
                errorCell.colSpan = 6;
                errorCell.textContent = 'Error fetching report. Please try again later.';
                errorRow.appendChild(errorCell);
                reportTableBody.appendChild(errorRow);
                reportTable.style.display = 'table';
            });
    }
  });

  downloadIcon.addEventListener('click', function () {
    document.getElementById('loader').style.display = 'block';
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');

    // Function to add the header on the first page
    const addHeader = () => {
        doc.setFontSize(20);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor('#32CD32');  // Lime green color for title
        doc.text('Attendance Report', doc.internal.pageSize.getWidth() / 2, 20, { align: 'center' });

        doc.setFontSize(12);
        doc.setTextColor('#32CD32');  // Lime green for context text
        const contextData = [
            `Start Date: ${document.getElementById("contextStartDate").textContent}`,
            `End Date: ${document.getElementById("contextEndDate").textContent}`,
            `Subject: ${document.getElementById("contextSubject").textContent}`,
            `Section: ${document.getElementById("contextSection").textContent}`,
            `Name: <?php echo $name; ?>`,
            `Email:<?php echo $email; ?>`
        ];

        let contextYPosition = 30;
        contextData.forEach((line, index) => {
            doc.text(line, 14, contextYPosition + (index * 10));
        });

        return contextYPosition + contextData.length * 10;
    };

    // Adding the header on the first page
    let contextYPosition = addHeader();

    // Prepare the table data from local storage
    const tableHeaders = ['Date', 'Time Slot', 'Student Roll', 'Student Name', 'Status', 'Attendance Count'];
    const tableData = [];
    const data = JSON.parse(localStorage.getItem('reportData'));

    if (data && data.length > 0) {
        let prevDate = null;
        let prevTimeSlot = null;
        let classCount = 0;
        const studentAttendance = {};

        data.forEach(row => {
            const rowData = [];

            // Date column
            if (row.date !== prevDate || row.time_Slot !== prevTimeSlot) {
                prevDate = row.date;
                prevTimeSlot = row.time_Slot;
                rowData.push(row.date);
                rowData.push(row.time_Slot);
                classCount++;
            } else {
                rowData.push('');
                rowData.push('');
            }

            // Other columns
            rowData.push(row.Student_Roll, row.Student_Name, row.Status, row.Attendance_Count);

            // Track the last attendance count per student
            studentAttendance[row.Student_Roll] = {
                name: row.Student_Name,
                count: row.Attendance_Count
            };

            tableData.push(rowData);
        });

        // Adding class count to the first page of the PDF
        doc.setFontSize(12);
        doc.setTextColor('#FFA500');  // Orange color for total classes
        doc.text(`Number of classes: ${classCount}`, 14, contextYPosition + 20);
        
        contextYPosition += 40;

        // Adding attendance percentage per student
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor('#000000');
        doc.text('Student Attendance Percentage', doc.internal.pageSize.getWidth() / 2, contextYPosition, { align: 'center' });

        contextYPosition += 10;
        doc.setFontSize(10);
        doc.setTextColor('#000000');

        const percentageTableHeaders = ['Roll Number', 'Name', 'Percentage'];
        const percentageTableData = Object.keys(studentAttendance).map((roll, index) => {
            const student = studentAttendance[roll];
            const percentage = ((student.count / classCount) * 100).toFixed(2);
            return [roll, student.name, `${percentage}%`];
        });

        doc.autoTable({
            startY: contextYPosition + 10,
            head: [percentageTableHeaders],
            body: percentageTableData,
            margin: { top: 10 },
            styles: { cellPadding: 3, fontSize: 10 },
            headStyles: { fillColor: '#FFA500' },  // Orange header
            bodyStyles: { fillColor: [240, 255, 240], textColor: [50, 50, 50] },  // Honeydew background with dark gray text
            alternateRowStyles: { fillColor: [255, 228, 196] },  // Bisque for alternate rows
            theme: 'striped',
        });

        contextYPosition = doc.autoTable.previous.finalY + 20;

        // Add "Attendance Data" above the table if needed
        if (contextYPosition + 10 > doc.internal.pageSize.getHeight() - 30) {
            doc.addPage();
            contextYPosition = 20;
        }
        doc.setFontSize(14);
        doc.setTextColor('#000000');
        doc.setFont('helvetica', 'bold');
        doc.text('Attendance Data', doc.internal.pageSize.getWidth() / 2, contextYPosition, { align: 'center' });
    }

    // Add the table to the PDF
    doc.autoTable({
        startY: contextYPosition + 10,
        head: [tableHeaders],
        body: tableData,
        margin: { top: 10 },
        styles: { cellPadding: 3, fontSize: 10 },
        headStyles: { fillColor: [50, 205, 50] },  // Lime green header
        bodyStyles: { fillColor: [240, 255, 240], textColor: [50, 50, 50] },  // Honeydew background with dark gray text
        alternateRowStyles: { fillColor: [144, 238, 144] },  // Light green for alternate rows
        theme: 'striped',
        didDrawCell: function (data) {
            const cell = data.cell;
            const rowIndex = data.row.index;
            const colIndex = data.column.index;
            const currentRowData = tableData[rowIndex];
            const prevRowData = tableData[rowIndex - 1];

            if (rowIndex > 0 && currentRowData && prevRowData) {
                if (colIndex === 0) { // Date column
                    const prevDate = prevRowData[0];
                    const currentDate = currentRowData[0];

                    if ((prevDate && !currentDate) || (!prevDate && currentDate) || (prevDate && currentDate && prevDate !== currentDate)) {
                        cell.styles.lineColor = [0, 128, 0]; // Green line color
                        cell.styles.lineWidth = 1;
                    }
                }

                if (colIndex === 1) { // Time Slot column
                    const prevTimeSlot = prevRowData[1];
                    const currentTimeSlot = currentRowData[1];

                    if ((prevTimeSlot && !currentTimeSlot) || (!prevTimeSlot && currentTimeSlot) || (prevTimeSlot && currentTimeSlot && prevTimeSlot !== currentTimeSlot)) {
                        cell.styles.lineColor = [0, 128, 0]; // Green line color
                        cell.styles.lineWidth = 1;
                    }
                }
            }
        },
        didDrawPage: function (data) {
            // Footer with download date and time
            const downloadDate = new Date().toLocaleString();
            doc.setFontSize(10);
            doc.setTextColor('#444444');
            doc.text(`Downloaded on: ${downloadDate}`, doc.internal.pageSize.getWidth() - 40, doc.internal.pageSize.getHeight() - 10, { align: 'right' });
        },
    });

    doc.save('attendance_report.pdf');
    document.getElementById('loader').style.display = 'none';
});



});


$(document).ready(function() {
            $('#classForm').on('submit', function(event) {
                event.preventDefault();

                const startDate = $('#classStartDate').val();
                const endDate = $('#classEndDate').val();
                let isValid = true;

                // Validate start and end dates
                if (!startDate) {
                    $('#classStartDateError').text('Start date is required');
                    isValid = false;
                } else {
                    $('#classStartDateError').text('');
                }

                if (!endDate) {
                    $('#classEndDateError').text('End date is required');
                    isValid = false;
                } else {
                    $('#classEndDateError').text('');
                }

                if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                    $('#classEndDateError').text('End date must be later than start date');
                    isValid = false;
                } else {
                    $('#classEndDateError').text('');
                }

                if (isValid) {
                    // Send the dates to the PHP script via AJAX

                    function formatDateToDDMMYYYY(dateString) {
                    const [year, month, day] = dateString.split('-');
                    return `${day}-${month}-${year}`;
                     }
                    $.ajax({
                        url: 'generate_class_report.php',
                        type: 'POST',
                        data: {
                            startDate: formatDateToDDMMYYYY(startDate),
                            endDate: formatDateToDDMMYYYY(endDate)
                        },
                        success: function(response) {
                            console.log(response);
                            const data = JSON.parse(response);
                            console.log(data); // Display data in console log
                            generatePDF(data, startDate, endDate);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error: ' + error);
                        }
                    });
                }
            });

            function generatePDF(data, startDate, endDate) {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();

                // Define colors and styles
                const headerColor = [70, 130, 180]; // SteelBlue color
                const textColor = [0, 0, 0]; // Black color
                const tableHeaderColor = [192, 192, 192]; // LightGray color
                const tableRowEvenColor = [240, 240, 240]; // Light color
                const tableRowOddColor = [255, 255, 255]; // White color

                // Heading
                doc.setFontSize(18);
                doc.setTextColor(headerColor[0], headerColor[1], headerColor[2]);
                doc.text('Class Report', pageWidth / 2, 20, { align: 'center' });

                // Date range
                doc.setFontSize(12);
                doc.setTextColor(textColor[0], textColor[1], textColor[2]);
                doc.text(`Start Date: ${startDate}`, 20, 30);
                doc.text(`End Date: ${endDate}`, pageWidth - 20, 30, { align: 'right' });
                doc.text(`Name :<?php echo $name; ?>`,20 , 40);
                doc.text(`Email:<?php echo $email; ?>`,pageWidth - 20 , 40, { align: 'right' });

                // Table headers
                const tableTop = 50;
                const cellPadding = 5;
                const rowHeight = 10;
                const colWidth = (pageWidth - 40) / 2;
                const columns = ['Subject Name (Code)', 'Number of Classes'];
                
                doc.setFontSize(10);
                doc.setFillColor(tableHeaderColor[0], tableHeaderColor[1], tableHeaderColor[2]);
                doc.rect(20, tableTop, colWidth, rowHeight, 'F');
                doc.rect(20 + colWidth, tableTop, colWidth, rowHeight, 'F');
                doc.text(columns[0], 20 + cellPadding, tableTop + rowHeight / 2 + 2.5);
                doc.text(columns[1], 20 + colWidth + cellPadding, tableTop + rowHeight / 2 + 2.5);

                // Table rows
                let startY = tableTop + rowHeight;
                let isEvenRow = true;

                data.forEach((item, index) => {
                    const rowColor = isEvenRow ? tableRowEvenColor : tableRowOddColor;
                    const subjectNameText = `${item.subjectName} (${item.subjectCode})`;
                    const classCountText = `${item.classCount}`;

                    // Wrap text if too long
                    const subjectNameLines = doc.splitTextToSize(subjectNameText, colWidth - cellPadding * 2);
                    const classCountLines = doc.splitTextToSize(classCountText, colWidth - cellPadding * 2);

                    const maxLines = Math.max(subjectNameLines.length, classCountLines.length);
                    for (let i = 0; i < maxLines; i++) {
                        if (i > 0) {
                            startY += rowHeight;
                        }

                        if (startY + rowHeight > pageHeight - 20) {
                            doc.addPage();
                            startY = 20;

                            // Table headers on new page
                            doc.setFillColor(tableHeaderColor[0], tableHeaderColor[1], tableHeaderColor[2]);
                            doc.rect(20, startY, colWidth, rowHeight, 'F');
                            doc.rect(20 + colWidth, startY, colWidth, rowHeight, 'F');
                            doc.text(columns[0], 20 + cellPadding, startY + rowHeight / 2 + 2.5);
                            doc.text(columns[1], 20 + colWidth + cellPadding, startY + rowHeight / 2 + 2.5);

                            startY += rowHeight;
                        }

                        const subjectNameLine = subjectNameLines[i] || '';
                        const classCountLine = classCountLines[i] || '';

                        doc.setFillColor(rowColor[0], rowColor[1], rowColor[2]);
                        doc.rect(20, startY, colWidth, rowHeight, 'F');
                        doc.rect(20 + colWidth, startY, colWidth, rowHeight, 'F');

                        doc.text(subjectNameLine, 20 + cellPadding, startY + rowHeight / 2 + 2.5);
                        doc.text(classCountLine, 20 + colWidth + cellPadding, startY + rowHeight / 2 + 2.5);
                    }

                    startY += rowHeight;
                    isEvenRow = !isEvenRow;
                });

                // Footer
                const today = new Date();
                const downloadDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                const downloadTime = `${String(today.getHours()).padStart(2, '0')}:${String(today.getMinutes()).padStart(2, '0')}`;
                const downloadBy = `Downloaded by: <?php echo $username; ?> on ${downloadDate} at ${downloadTime}`;

                doc.setFontSize(10);
                doc.setTextColor(textColor[0], textColor[1], textColor[2]);
                doc.text(downloadBy, 20, pageHeight - 10);

                doc.save('class_report.pdf');
            }
        });
function goBack() {
    window.history.back();
}

function goHome() {
    localStorage.clear(); // Clear localStorage
    sessionStorage.clear(); // Clear sessionStorage
    window.location.href = 'index.php'; // Navigate to home page
}

</script>
        
</body>
</html>
