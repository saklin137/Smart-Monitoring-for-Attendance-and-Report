<?php

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT subject_name, subject_code FROM subjects";
$result = $conn->query($sql);

$subjects = array();


if ($result->num_rows > 0) {
  
    while($row = $result->fetch_assoc()) {

        $subject = array(
            "name" => $row["subject_name"],
            "code" => $row["subject_code"]
        );

        $subjects[] = $subject;
    }
}


$conn->close();

echo json_encode($subjects);
?>
