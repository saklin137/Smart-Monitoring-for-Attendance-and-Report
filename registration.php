<?php
include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("INSERT INTO re_table (username, email, password, qualification, discipline, name, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $username, $email, $password, $qualification, $discipline, $name, $phoneNumber);


$username = $_POST['username'];
$email = $_POST['email'];
$password = md5($_POST['password']);
$qualification = $_POST['qualification'];
$discipline = $_POST['discipline'];
$name = $_POST['name'];
$phoneNumber = $_POST['phoneNumber'];


if ($stmt->execute()) {
    echo "New record created successfully";
} else {
    echo "Error: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>
