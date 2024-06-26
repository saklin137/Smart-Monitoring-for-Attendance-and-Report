<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$username = $_SESSION['username'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct subject codes for the given username within the date range
$sql = "SELECT DISTINCT Subject_Code 
        FROM studentattendance 
        WHERE User_Name = ? AND Date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$subjectCodes = [];
while ($row = $result->fetch_assoc()) {
    $subjectCodes[] = $row['Subject_Code'];
}

$data = [];

foreach ($subjectCodes as $subjectCode) {
    // Count the number of unique classes for each subject code
    $sql = "SELECT COUNT(DISTINCT Date, Time_Slot) AS classCount 
            FROM studentattendance 
            WHERE User_Name = ? AND Subject_Code = ? AND Date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $subjectCode, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Get the subject name for each subject code
    $sql = "SELECT subject_name 
            FROM subjects 
            WHERE subject_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $subjectCode);
    $stmt->execute();
    $resultSubject = $stmt->get_result();
    $subjectRow = $resultSubject->fetch_assoc();

    $data[] = [
        'subjectName' => $subjectRow['subject_name'],
        'subjectCode' => $subjectCode,
        'classCount' => $row['classCount']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>
