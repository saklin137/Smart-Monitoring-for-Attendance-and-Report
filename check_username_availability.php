<?php

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$username = $_POST['username'];


$stmt = $conn->prepare("SELECT username FROM re_table WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();


if ($stmt->num_rows > 0) {
    $response['status'] = "not available";
} else {
    $response['status'] = "available";
}

$stmt->close();
$conn->close();


header('Content-Type: application/json');
echo json_encode($response);
?>
