<?php
session_start();

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$subjectName = $_SESSION['subject'];
$timeSlot = $_SESSION['time_slot'];

// Splitting subject name and subject code
$subjectParts = explode('(', $subjectName);
$subjectName = trim($subjectParts[0]);
$subjectCode = rtrim($subjectParts[1], ')');

$tableName = "studentattendance";
$date = date("d-m-Y");

$tableExists = $conn->query("SHOW TABLES LIKE '$tableName'")->num_rows > 0;

if (!$tableExists) {
    $createTableQuery = "CREATE TABLE $tableName (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        Date VARCHAR(10) NOT NULL,
        Time_Slot VARCHAR(255) NOT NULL,
        User_Name VARCHAR(255) NOT NULL,
        Student_Roll BIGINT NOT NULL,
        Student_Name VARCHAR(255) NOT NULL,
        Subject_Name VARCHAR(255) NOT NULL,
        Subject_Code VARCHAR(255) NOT NULL,
        Section VARCHAR(255) NOT NULL,
        Status VARCHAR(255) NOT NULL,
        Attendance_Count INT(6) DEFAULT 0,
        date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($createTableQuery) === TRUE) {
        echo "Attendance table created successfully.";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

$data = json_decode(file_get_contents("php://input"), true);

if (is_array($data)) {
    foreach ($data as $attendance) {
        $studentName = $attendance['studentName'];
        $studentRoll = $attendance['studentRoll'];
        $section = $attendance['section'];
        $status = $attendance['status'];

        $countQuery = "SELECT Attendance_Count FROM $tableName WHERE User_Name = '$username' AND Subject_Code = '$subjectCode' AND Student_Roll = '$studentRoll' ORDER BY id DESC LIMIT 1";
        $result = $conn->query($countQuery);
        $row = $result->fetch_assoc();
        $attendanceCount = $row ? $row['Attendance_Count'] : 0;

        if ($status === 'Present') {
            $attendanceCount++;
        }

        $sql = "INSERT INTO $tableName (Date, Time_Slot, User_Name, Student_Roll, Student_Name, Subject_Name, Subject_Code, Section, Status, Attendance_Count) 
                VALUES ('$date', '$timeSlot', '$username', '$studentRoll', '$studentName', '$subjectName', '$subjectCode', '$section', '$status', $attendanceCount)";

        if ($conn->query($sql) !== TRUE) {
            echo "Error updating data: " . $conn->error;
        }
    }
} else {
    echo "Error: JSON data is not properly decoded.";
}

$conn->close();
?>
