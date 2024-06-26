<?php
session_start();

// Function to send JSON error responses
function send_json_error($message, $status_code) {
    http_response_code($status_code);
    echo json_encode(array("error" => $message));
    exit();
}

// Check if the username is set in the session
if (!isset($_SESSION['username'])) {
    send_json_error("Username not set in session", 400);
}

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check the connection
if ($conn->connect_error) {
    send_json_error("Connection failed: " . $conn->connect_error, 500);
}

// Get current username from session
$username = $_SESSION['username'];

// Check if day and timeSlot are provided
if (!isset($_POST['day']) || !isset($_POST['timeSlot'])) {
    send_json_error("Day or timeSlot not provided", 400);
}

$day = $_POST['day'];
$timeSlot = $_POST['timeSlot'];

// Prepare SQL statement to fetch subject for the current user, day, and time slot
$stmt = $conn->prepare("SELECT `$timeSlot` FROM Faculty_TimeTable WHERE username = ? AND day = ?");
if (!$stmt) {
    send_json_error("Invalid time slot", 400);
}
$stmt->bind_param("ss", $username, $day);
$stmt->execute();
$stmt->bind_result($subjectName);
$stmt->fetch();
$stmt->close();

if (!$subjectName) {
    send_json_error("Subject not found for the user, day, and time slot", 404);
}

// Extract subject code from subject name
preg_match("/\(([^)]+)\)$/", $subjectName, $matches);
$subjectCode = isset($matches[1]) ? $matches[1] : "";

if (empty($subjectCode)) {
    send_json_error("Invalid subject code format", 400);
}

// Fetch department and semester from subjects table based on subject code
$stmt = $conn->prepare("SELECT department, semester FROM subjects WHERE subject_code = ?");
$stmt->bind_param("s", $subjectCode);
$stmt->execute();
$stmt->bind_result($department, $semester);
$stmt->fetch();
$stmt->close();

if (!$department || !$semester) {
    send_json_error("Department or semester not found for the subject code", 404);
}

// Construct an array with subject details
$subjectDetails = array(
    "subjectName" => $subjectName,
    "subjectCode" => $subjectCode,
    "department" => $department,
    "semester" => $semester
);

// Send the subject details as the response
echo json_encode($subjectDetails);

// Close the database connection
$conn->close();
?>
