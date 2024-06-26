<?php

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$phone_number = $_POST['phoneNumber'];


$stmt = $conn->prepare("SELECT phone_number FROM re_table WHERE phone_number = ?");
$stmt->bind_param("s", $phone_number);
$stmt->execute();
$stmt->store_result();

$response = array(); 

$response['phoneNumber'] = $phone_number; 

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
