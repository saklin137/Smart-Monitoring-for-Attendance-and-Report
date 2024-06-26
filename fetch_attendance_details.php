<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(array('error' => 'User not logged in.'));
    exit();
}

if (!isset($_GET['roll_number']) || !isset($_GET['section'])) {
    echo json_encode(array('error' => 'Roll number or section not provided.'));
    exit();
}

$rollNumber = $_GET['roll_number'];
$section = $_GET['section'];
$subject = $_SESSION['subject'];
$subjectParts = explode('(', $subject);
$subjectCode = rtrim($subjectParts[1], ')');

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    echo json_encode(array('error' => 'Database connection failed.'));
    exit();
}

// Fetch total attendance count for the last record of the student for the current user and subject
$sqlTotalAttendance = "SELECT Attendance_Count FROM studentattendance WHERE Student_Roll = '$rollNumber' AND Subject_Code = '$subjectCode' AND User_Name = '{$_SESSION['username']}' ORDER BY id DESC LIMIT 1";
$resultTotalAttendance = $conn->query($sqlTotalAttendance);
$totalAttendance = null;

if ($resultTotalAttendance && $resultTotalAttendance->num_rows > 0) {
    $rowTotalAttendance = $resultTotalAttendance->fetch_assoc();
    $totalAttendance = $rowTotalAttendance['Attendance_Count'];
}

// Fetch total class count
$sqlTotalClass = "
    SELECT COUNT(DISTINCT CONCAT(STR_TO_DATE(date, '%d-%m-%Y'), ' ', time_slot)) AS total_class_count 
    FROM studentattendance 
    WHERE Subject_Code = '$subjectCode' 
    AND User_Name = '{$_SESSION['username']}' 
    AND Section = '$section'
";
$resultTotalClass = $conn->query($sqlTotalClass);
$totalClassCount = 0;

if ($resultTotalClass && $resultTotalClass->num_rows > 0) {
    $rowTotalClass = $resultTotalClass->fetch_assoc();
    $totalClassCount = $rowTotalClass['total_class_count'];
}

// Calculate percentage of class attendance
$percentageAttendance = 0;
if ($totalClassCount > 0) {
    $percentageAttendance = ($totalAttendance / $totalClassCount) * 100;
}

// Close database connection
$conn->close();

// Output JSON response
echo json_encode(array('total_attendance' => $totalAttendance, 'total_class_count' => $totalClassCount, 'percentage_attendance' => $percentageAttendance));
?>
