<?php 
session_start();
include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$department = $_POST['department'];
$subject = $_POST['subject'];
$timeslot= $_POST['timeslot'];
$semester = $_POST['semester'];
$section = ($_POST['section'] === 'Select Section') ? null : $_POST['section'];
$startRoll = ($_POST['startRoll'] === '') ? null : $_POST['startRoll'];
$endRoll = ($_POST['endRoll'] === '') ? null : $_POST['endRoll'];

if ($startRoll !== null && $endRoll !== null && $section !== null) {
    $sql = "SELECT * FROM students WHERE department = '$department' AND semester = '$semester' AND section = '$section' AND roll_number BETWEEN '$startRoll' AND '$endRoll'"; 
} elseif ($section !== null && ($startRoll === null || $endRoll === null)) {
    $sql = "SELECT * FROM students WHERE department = '$department'  AND semester = '$semester' AND section = '$section'";
} elseif ($startRoll !== null && $endRoll !== null && $section === null) {
    $sql = "SELECT * FROM students WHERE department = '$department'  AND semester = '$semester' AND roll_number BETWEEN '$startRoll' AND '$endRoll'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['subject'] = $subject;
    $_SESSION['time_slot'] = $timeslot;
    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    $response = array(
        'error' => 'No data found from server',
        'department' => $department,
        'subject' => $subject,
        'semester' => $semester,
        'section' => $section,
        'startRoll' => $startRoll,
        'endRoll' => $endRoll
    );
    echo json_encode($response);
}

$conn->close();
?>
