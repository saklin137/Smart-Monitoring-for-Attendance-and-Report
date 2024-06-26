<?php

session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(array('error' => 'User not logged in.'));
    exit();
}

if (!isset($_GET['roll_number'])) {
    echo json_encode(array('error' => 'Roll number not provided.'));
    exit();
}

$rollNumber = $_GET['roll_number'];

// Replace this with your database connection code
include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
// Check connection
if ($conn->connect_error) {
    echo json_encode(array('error' => 'Database connection failed.'));
    exit();
}

// Fetch only parent phone number, student phone number, and parent name from the students table
$sql = "SELECT parent_phone, student_phone, parent_name FROM students WHERE roll_number = '$rollNumber'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $studentDetails = $result->fetch_assoc();
    echo json_encode($studentDetails);
} else {
    // Return null if the student is not found
    echo json_encode(array('parent_phone' => null, 'student_phone' => null, 'parent_name' => null));
}

$conn->close();
?>
